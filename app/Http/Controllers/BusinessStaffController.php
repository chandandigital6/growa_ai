<?php

namespace App\Http\Controllers;

use App\Models\Business;
use App\Models\BusinessUser;
use App\Models\User;
use App\Services\BusinessAccessService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;

class BusinessStaffController extends Controller
{
    private function authorizeManager(Business $business): void
    {
        $user = auth()->user();

        abort_unless($user, 401);

        abort_unless(
            app(BusinessAccessService::class)->canManageStaff($user, $business),
            403,
            'Only the active owner of an approved business can manage staff.'
        );
    }

    private function roles(): array
    {
        return [
            'admin' => 'Business Admin',
            'manager' => 'Manager',
            'staff' => 'Staff',
            'marketing' => 'Marketing',
            'sales' => 'Sales',
        ];
    }

    private function permissions()
    {
        // Only business-module permissions can be assigned.
        $names = [
            'business.view',
            'products.view',
            'products.create',
            'products.update',
            'products.delete',
            'services.view',
            'services.create',
            'services.update',
            'services.delete',
            'orders.view',
            'orders.manage',
            'customers.view',
            'customers.manage',
            'staff.view',
            'reviews.view',
            'reviews.manage',
            'offers.view',
            'offers.manage',
            'analytics.view',
            'social.view',
            'social.manage',
            'ai.use',
            'subscriptions.view',
            'gallery.view',
            'gallery.manage',
            'business.hours.manage',
            'business.social.manage',
        ];

        return Permission::query()
            ->where('guard_name', 'web')
            ->whereIn('name', $names)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function index(Business $business)
    {
        $this->authorizeManager($business);

        $staff = $business->businessUsers()
            ->with(['user:id,name,email,phone', 'permissions'])
            ->orderByRaw("CASE WHEN role = 'owner' THEN 0 ELSE 1 END")
            ->latest('id')
            ->paginate(15);

        $roles = $this->roles();
        $permissions = $this->permissions();

        return view(
            'businesses.staff.index',
            compact('business', 'staff', 'roles', 'permissions')
        );
    }

    public function store(Request $request, Business $business)
    {
        $this->authorizeManager($business);

        $data = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'role' => [
                'required',
                Rule::in(array_keys($this->roles())),
            ],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'distinct'],
        ]);

        $user = User::query()
            ->where('email', $data['email'])
            ->where('status', 'active')
            ->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => 'Active registered user not found.',
            ]);
        }

        if ((int) $business->owner_id === (int) $user->id) {
            throw ValidationException::withMessages([
                'email' => 'Business owner already has owner access.',
            ]);
        }

        $exists = BusinessUser::query()
            ->where('business_id', $business->id)
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            throw ValidationException::withMessages([
                'email' => 'This user is already a member.',
            ]);
        }

        $permissionIds = $this->validatePermissionIds(
            $data['permissions'] ?? []
        );

        DB::transaction(function () use (
            $business, $user, $data, $permissionIds
        ) {
            \App\Models\Business::whereKey($business->id)->lockForUpdate()->firstOrFail();
            app(\App\Services\GrowyaPlanLimitService::class)->assertCanAdd($business, 'max_staff');
            $member = $business->businessUsers()->create([
                'user_id' => $user->id,
                'role' => $data['role'],
                'is_active' => true,
                'joined_at' => now(),
            ]);

            $member->permissions()->sync($permissionIds);
        });

        return redirect()
            ->route('businesses.staff.index', $business)
            ->with('success', 'Staff added successfully.');
    }

    public function update(
        Request $request,
        Business $business,
        BusinessUser $member
    ) {
        $this->authorizeManager($business);
        $this->checkMember($business, $member);

        $data = $request->validate([
            'role' => [
                'required',
                Rule::in(array_keys($this->roles())),
            ],
            'is_active' => ['required', 'boolean'],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['integer', 'distinct'],
        ]);

        $permissionIds = $this->validatePermissionIds(
            $data['permissions'] ?? []
        );

        DB::transaction(function () use (
            $member, $data, $permissionIds, $business
        ) {
            $lockedBusiness = Business::query()->whereKey($business->id)->lockForUpdate()->firstOrFail();
            $lockedMember = BusinessUser::query()->whereKey($member->id)->lockForUpdate()->firstOrFail();
            if (!$lockedMember->is_active && (bool) $data['is_active']) {
                app(\App\Services\GrowyaPlanLimitService::class)->assertCanAdd($lockedBusiness, 'max_staff');
            }
            $member->update([
                'role' => $data['role'],
                'is_active' => (bool) $data['is_active'],
            ]);

            $member->permissions()->sync($permissionIds);
        });

        return back()->with(
            'success',
            'Staff updated successfully.'
        );
    }

    public function destroy(
        Business $business,
        BusinessUser $member
    ) {
        $this->authorizeManager($business);
        $this->checkMember($business, $member);

        $member->delete();

        return redirect()
            ->route('businesses.staff.index', $business)
            ->with('success', 'Staff removed successfully.');
    }

    private function checkMember(
        Business $business,
        BusinessUser $member
    ): void {
        abort_unless(
            (int) $member->business_id === (int) $business->id,
            404
        );

        // Business owner can never be managed here.
        abort_if(
            (int) $member->user_id === (int) $business->owner_id
            || $member->role === 'owner',
            403,
            'Business owner cannot be modified here.'
        );
    }

    private function validatePermissionIds(array $ids): array
    {
        $ids = array_values(array_unique(
            array_map('intval', $ids)
        ));

        $allowed = $this->permissions()
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (array_diff($ids, $allowed)) {
            throw ValidationException::withMessages([
                'permissions' => 'Invalid business permission selected.',
            ]);
        }

        return $ids;
    }
}
