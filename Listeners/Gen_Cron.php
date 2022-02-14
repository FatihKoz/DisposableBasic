<?php

namespace Modules\DisposableBasic\Listeners;

use App\Contracts\Listener;
use App\Events\CronFiveMinute;
use App\Events\CronFifteenMinute;
use App\Events\CronThirtyMinute;
use App\Events\CronHourly;
use App\Events\CronNightly;
use App\Events\CronWeekly;
use App\Events\CronMonthly;
use Illuminate\Support\Facades\Log;
use Modules\DisposableBasic\Services\DB_CronServices;

class Gen_Cron extends Listener
{
    public static $callbacks = [
        CronFiveMinute::class => 'handle_05min',
        CronFifteenMinute::class => 'handle_15min',
        CronThirtyMinute::class => 'handle_30min',
        CronHourly::class  => 'handle_hourly',
        CronNightly::class => 'handle_nightly',
        CronWeekly::class => 'handle_weekly',
        CronMonthly::class => 'handle_monthly',
    ];

    // Cron 5 Mins
    public function handle_05min()
    {
        // $this->DB_WriteToLog('05 mins test');
    }

    // Cron 15 Mins
    public function handle_15min()
    {
        $DB_CronSVC = app(DB_CronServices::class);
        $DB_CronSVC->ReleaseStuckAircraft();
        // $this->DB_WriteToLog('15 mins test');
    }

    // Cron 30 Mins
    public function handle_30min()
    {
        // $this->DB_WriteToLog('30 mins test');
    }

    // Cron Hourly Mins
    public function handle_hourly()
    {
        // $this->DB_WriteToLog('Hourly test');
    }

    // Cron Nightly
    public function handle_nightly()
    {
        // $this->DB_WriteToLog('Nightly test');
    }

    // Cron Weekly
    public function handle_weekly()
    {
        // $this->DB_WriteToLog('Weekly test');
    }

    // Cron Monthly Mins
    public function handle_monthly()
    {
        // $this->DB_WriteToLog('Monthly test');
    }

    // Test Method
    public function DB_WriteToLog($text = null)
    {
        Log::debug('Disposable Basic | ' . $text);
    }
}
