<?php

namespace Modules\DisposableBasic\Providers;

use App\Services\ModuleService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;

class DB_ServiceProvider extends ServiceProvider
{
    protected $moduleSvc;

    // Boot the application events
    public function boot()
    {
        $this->moduleSvc = app(ModuleService::class);

        $this->registerRoutes();
        $this->registerTranslations();
        $this->registerConfig();
        $this->registerViews();
        $this->registerLinks();

        $this->loadMigrationsFrom(__DIR__ . '/../Database/migrations');

        app('arrilot.widget-namespaces')->registerNamespace('DBasic', 'Modules\DisposableBasic\Widgets');
    }

    // Service Providers
    public function register()
    {
    }

    // Module Links
    public function registerLinks()
    {
        $this->moduleSvc->addAdminLink('Disposable Basic', '/admin/dbasic', 'pe-7s-tools');
    }

    // Routes
    protected function registerRoutes()
    {
        // Frontend
        Route::group([
            'as'         => 'DBasic.',
            'middleware' => ['web', 'auth'],
            'namespace'  => 'Modules\DisposableBasic\Http\Controllers',
            'prefix'     => '',
        ], function () {
            // Airlines
            Route::get('dairlines', 'DB_AirlineController@index')->name('airlines');
            Route::get('dairlines/{icao}', 'DB_AirlineController@show')->name('airline');
            Route::get('dairline/{id}', 'DB_AirlineController@myairline')->name('myairline');
            // Awards
            Route::get('dawards', 'DB_AwardController@index')->name('awards');
            // Fleet
            Route::get('dfleet', 'DB_FleetController@index')->name('fleet');
            Route::get('dfleet/{subfleet_type}', 'DB_FleetController@subfleet')->name('subfleet');
            Route::get('daircraft/{ac_reg}', 'DB_FleetController@aircraft')->name('aircraft');
            // Hubs
            Route::get('dhubs', 'DB_HubController@index')->name('hubs');
            Route::get('dhubs/{icao}', 'DB_HubController@show')->name('hub');
            // News
            Route::get('dnews', 'DB_NewsController@index')->name('news');
            // Ranks
            Route::get('dranks', 'DB_RankController@index')->name('ranks');
            // Roster
            Route::get('droster', 'DB_RosterController@index')->name('roster');
            // Pages
            Route::get('dlivewx', 'DB_PageController@livewx')->name('livewx');
            // Pireps
            Route::get('dpireps', 'DB_PirepController@index')->name('pireps');
            // Stable Approach
            Route::get('dstable', 'DB_StableApproachController@index')->name('stable');
            // Statistics
            Route::get('dstats', 'DB_StatisticController@index')->name('stats');
            // Widgets
            Route::match(['get', 'post'], 'djumpseat', 'DB_WidgetController@jumpseat')->name('jumpseat');
            Route::match(['get', 'post'], 'dtransferac', 'DB_WidgetController@transferac')->name('transferac');
        });

        // Frontend Public
        Route::group([
            'as'         => 'DBasic.',
            'middleware' => ['web'],
            'namespace'  => 'Modules\DisposableBasic\Http\Controllers',
            'prefix'     => '',
        ], function () {
            // Public Pages (for IVAO/VATSIM Audits)
            Route::get('dreports', 'DB_PirepController@index')->name('reports');
            Route::get('dstatistics', 'DB_StatisticController@index')->name('statistics');
            // Plain Pages
            Route::get('dp_roster', 'DB_WebController@roster')->name('dp_roster');
            Route::get('dp_stats', 'DB_WebController@stats')->name('dp_stats');
            Route::get('dp_page', 'DB_WebController@page')->name('dp_page');
            Route::get('dp_pireps', 'DB_WebController@pireps')->name('dp_pireps');
        });

        // API Public
        Route::group([
            'as'         => 'DBasic.',
            'middleware' => ['api'],
            'namespace'  => 'Modules\DisposableBasic\Http\Controllers',
            'prefix'     => '',
        ], function () {
            // Stable Approach Plugin Report
            Route::post('dstable/new', 'DB_StableApproachController@store');
        });

        // Admin
        Route::group([
            'as'         => 'DBasic.',
            'middleware' => ['web', 'auth', 'ability:admin,admin-access'],
            'namespace'  => 'Modules\DisposableBasic\Http\Controllers',
            'prefix'     => 'admin',
        ], function () {
            Route::get('dbasic', 'DB_AdminController@index')->name('admin')->middleware('ability:admin,addons,modules');
            Route::get('dcheck', 'DB_AdminController@health_check')->name('health_check')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'dsettings_update', 'DB_AdminController@settings_update')->name('settings_update')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'dpark_aircraft', 'DB_AdminController@park_aircraft')->name('park_aircraft')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'dspecs', 'DB_SpecController@index')->name('specs')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'dspecs_store', 'DB_SpecController@store')->name('specs_store')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'dtech', 'DB_TechController@index')->name('tech')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'dtech_store', 'DB_TechController@store')->name('tech_store')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'drunway', 'DB_RunwayController@index')->name('runway')->middleware('ability:admin,addons,modules');
            Route::match(['get', 'post'], 'drunway_store', 'DB_RunwayController@store')->name('runway_store')->middleware('ability:admin,addons,modules');
            Route::post('dstable/update', 'DB_StableApproachController@update')->name('stable_update')->middleware('ability:admin,addons,modules');
            Route::post('dmanual_award', 'DB_AdminController@manual_award')->name('manual_award')->middleware('ability:admin,addons,modules');
            Route::post('dmanual_payment', 'DB_AdminController@manual_payment')->name('manual_payment')->middleware('ability:admin,addons,modules');
        });
    }

    // Config
    protected function registerConfig()
    {
        $this->publishes([__DIR__ . '/../Config/config.php' => config_path('DisposableBasic.php'),], 'config');
        $this->mergeConfigFrom(__DIR__ . '/../Config/config.php', 'DisposableBasic');
    }

    // Translations
    public function registerTranslations()
    {
        $langPath = resource_path('lang/modules/DisposableBasic');

        if (is_dir($langPath)) {
            $this->loadTranslationsFrom($langPath, 'DBasic');
        } else {
            $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'DBasic');
        }
    }

    // Views
    public function registerViews()
    {
        $viewPath = resource_path('views/modules/DisposableBasic');
        $sourcePath = __DIR__ . '/../Resources/views';

        $this->publishes([$sourcePath => $viewPath,], 'views');

        $this->loadViewsFrom(array_merge(array_map(function ($path) {
            return $path . '/modules/DisposableBasic';
        }, \Config::get('view.paths')), [$sourcePath]), 'DBasic');
    }

    public function provides(): array
    {
        return [];
    }
}
