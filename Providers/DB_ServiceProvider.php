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

    $this->loadMigrationsFrom(__DIR__.'/../Database/migrations');

    app('arrilot.widget-namespaces')->registerNamespace('DBasic', 'Modules\DisposableBasic\Widgets');
  }

  // Service Providers
  public function register() { }

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
      'prefix'     => '',
      'middleware' => ['web'],
      'namespace'  => 'Modules\DisposableBasic\Http\Controllers',
    ], function () {
      Route::group([
        'middleware' => ['auth'],
      ], function () {
        // Airlines
        Route::get('dairlines', 'DB_AirlinesController@index')->name('airlines');
        Route::get('dairlines/{icao}', 'DB_AirlinesController@show')->name('airline');
        // Awards
        Route::get('dawards', 'DB_AwardsController@index')->name('awards');
        // Fleet
        Route::get('dfleet', 'DB_FleetController@index')->name('fleet');
        Route::get('dfleet/{subfleet_type}', 'DB_FleetController@subfleet')->name('subfleet');
        Route::get('daircraft/{ac_reg}', 'DB_FleetController@aircraft')->name('aircraft');
        // Hubs
        Route::get('dhubs', 'DB_HubsController@index')->name('hubs');
        Route::get('dhubs/{icao}', 'DB_HubsController@show')->name('hub');
        // News
        Route::get('dnews', 'DB_NewsController@index')->name('news');
        // Ranks
        Route::get('dranks', 'DB_RanksController@index')->name('ranks');
        // Roster
        Route::get('droster', 'DB_RosterController@index')->name('roster');
        // Pireps
        Route::get('dpireps', 'DB_PirepsController@index')->name('pireps');
        // Stats
        Route::get('dstats', 'DB_StatsController@index')->name('stats');
      });
      /*
      Route::group([],
      function () {
        // Public Routes
        Route::get('dstats', 'DB_StatsController@public')->name('stats.public');
      });
      */
    });

    // Admin
    Route::group([
      'as'         => 'DBasic.',
      'prefix'     => 'admin',
      'middleware' => ['web', 'role:admin'],
      'namespace'  => 'Modules\DisposableBasic\Http\Controllers',
    ], function () {
      Route::group([],
        function () {
        Route::get('dbasic', 'DB_AdminController@index')->name('admin');
      });
    });
  }

  // Config
  protected function registerConfig()
  {
    $this->publishes([ __DIR__.'/../Config/config.php' => config_path('DisposableBasic.php'),], 'config');
    $this->mergeConfigFrom( __DIR__.'/../Config/config.php', 'DisposableBasic');
  }

  // Translations
  public function registerTranslations()
  {
    $langPath = resource_path('lang/modules/DisposableBasic');

    if (is_dir($langPath)) {
      $this->loadTranslationsFrom($langPath, 'DBasic');
    } else {
      $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'DBasic');
    }
  }

  // Views
  public function registerViews()
  {
    $viewPath = resource_path('views/modules/DisposableBasic');
    $sourcePath = __DIR__.'/../Resources/views';

    $this->publishes([$sourcePath => $viewPath,], 'views');

    $this->loadViewsFrom(array_merge(array_map(function ($path) {
      return $path . '/modules/DisposableBasic';
    }, \Config::get('view.paths')), [$sourcePath]), 'DBasic');
  }

  public function provides(): array { return []; }
}
