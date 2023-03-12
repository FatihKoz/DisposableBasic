# Disposable Basic Pack v3

phpVMS v7 module for Basic VA features

Compatible with phpVMS v7 builds as described below;

* Module versions starting with v3.1.xx and up supports only php8 and laravel9
* Minimum required phpVMS v7 version is phpVms `7.0.0-dev+220314.128480` for v3.1.xx
* Module version v3.0.19 is the latest version with php7.4 and laravel8 support
* Latest available phpVMS v7 version is phpVms `7.0.0-dev+220307.00bf18` (07.MAR.22) for v3.0.19
* Minimum required phpVMS v7 version is phpVms `7.0.0-dev+220211.78fd83` (11.FEB.22) for v3.0.19
---
* If you try to use latest version of this addon with an old version of phpvms, it will fail.
* If you try to use latest phpvms with an old version of this addon, it will fail.
* If you try to use your duplicated old blades with this version without checking and applying necessary changes, it will fail.
---

Module blades are designed for themes using **Bootstrap v5.x** and **FontAwesome v5.x** icons.

This module pack aims to cover basic needs of any Virtual Airline with some new pages, widgets and backend tools. Provides;

* Airlines page (with details of selected airline)
* Fleet page (with details of selected subfleet and/or aircraft)
* Hubs page (with details of selected hub)
* Statistics page (with basic leaderboard options)
* Support for variable aircraft addons and related displays (mainly for SimBrief integration)
* Additional customizable pre-defined content pages (apart from phpVMS v7 pages system)
* Live Weather Map (uses Windy as its source)
* Configurable JumpSeat Travel and Aircraft Transfer possibilities for pilots at frontend
* Daily random flight assignments/offerings
* Fleet/Flights/Pireps Maps (based on leaflet, mostly customizable)
* Manual Awarding and Manual Payment features
* Aircraft state control (in use, in air, on ground)
* IVAO/VATSIM Network Presence Check (with additional callsign checks)
* Pirep Auto Rejecting capabilities,
* Some widgets to enhance any page/layout as per virtual airline needs
* Database checks (to identify errors easily when needed)

## Installation and Updates

* Manual Install : Upload contents of the package to your phpvms root `/modules` folder via ftp or your control panel's file manager
* GitHub Clone : Clone/pull repository to your phpvms root `/modules/DisposableBasic` folder
* PhpVms Module Installer : Go to admin -> addons/modules , click Add New , select downloaded file then click Add Module
*
* Go to admin > addons/modules enable the module
* Go to admin > dashboard (or /update) to trigger module migrations
* When migration is completed, go to admin > maintenance and clean `application` cache

:information_source: *There is a known bug in v7 core, which causes an error/exception when enabling/disabling modules manually. If you see a server error page or full stacktrace debug window when you enable a module just close that page and re-visit admin area in a different browser tab/window. You will see that the module is enabled and active, to be sure just clean your `application` cache*

### Update (from v3.xx to v3.yy)

Just upload updated files by overwriting your old module files, visit /update and clean `application` cache when update process finishes.

### Update (from v2.xx series to v3.xx)

Below order and steps are really important for proper update from old modules to new combined module pack

:warning: **There is no easy going back to v2 series once v3 is installed !!!** :warning:  
**Backup your database tables and old module files before this process**  
**Only database tables starting with `disposable_` is needed to be backed up**

* From admin > addons/modules **DISABLE** all old Disposable modules
* From admin > addons/modules **DELETE** all old Disposable modules
* Go to admin > maintenance and clean `all` cache
* Install Disposable Basic module (by following installation procedure)

## Module links and routes

Module does not provide auto links to your phpvms theme, Disposable Theme v3 has some built in menu items but in case you need to manually adjust or use a different theme/menu, below are the routes and their respective url's module provide

Named Routes and Url's

```php
DBasic.airlines    /dairlines         // Airlines index page
DBasic.airline     /dairlines/DSP     // Airline details page, needs an {icao} code to run

DBasic.hubs        /dhubs             // Hubs index page
DBasic.hub         /dhubs/LTAI        // Hub details page, needs an {icao} code to run

DBasic.fleet       /dfleet            // Fleet index page
DBasic.subfleet    /dfleet/B738-DSP   // Subfleet details page, needs a {subfleet_type} code to run
DBasic.aircraft    /daircraft/TC-DHA  // Aircraft details page, needs a {registration} code to run

DBasic.awards      /dawards           // Awards index page
DBasic.news        /dnews             // News index page
DBasic.livewx      /dlivewx           // Live Weather Map index page
DBasic.pireps      /dpireps           // All Pireps index page
DBasic.ranks       /dranks            // Ranks index page
DBasic.reports     /dreports          // All Pireps index page (public, for IVAO/VATSIM)
DBasic.roster      /droster           // Roster index page (full roster)
DBasic.stats       /dstats            // Statistics index page
DBasic.statistics  /dstatistics       // Statistics index page (public, for IVAO/VATSIM)
```

Also for embedding in your main (landing) sites, some public url's are available.  
These pages will have no logo, background image or menu items. They are suitable for iframe usage at your landing pages (or main sites)

```php
/dp_roster  // Pilot roster
/dp_pireps  // Latest pireps (amount can be customized with ?count=25, default is 10)
/dp_stats   // Statistics
/dp_page    // Empty page in which you can place widgets like Flight Board etc as per your needs
```

Usage examples;

```html
<a class="nav-link" href="{{ route('DBasic.fleet') }}" title="Fleet">
  Fleet
  <i class="fas fa-plane mx-1"></i>
</a>

<a class="nav-link" href="{{ route('DBasic.subfleet', [$subfleet->type]) }}" title="Fleet">
  {{ $subfleet->type }}
  <i class="fas fa-link mx-1"></i>
</a>

<a class="nav-link" href="{{ route('DBasic.aircraft', [$aircraft->registration]) }}" title="Fleet">
  {{ $aircraft->registration }}
  <i class="fas fa-paper-plane mx-1"></i>
</a>

<a class="nav-link" href="/dfleet" title="Fleet">
  Fleet
  <i class="fas fa-plane mx-1"></i>
</a>

<a class="nav-link" href="/daircraft/{{ $aircraft->registration}}" title="Fleet">
  {{ $aircraft->registration }}
  <i class="fas fa-plane mx-1"></i>
</a>

<iframe src="https://your.phpvms.site/dp_roster" style="border:none; display:block; width: 500px; height: 600px;" title="Roster"></iframe>
```

## Usage and Module Settings

Check module admin page to view all features and possible settings module offers.

When enabled module can listen pirep events and change Aircraft states (PARKED, IN USE, PARKED) which simply blocks simultaneous usage of aircraft by multiple pilots. Even though there are some background checks and a cron feature to release possible stuck aircraft admins can also manually park an aircraft from admin panel.

Also module can send customized Discord notifications when a pirep gets filed, it is a separate feature compared to phpvms core system. It only sends one message per pirep.

As additional features, you can define addon based specifications for your fleet members. Like defining two profiles for a Boeing B737-800 like one for `Zibo` and another for `PMDG`. These definitions can be per aircraft, per subfleet or per icao type and used both for visual display at respective pages (aircraft/subfleet) and be used with SimBrief API for proper flight planning.

If you are not developing your own pirep checks and/or not using Disposable Special/Extended module solutions you can simply skip using Maintenance periods etc. They are here just for backward compatibility and some va's already based their custom code on them.

For runways, simply check `Support Files` folder. There is a world runways database shipped with the module, it is quite old (from the end of 2020, Airac 2020/14) but still usefull for most airports. You can import those runways and have runway selection at SimBrief flight planning form. This is an optional feature like the maintenance details definitions.

If you want to display subfleet or aircraft images, just put images under public/image/aircraft or public/image/subfleet folders. Files should be in all lowercase including the extension (like tc-grd.jpg). Aircraft images use registration, subfleet images use subfleet type code. (Disposable Theme offers some examples)

## Auto Rejection of Pireps

It is possible to auto reject pireps with Network Presence and Network Callsign checks, also module provides rejection by score, landing rate and flight time options. For the system work properly set your acars pireps to be auto approved, let phpvms handle acceptance and module to reject only when needed.

Auto Rejected pireps can be always accepted by admins/staff via phpvms admin section.

## Database Health Checks

As an additional feature, module provides a quick database health check here. Technically it is a helper for you to solve problems, like finding out missing airports or broken relationships 'caused by either import problems or hard deleting records from your database. Provided results are mostly for usage in your sql queries to fix things manually when needed.

## Manual Awarding & Manual Payment

You can either define a blank award (with provided award class, keep it inactive) as you wish and then assign it your pilots manually. Or when needed you can assign a real award too, imagine a pilot finishes a tour some hours late and automatic awarding does not work anymore etc.

For "Manual Payment" selected user's airline must have enough funds for the transfer, creating money out of thin air is not possible.

## Network Presence & Callsign Checks

Module is able to check online network flights (IVAO/VATSIM) and record presence ratio of the pilot as an additional pirep field value. Also a callsign check can be done, which then also controls the airline identification part of the connection callsign (first 3 letters) against the airlines in your v7 setup. So in a multi airline setup your pilots will be able to operate one airline's flight but can connect to IVAO/VATSIM with another airline you provide service for.

Imagine like you have THY, DLH and RYR in your setup. In this scenario, a pilot flying with a callsign THY1978 but operating a DLH flight will be counted as valid. Pilot online callsigns can be limited with core v7 SimBrief settings, you can force pilots to use their ident as callsign always. Deep checks with flight numbers is not considered due to network errors like refusal of connections with same callsigns etc.

I suggest setting a ratio of 80-85 for presence as there may be connection issues and also a pilot can start boarding but do not connect to IVAO/VATSIM during that time for understanable reasons, this choice may reduce pilot's presence ratio 'cause checks do start with boarding and done on each update afterwards.

By default, ground checks are done on status change (like from BOARDING to PUSHBACK or TAXI etc), airborne checks are triggered with status changes and time interval (by default 5 minutes)... Also it is possible to enable data download via cron, which will speed up check process but will increase your network traffic. So if you are small group with I advise to keep cron download disabled. Module is able to download new data when needed.

Presence checks has an AUTO option (default), this enables checking both networks initially and continues on the one which the pilot is online. If you are operating only on one network, selecting it from settings is still the best choice (to reduce server load and unnecessary traffic) but if you allow both then leave the setting at AUTO and provide two custom profile fields for your pilots to fill in their network ID's. System is designed to check ID's existence too to reduce traffic and server load.

System will save individual checks in a separate table which is cleaned up periodically and saves the results as new pirep field values. `network-online` (identified/selected network), `network-presence` (ratio of presence), `network-callsign` (ratio of callsign usage) are the slugs used for those pirep fields.

## Stable Approach Plugin Support

Stable Approach Plugin is a **great** FDM (Flight Data Monitoring) tool for X-Plane, developed by [@Clamb94](https://github.com/Clamb94).

Admins are able to delete unstable reports, if they see it necessary like getting UNSTABLE result after an intentional test flight by the pilot etc.  
Also it is possible for admins to "Approve" an unstable report as STABLE after checking the details, like a slight deviation or incorrectly identified approach type etc.

For auto receiving FDM reports and matching them with users/pireps, below steps are required to be completed;

**Virtual Airline Requirements;**

1. Add a new custom user profile field with the name `Stable Approach ID`, make it active and private  
  *Only admins and the owner needs to see it*  
2. Enable setting from admin > Disposable Basic page.  
  *Also it is possible to define a custom name for the user profile field here if you wish*  
3. Follow Stable Approach Plugin's wiki/documents to have your own custom repository, va based settings and (optionally) aircraft profiles

`your.phpvms.url` (for host field) and `/dstable` (for target field) should be used in `plugin_version.json` file to receive reports, like below;

```json
    "report_server":{
      "enabled": true,
      "host": "your.phpvms.url",
      "target": "/dstable"
    },
```

**Pilot/User Requirements;**

1. Pilots should enter your va's GitHub repository url `YourGithubUsername/StableApproach` to their plugin settings  
   *X-Plane > Stable Approach > Settings*  
2. Finally pilots should enter their Stable Approach userID's to their user va profiles  
   *phpvms > Profile > Edit*  

Just a generic note for pilots; Report sending is automatic and this process must be completed before your current/active pirep gets filed. Normally a report gets generated while you and the aircraft is still taxiing out from the runway and plugin informs you about this. So be reasonable, do not land and instantly try filing your pirep. In such cases report will still be sent but it will be rejected.  

:warning: *Stable Approach Plugin ONLY support SSL/Secure connections for report uploads* :warning:

For more details please refer to [plugin documentation](https://github.com/Clamb94/StableApproach/wiki/Virtual-Airline-Integration#receive-landing-reports). Module only gets the reports generated by the plugin and stores/displays them.

## Widgets

When needed, you can use below widgets to enhance the data provided by your pages.

```php
@widget('DBasic::ActiveBookings')
@widget('DBasic::ActiveUsers')
@widget('DBasic::AirportAssets')
@widget('DBasic::AirportInfo')
@widget('DBasic::Discord')
@widget('DBasic::FleetOverview')
@widget('DBasic::FlightBoard')
@widget('DBasic::FlightTimeMultiplier')
@widget('DBasic::FuelCalculator')
@widget('DBasic::JournalDetails')
@widget('DBasic::JumpSeat')
@widget('DBasic::LeaderBoard')
@widget('DBasic::Map')
@widget('DBasic::PersonalStats')
@widget('DBasic::RandomFlights')
@widget('DBasic::StableApproach')
@widget('DBasic::Stats')
@widget('DBasic::SunriseSunset')
@widget('DBasic::TransferAircraft')
@widget('DBasic::WhazzUp')
```

### Active Bookings

Shows either active SimBrief bookings or bids.

```php
@widget('DBasic::ActiveBookings', ['source' => 'bids'])
```

* `'source'` can be `'bids'` or `'simbrief'`

### Active Users

Show active users browsing your website (only members), needs the session to be handled by database.

```php
@widget('DBasic::ActiveUsers', ['margin' => 5])
```

* `'margin'` can be any number you wish for inactivity time limit (in minutes)

### Airport Assets

Shows pilots, aircraft or pireps of an airport.

```php
@widget('DBasic::AirportAssets', ['location' => $airport->id, 'type' => 'pireps', 'count' => 50])
```

* `'location'` must be an ICAO code like `$airport->id` or plain text `ETNL`  
* `'type'` can be either `'pilots'` , `'pireps'` or `'aircraft'`  
* `'count'` can be used to limit the displayed assets and must be numeric like 50 or 100 etc.

### Airport Info

Shows all your airports in a dropdown and provides a link to visit their pages.

```php
@widget('DBasic::AirportInfo', ['type' => 'nohubs'])
```

* `'type'` can be `'all'` , `'hubs'` or `'nohubs'`

This widget is designed my @macofallico and slightly enhanced by me. Distributed in this pack with his permission.

### Discord

Shows real time data from your Discord Server. Be advised, you should enable widget support of your Discord server first.

```php
@widget('DBasic::Discord', ['server' => 123456789123456789, 'bots' => true, 'bot' => ' Server Bot'])
```

* `'server'` is your Discord server id.  
* `'bots'` can be `true` or `false` and control displaying of Bots as online users
* `'bot'` should be the distinctive name part you give to your bots. For hiding 'Weahter Bot' and 'MEE6 Bot' for example, use `' Bot'` for this option
* `'gdrp'` can be `true` or `false` (default is false) and applies a similar privatized naming as like phpVMS core $user->name_private result
* `'icao'` can be any text you wish to filter the users displayed by the bot

If you are forcing (or manually renaming) your Discord members to use something like DSP978 - Name Surname etc, then you can use `'icao' => 'DSP'` within the option set. Widget will only show users whose names are starting with DSP.

In a similar manner, setting `'gdpr' => true` will result nicknames to be displayed like `DSP978 - Name S` by the widget.

### Fleet Overview

Displays the airports your fleet is located or gives a SubFleet / ICAO type based count.

```php
@widget('DBasic::FleetOverview', ['type' => 'location', 'hubs' => false])
```

* `'type'` can be `'location'` , `'subfleet'` or `'icao'`  
* `'hubs'` can be either `true` or `false`

### Flight Board

Shows current/active flights. It has no custom settings.

```php
@widget('DBasic::FlightBoard')
```

### Flight Time Multiplier

A Basic flight time calculator. No special settings.

```php
@widget('DBasic::FlightTimeMultiplier')
```

Some VA's or platforms offer double or multiplied hours for some tours and events, thus this may come in handy.

### Fuel Calculator

Basic fuel calculator.

```php
@widget('DBasic::FuelCalculator', ['aircraft' => $aircraft->id])
```

* `'aircraft'` must be an aircraft's id (numeric value, not registration or name etc).

Widget uses either that aircraft's pirep average consumption or if not flown before it uses subfleet average.
Also it is possible to define an ICAO type based manufacturer consumption value (via module admin interface), then it will be used as primary source.

Considering the basic calculation, provided results should not be used for airline ops. Can be used for general aviation or for short range trips etc.

### Journal Details

Provides latest transaction details and overall summary of a user's journal.

```php
@widget('DBasic::JournalDetails', ['user' => $user->id, 'limit' => 25])
```

* `'user'` should be a user's id, when left blank widget will use current authenticated user.
* `'limit'` can be any number to get the latest details, logically last 15 or 20 is more than enough to provide some details
* `'card'` can be either `true` or `false` which will display a card or just plain text. Modal will be always available by clicking the balance

Above example can be used at Profile > index.blade, or anywhere you have the `$user` collection ready.

### Jumpseat Travel

Adds frontend pilot self transfer capability to phpVMS v7.

```php
@widget('DBasic::JumpSeat', ['base' => 0.25, 'price' => 'auto', 'hubs' => true])
```

* `'base'` forces the auto price system to use any given numeric value (like `0.25` cents per nautical mile)  
* `'price'` can be `'free'` , `'auto'` or a fixed numeric value like `250`. Currency is based on your phpvms v7 setttings  
* `'hubs'` can be `true` or `false` only and limits the destinations to hubs.
* `'dest'` can be a fixed airport ICAO code (like `'LTFG'` or `$flight->dpt_airport_id`) and removes the selection dropdown. Provides direct travel to that destination

### Leader Board

Provides a leader board according to config options defined.

```php
@widget('DBasic::LeaderBoard', ['source' => 'pilot', 'hub' => $hub->id, 'count' => 3, 'period' => 'lastm', 'type' => 'lrate_low'])
```

* `'source'` should be `'pilot'`, `'airline'`, `'dep'` or `'arr'`
* `'hub'` can be an airport ID like `$hub->id` or plain text `'EDDH'`
* `'count'` can be any number you want (except 0 of course)
* `'type'` should be `'flight'`, `'time'`, `'distance'`, `'lrate'`, `'lrate_low'`, `'lrate_high'` or `'score'`
* `'period'` can be `'currentm'`, `'lastm'`, `'prevm'`, `'currenty'`, `'lasty'` or `'prevy'`  

The example above will give you the Top 3 pilots of that Hub for last month according to lowest landing rate recorded by acars.

To save server resources, past time based results will be cached until the end of month or year. Others will be cached until the end of day.

### Map

Generates a leaflet map according to config options defined.

```php
@widget('DBasic::Map', ['source' => 'fleet', 'airline' => $airline->id])
@widget('DBasic::Map', ['source' => $hub->id, 'limit' => 1000])
@widget('DBasic::Map', ['location' => true])
@widget('DBasic::Map', ['source' => 'assignment'])
@widget('DBasic::Map', ['source' => 'aerodromes'])
```

* `'source'` can be an airport_id (like `$airport->id` or `'EHAM'`), an airline_id (like `$airline->id` or `3`), `'user'`, `'fleet'`, `'assignment'`, `'aerodromes'`
* `'visible'` can be either `true` or `false` (to show or skip visible flights as per phpvms settings)
* `'limit'` can be a numeric value like `500` (to limit the drawn flights/pireps on the map due to performance reasons)

Below settings can be used to improve map performance WHEN admin settings are set to show/search all flights at search page.  

* `'location'` can be `true` or `false` (to limit the drawn flights to user's current location only)
* `'company'` can be `true` or `false` (to limit the drawn flights to user's company only)
* `'popups'` can be `true` or `false` (to enable/disable detailed popups over great circle lines)

Additionally;

* When `'source' => 'fleet'` is used then you can define a specific airline with `'airline' => $airline->id` (or `'airline' => 3`) to show results for that airline

*For "Assignment" source type, Disposable Special/Extended module needs to be installed and active*  

### Personal Stats

Provides personal pirep statistics per pilot according to config options defined.

```php
@widget('DBasic::PersonalStats', ['user' => $user->id, 'period' => 'lastm', 'type' => 'avglanding'])
```

* `'user'` can be a user's id like `$user->id` or `3`  
* `'period'` can be any number of days (like `15`, except 0 of course), `'currentm'`, `'lastm'`, `'prevm'`, `'currenty'`, `'lasty'`, `'q1'`, `'q2'`, `'q3'`, `'q4'`  
* `'disp'` can be `'full'` to display the results in a pre-defined card  
* `'type'` can be `'avglanding'`, `'avgscore'`, `'avgtime'`, '`tottime'`, `'avgdistance'`, `'totdistance'`, `'avgfuel'`, `'totfuel'`, `'totflight'`, `'fdm'` or `'assignment'`

Please note; `fdm` can be used if you are using *Stable Approach Plugin*, also `assignment` stats are tied to *Monthly Flight Assignments*.

Above example will give the average landing rate of that user for last month.

To save server resources, past time based results will be cached until the end of month or year. Others will be cached until the end of day.

### Random Flights

Picks up some random flights by following your phpVms settings and config options defined.

```php
@widget('DBasic::RandomFlights', ['count' => 3, 'hub' => true, 'daily' => true])
```

* `'count'` can be any number you wish like `3` (except 0 of course)
* `'daily'` can be `true` or `false`. Which will force widget to pick random flights once per day
* `'hub'` can be `true` or `false`. It will force the widget to pick up random flights departing from user's own hub/home airport

Above example will pick 3 random flights departing from that user's hub/home airport for that day.

In any config, random flights will be refreshed each day.

### Stable Approach

Show a pirep's Stable Approach Report in a modal.

* `'pirep'` should be the full pirep model (like `$pirep`)
* `'button'` can be either `true` or `false` (default is false). Changes the clickable item to a button (default is badge)

```php
@widget('DBasic::StableApproach', ['pirep' => $pirep])
@widget('DBasic::StableApproach', ['pirep' => $user->last_pirep, 'button' => true])
```

First example is for generic usage (ex. pirep details page), second one uses a bigger button and a user's last pirep (ex. user profile page)

### Statistics

Provides mainly pirep based statistics for an airline, aircraft or your entire v7 installation.

```php
@widget('DBasic::Stats', ['type' => 'aircraft', 'id' => $aircraft->id])
```

* `'type'` can be `'airline'` , `'aircraft'`, `'home'` (for generic but slightly reduced result set)
* `'id'` can be airline's or aircraft's numeric id like `$airline->id`, `$aircraft->id`, or just `3`

### Sunrise Sunset Details

Provides times related to sun's position for a given location.

```php
@widget('DBasic::SunriseSunset', ['location' => $airport->id, 'type' => 'civil', 'card' => true])
```

* `'location'` should be an airport's icao id like `$airport->id` or `LWSK`
* `'type'` can be `'civil'` or `'nautical'` (defines the twilight periods)
* `'card'` can be `true` or `false` (to display results in a card or just in a single line)

### Transfer Aircraft

Adds frontend pilot self aircraft transfer capability to phpVms v7.

```php
@widget('DBasic::TransferAircraft', ['price' => 'auto', 'landing' => 6])
```

* `'price'` can be `'free'`, `'auto'` or a fixed numeric value like `25000`
* `'list'` can be `'hub'` (ac parked at its hub), `'hubs'` (ac parked at any hub), `'nohubs'` (ac parked at non hub locations)
* `'aircraft'` can be an aircraft's id (like `$aircraft->id`) which removes the selection dropdown and transfers provided aircraft
* `'landing'` can be any number hours like `24` which blocks transfer of recently used/landed aircraft

Above example will calculate automatic transfer price according to great circle distance between airports and will allow only transfer of aircraft which are landed at least 6 hours from that time.
There is no `base` price definition for this widget. It uses airport fuel prices, that aircraft's average fuel consumption and ground handling costs.

### User Pireps

Displays a users latest pireps with reverse order. Designed mainly for user profile pages or somewhere similar.

```php
@widget('DBasic::UserPireps', ['user' => $user->id, 'limit' => 50])
```

* `'user'` should be the user id, either a variable as in the example `$user->id` or an integer like `3`
* `'limit'` should be an integer value like `50`, by default widget displays 25 entries

### WhazzUp

Provides live server data for IVAO and VATSIM networks.

```php
@widget('DBasic::WhazzUp', ['network' => 'IVAO', 'field_name' => 'IVAO ID', 'refresh' => 300])
```

* `'network'` should be `'IVAO'` or `'VATSIM'`
* `'refresh'` can be any number in seconds greater than `15` (as per network requirements)
* `'field_name'` should be the exact custom profile field name you defined at phpvms > admin > users > custom fields page for that Network.

Providing a wrong `field name` value will result in `No Online .... flights found` result even if you have pilots flying online.

## Duplicating Module Blades/Views

Technically all blade files should work with your template but they are mainly designed for Bootstrap v5.* compatible themes. So if something looks weird in your template then you need to edit the blades files. I kindly suggest copying/duplicating them under your theme folder and do your changes there, directly editing module files will only make updating harder for you.

All Disposable Modules are capable of displaying customized files located under your theme folders;

* Original Location : phpvms root `/modules/DisposableBasic/Resources/views/widgets/some_file.blade.php`
* Target Location   : phpvms root `/resources/views/layouts/YourTheme/modules/DisposableBasic/widgets/some_file.blade.php`

As you can see from the above example, filename and sub-folder location is not changed. We only copy a file from a location to another and have a copied version of it.  
If you have duplicated blades and encounter problems after updating the module or after editing, just rename them to see if the provided original works fine.

## Release / Update Notes

12.MAR.23

* Added total/average passenger and freight figures to statistics

24.FEB.23

* Added a new option to Map Widget (Airports)

11.FEB.23

* Updated Discord Widget (to fix "Rate Limit" problems)
* Updated WhazzUp Data download (to bypass VATSIM server feed errors)

04.FEB.23

* Fixed Auto Reject (not working for network presence and callsign)
* Improved Auto Reject (it will consider online flights only for callsign based rejection)

03.FEB.23

* Added Network Statistics (for IVAO and VATSIM audits)
* Provided a public route for stats (for IVAO and VATSIM audits)
* Fixed Auto Reject settings (forced negative values for landing rate criteria)

28.JAN.23

* Network Presence check update (bugfix and logic improvements)
* Aircraft Transfer widget fix (fuel price calculation was failing in some rare cases)

15.JAN.23

* More Network Presence check updates (performance improvements mostly)
* Updated Network Check badge to show different colors for IVAO/VATSIM/OFFLINE (instead of ACCEPTED/REJECTED state)

14.JAN.23

* Updated Network Presence Checks (Now allows both networks to be checked at the same time)
* Updated pirep listing blades to show Network Presence checks (for IVAO/VATSIM audits)
* Updated helpers (to provide button/badge for Network checks)

12.JAN.23

* DE Translation (Thanks to [Cyber Air](http://www.cyber-air.org/))
* Failsafe for null landing rate (Auto Reject)
* Stable Approach Report visual fix (following SA 1.4.x beta changes)

26.DEC.22

* Added new checks to Auto Reject (Thanks to @arthurpar06)
* Fixed landing rate check of Auto Reject

25.DEC.22

* Added Callsign to Network Presence checks
* Added Auto Reject feature

17.DEC.22

* Added public pireps route (for IVAO/VATSIM compatibility)
* Added UserPireps widget to display a pilots pireps only (can be used at profile pages for IVAO/VATSIM purposes)

15.NOV.22

* PT-BR Translation (Thanks to @Joaolzc)
* PT-PT Translation (Thanks to @PVPVA , specially JCB)

13.NOV.22

* License Updated (more non-authorized virtual airlines added !)
* Map Widget Updated (added auto zoom and support for Disposable Special : Monthly Flight Assignments)

23.OCT.22

* Added price checking ability to JumpSeat Travel and Aircraft Transfer Widgets (can be improved later)
* Updated some widget and page blades to follow theme configuration (for displaying pilot idents/callsigns)
* Added some debug log entries to backend processes to track down some edge cases (zero aircraft transfer cost sometimes)

04.SEP.22

* Updated Random Flights widget code (reducing possibility of reaching limits of MySQL with 65k flights)
* Added IVAO/VATSIM Network Presence checks (pireps will have online flight percentage in fields)

22.AUG.22

* Fixed a javascript issue in Fuel Calculator widget (thx to @ARV187)
* Added radio telephony "Callsign" to SimBrief form (will be automatically used if defined)

21.AUG.22

* Added radio telephony "Callsign" to airline details page
* Slightly updated Fuel Calculator widget (moved script to scripts)

14.AUG.22

* Added Notes to Hub Details page (needs phpvms core update about Airport Notes)
* Added more fields to Specs (SELCAL, HEX, Custom Remarks, can be used while defining specs per each aircraft)
* Added user based Missing Airport checks

09.JUL.22

* Specs: Added baggage weights to SimBrief form (acdata) to follow up changes/improvements done to the API.

11.JUN.22

* Fixed a conversion error in Theme Helpers (fuel price conversion from lbs to kgs)

25.APR.22

* Fixed French translation, thanks to @loko06320
* Added month name translations and simple localization capability for dates  
  *Only used in Personal Stats Widget at the moment*

06.APR.22

* Added role/ability support for module backend features
* Improved admin pages to show units as placeholders

14.MAR.22

* Module is now only compatible with php8 and Laravel9
* All module blades changed to provide better support mobile devices
* Module helpers updated to meet new core requirements
* Module controller and services updated to meet new core requirements
* Some more failsafe checks added to cover admin/user errors
* Added Journal Details widget
* Added Manual Awarding and Manual Payment features
* Spaning (Spain) translation, thanks to @arv187

01.MAR.22

**WARNING: THIS IS THE LAST VERSION SUPPORTING PHP 7.4.xx AND LARAVEL 8**

* Updated Stable Approach Plugin support (Approve and Delete functions added)

28.FEB.22

* Refactored some module helpers
* Updated Personal Stats Widget and blade

23.FEB.22

* Added more details to Stable Approach Plugin reports (touchdown speed, runway details)
* Removed inactive users from WhazzUp widget (skip inactives/rejected/suspended users)

19.FEB.22

* Added pilot counts to Hubs index page

14.FEB.22

* Updated cron based features (Requries phpVms 7.0.0-dev+220211.78fd83 or later)

11.FEB.22

* Added a failsafe to AirportAssets widget for deleted user ranks
* Added two new options to Discord Widget (GDPR naming and Username filtering)

06.FEB.22

* Updated French Translation (Thanks to @arthurpar06)
* Added weights to tech (can be used to define ICAO type based checks)

04.FEB.22

* Added French translation (Thanks to Jbaltazar67, from phpVMS Forum)
* Added new external page for latest pireps / suitable for iframe usage
* Fixed Italian translation
* Updated WhazzUp Widget (it will not show pilots not flying for the VA)
* Updated Stable Approach Report Widget **blade**, it will now show the TDZ length too
* Updated Flight Board Widget to skip "initiated" but stuck flights (edge case)

11.JAN.22

* Small fixes (Subfleet details page failsafe, Specs ordering, Personal Stats Widget text grammar correction)
* Added "False Runway Identifications" check to Disposable Database Checks (for vmsAcars users, just for info)

05.JAN.22

* Added two new options for Personal Stats Widget (for Stable Approach Plugin and Monthly Flight Assignments)
* Added FDM Results to Pireps display/listing page (visible only to admins)
* Improved Stable Approach Report modal (for to be compatible table/list views)

03.JAN.22

* Fixed Map Widget (it now consider type rating restrictions like rank restrictions for fleet display)
* Performance improvements for Map Widget (Maps will not display detailed popups if the flight count is over 1000)

02.JAN.22

* Added two new config options to Map Widget to have better control of drawn flights and increase performance
* Added used aircraft ICAO to Stable Approach reports (both to Analysis and card footer)

20.DEC.21

* Added a failsafe for Random Flights widget (for assigned but somehow deleted flights)
* Fixed the failsafe for Stable Approach reports
* Enhanced the readme section for Stable Approach Plugin support

18.DEC.21

* Added support for receiving Stable Approach Plugin (X-Plane) reports.
* Added new widget for Stable Approach Plugin support
* Fixed a typo in EventServiceProvider (Cron Listeners)

05.DEC.21

* Added "Type Ratings" support (to display at aircraft/subfleet details and use in restrictions where needed)
* Improved "Aircraft Hub" support (mainly fixed blades to use the correct hub where needed)

04.DEC.21

* Fixed migration errors when a custom table prefix is used during phpvms install
* Added "Aircraft Hub" support (apart from Subfleet Hub, as per the core changes about it)
* Requires updated phpvms dev build released after 30.NOV.21

26.NOV.21

* Added some failsafe for positive (or zero) landing rates
* Italian translation (Thanks Fabietto for his support)

21.NOV.21

* Fix monetary value display issues (Airline overall finance)

18.NOV.21

* Fixed JumpSeat Travel widget's button (Quick Travel option)
* Recuded Discord widget's vertical size for crowded servers (it uses overflow-auto now)

16.NOV.21

* Initial Release
