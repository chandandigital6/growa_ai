
<x-layouts::app :title="'Staff - '.$business->name">

<div class="mx-auto max-w-7xl space-y-6 p-4 md:p-6">

    {{-- Header --}}
    <div class="flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold">
                Business Staff Management
            </h1>

            <p class="mt-1 text-sm text-zinc-500">
                {{ $business->name }}
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('businesses.staff.history', $business) }}" class="rounded-xl border border-zinc-300 px-4 py-2 text-sm dark:border-zinc-700">Staff Change History</a>
        <a href="{{ route('businesses.show', $business) }}"
           class="rounded-xl border border-zinc-300 px-4 py-2 text-sm dark:border-zinc-700">
            Back to Business
        </a>
        </div>
    </div>

    @if(session('success'))
        <div class="rounded-xl bg-green-50 p-4 text-sm text-green-700 dark:bg-green-950 dark:text-green-200">
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 dark:bg-red-950 dark:text-red-200">
            <ul class="list-disc pl-5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Add Staff --}}
    <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">

        <h2 class="mb-4 text-lg font-semibold">
            Add New Staff
        </h2>

        <form method="POST"
              action="{{ route('businesses.staff.store', $business) }}"
              class="space-y-5">
            @csrf

            <div class="grid gap-4 md:grid-cols-2">

                <label class="space-y-1">
                    <span class="text-sm font-medium">
                        Registered User Email *
                    </span>

                    <input type="email"
                           name="email"
                           required
                           value="{{ old('email') }}"
                           placeholder="staff@example.com"
                           class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 dark:border-zinc-700 dark:bg-zinc-800">
                </label>

                <label class="space-y-1">
                    <span class="text-sm font-medium">
                        Business Role *
                    </span>

                    <select name="role"
                            required
                            class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2.5 dark:border-zinc-700 dark:bg-zinc-800">

                        @foreach($roles as $key => $label)
                            <option value="{{ $key }}"
                                @selected(old('role', 'staff') === $key)>
                                {{ $label }}
                            </option>
                        @endforeach

                    </select>
                </label>
            </div>

            <div>
                <h3 class="mb-3 font-semibold">
                    Assign Staff Permissions
                </h3>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach($permissions as $permission)
                        <label class="flex items-center gap-2 rounded-xl border border-zinc-200 p-3 text-sm dark:border-zinc-700">

                            <input
                                type="checkbox"
                                name="permissions[]"
                                value="{{ $permission->id }}"
                                @checked(in_array($permission->id, old('permissions', [])))
                                class="rounded border-zinc-300"
                            >

                            <span>{{ $permission->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>

            <button type="submit"
                    class="rounded-xl bg-zinc-950 px-6 py-2.5 font-semibold text-white dark:bg-white dark:text-zinc-950">
                Add Staff
            </button>
        </form>
    </div>

    {{-- Staff Listing --}}
    <div class="space-y-4">

        <h2 class="text-lg font-semibold">
            Business Team Members
            <span class="text-sm font-normal text-zinc-500">
                ({{ $staff->total() }})
            </span>
        </h2>

        @forelse($staff as $member)

            @php
                $isOwner = $member->role === 'owner'
                    || (int) $member->user_id === (int) $business->owner_id;

                $assigned = $member->permissions
                    ->pluck('id')
                    ->map(fn ($id) => (int) $id)
                    ->all();
            @endphp

            <div class="rounded-2xl border border-zinc-200 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">

                <div class="mb-5 flex flex-wrap items-center justify-between gap-3">

                    <div>
                        <h3 class="text-lg font-semibold">
                            {{ $member->user?->name ?? 'Unknown User' }}
                        </h3>

                        <p class="text-sm text-zinc-500">
                            {{ $member->user?->email }}
                        </p>

                        <p class="mt-1 text-xs text-zinc-500">
                            {{ ucfirst($member->role) }}
                            ·
                            {{ $member->is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>

                    @if($isOwner)
                        <span class="rounded-full bg-zinc-100 px-3 py-1 text-xs font-medium text-zinc-700 dark:bg-zinc-800 dark:text-zinc-200">
                            Business Owner
                        </span>
                    @endif

                </div>

                @if(!$isOwner)

                    <form method="POST"
                          action="{{ route('businesses.staff.update', [$business, $member]) }}"
                          class="space-y-4">

                        @csrf
                        @method('PUT')

                        <div class="grid gap-4 md:grid-cols-2">

                            <label class="space-y-1">
                                <span class="text-sm font-medium">
                                    Role
                                </span>

                                <select name="role"
                                        class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">

                                    @foreach($roles as $key => $label)
                                        <option value="{{ $key }}"
                                            @selected($member->role === $key)>
                                            {{ $label }}
                                        </option>
                                    @endforeach
                                </select>
                            </label>

                            <label class="space-y-1">
                                <span class="text-sm font-medium">
                                    Status
                                </span>

                                <select name="is_active"
                                        class="w-full rounded-xl border border-zinc-300 bg-white px-3 py-2 dark:border-zinc-700 dark:bg-zinc-800">

                                    <option value="1" @selected($member->is_active)>
                                        Active
                                    </option>

                                    <option value="0" @selected(!$member->is_active)>
                                        Inactive
                                    </option>
                                </select>
                            </label>

                        </div>

                        <div>
                            <h4 class="mb-3 text-sm font-semibold">
                                Permissions
                            </h4>

                            <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">

                                @foreach($permissions as $permission)

                                    <label class="flex items-center gap-2 rounded-xl border border-zinc-200 p-3 text-sm dark:border-zinc-700">

                                        <input
                                            type="checkbox"
                                            name="permissions[]"
                                            value="{{ $permission->id }}"
                                            @checked(in_array((int) $permission->id, $assigned))
                                            class="rounded border-zinc-300"
                                        >

                                        <span>{{ $permission->name }}</span>
                                    </label>

                                @endforeach

                            </div>
                        </div>

                        <button type="submit"
                                class="rounded-xl bg-zinc-950 px-5 py-2.5 text-sm font-semibold text-white dark:bg-white dark:text-zinc-950">
                            Update Staff
                        </button>

                    </form>

                    <form method="POST"
                          action="{{ route('businesses.staff.destroy', [$business, $member]) }}"
                          onsubmit="return confirm('Remove this staff member from this business?')"
                          class="mt-3">
                        @csrf
                        @method('DELETE')

                        <button type="submit"
                                class="rounded-xl border border-red-300 px-4 py-2 text-sm font-medium text-red-600 hover:bg-red-50 dark:border-red-900">
                            Remove Staff
                        </button>
                    </form>

                @else

                    <p class="text-sm text-zinc-500">
                        Business owner access is managed separately.
                    </p>

                @endif

            </div>

        @empty

            <div class="rounded-xl border border-zinc-200 p-8 text-center text-zinc-500 dark:border-zinc-700">
                No team members found.
            </div>

        @endforelse

        <div>
            {{ $staff->links() }}
        </div>

    </div>

</div>

</x-layouts::app>
