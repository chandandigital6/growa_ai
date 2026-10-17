<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Gate;
use App\Models\Appointment;
use App\Models\Business;
use App\Models\Review;
use App\Models\ServiceEnquiry;
use App\Observers\GrowyaEventNotificationObserver;
use App\Models\Order;
use App\Observers\GrowyaOrderNotificationObserver;
use App\Observers\GrowyaReferralBusinessObserver;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */


    public function boot(): void
    {
        Gate::policy(
            \App\Models\Business::class,
            \App\Policies\BusinessPolicy::class
        );

        Gate::before(function ($user, $ability) {
            return $user->hasRole('super_admin', 'web')
                ? true
                : null;
        });

        $this->configureDefaults();


        Business::observe(GrowyaEventNotificationObserver::class);

        ServiceEnquiry::observe(GrowyaEventNotificationObserver::class);

        Appointment::observe(GrowyaEventNotificationObserver::class);

        Review::observe(GrowyaEventNotificationObserver::class);
        Order::observe(GrowyaOrderNotificationObserver::class);
        Business::observe(GrowyaReferralBusinessObserver::class);
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn(): ?Password => app()->isProduction()
                ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
                : null,
        );
    }
}
