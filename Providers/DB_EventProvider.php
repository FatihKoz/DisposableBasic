<?php

namespace Modules\DisposableBasic\Providers;

use App\Events\PirepCancelled;
use App\Events\PirepFiled;
use App\Events\PirepPrefiled;
use App\Events\PirepStatusChange;
use App\Events\PirepUpdated;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\DisposableBasic\Listeners\Gen_Cron;
use Modules\DisposableBasic\Listeners\Gen_AutoReject;
use Modules\DisposableBasic\Listeners\Pirep_Cancelled;
use Modules\DisposableBasic\Listeners\Pirep_Filed;
use Modules\DisposableBasic\Listeners\Pirep_Prefiled;
use Modules\DisposableBasic\Listeners\Pirep_StatusChange;
use Modules\DisposableBasic\Listeners\Pirep_Updated;

class DB_EventProvider extends ServiceProvider
{
    // Listen Below Events
    protected $listen =
    [
        PirepCancelled::class => [
            Pirep_Cancelled::class,
        ],

        PirepFiled::class => [
            Pirep_Filed::class,
            Gen_AutoReject::class,
        ],

        PirepPrefiled::class => [
            Pirep_Prefiled::class,
        ],

        PirepStatusChange::class => [
            Pirep_StatusChange::class,
        ],

        PirepUpdated::class => [
            Pirep_Updated::class,
        ],
    ];

    // Subscribe multiple events
    protected $subscribe =
    [
        Gen_Cron::class,
    ];

    // Register Module Events
    public function boot()
    {
        parent::boot();
    }
}
