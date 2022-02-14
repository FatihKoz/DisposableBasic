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
        CronFiveMinute::class => 'cron_05min',
        CronFifteenMinute::class => 'cron_15min',
        CronThirtyMinute::class => 'cron_30min',
        CronHourly::class  => 'cron_hourly',
        CronNightly::class => 'cron_nightly',
        CronWeekly::class => 'cron_weekly',
        CronMonthly::class => 'cron_monthly',
    ];

    public function cron_05min()
    {
        // $this->DB_WriteToLog('05 mins test');
    }

    public function cron_15min()
    {
        // $this->DB_WriteToLog('15 mins test');
        $DB_CronSVC = app(DB_CronServices::class);
        $DB_CronSVC->ReleaseStuckAircraft();
    }

    public function cron_30min()
    {
        // $this->DB_WriteToLog('30 mins test');
    }

    public function cron_hourly()
    {
        // $this->DB_WriteToLog('Hourly test');
    }

    public function cron_nightly()
    {
        // $this->DB_WriteToLog('Nightly test');
    }

    public function cron_weekly()
    {
        // $this->DB_WriteToLog('Weekly test');
    }

    public function cron_monthly()
    {
        // $this->DB_WriteToLog('Monthly test');
    }

    // Test Method
    public function DB_WriteToLog($text = null)
    {
        Log::debug('Disposable Basic | ' . $text);
    }
}
