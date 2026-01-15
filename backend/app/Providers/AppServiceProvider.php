<?php

namespace App\Providers;

use App\Models\Offer;
use App\Observers\OfferObserver;
use App\Events\OfferStatusChanged;
use App\Events\OfferNeedsReview;
use App\Listeners\SendOfferStatusChangedNotification;
use App\Listeners\SendOfferNeedsReviewNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
        ResetPassword::createUrlUsing(function (object $notifiable, string $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        // Register Observer
        Offer::observe(OfferObserver::class);

        // Register Event Listeners
        Event::listen(
            OfferStatusChanged::class,
            SendOfferStatusChangedNotification::class
        );

        Event::listen(
            OfferNeedsReview::class,
            SendOfferNeedsReviewNotification::class
        );
    }
}
