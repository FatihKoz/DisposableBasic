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
use Modules\DisposableBasic\Services\DB_OnlineServices;

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
        if (DB_Setting('dbasic.networkcheck', false)) {
            // If Network Presence Check is enabled, download WhazzUp Data with cron
            // This will speed up check time, on the other hand will increase server traffic
            $server_name = DB_Setting('dbasic.networkcheck_server', 'IVAO');

            if ($server_name === 'VATSIM') {
                $server_url = 'https://data.vatsim.net/v3/vatsim-data.json';
            } else {
                $server_url = 'https://api.ivao.aero/v2/tracker/whazzup';
            }

            $DB_OnlineSVC = app(DB_OnlineServices::class);
            $DB_OnlineSVC->DownloadWhazzUp($server_name, $server_url);
        }
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
        if (DB_Setting('dbasic.networkcheck', false)) {
            $DB_CronSVC = app(DB_CronServices::class);
            $DB_CronSVC->CleanUpWhazzUpChecks();
        }
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
