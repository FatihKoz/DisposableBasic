# Disposable Basic Pack v3

phpVMS v7 module for Basic VA features

> [!IMPORTANT]
> * Minimum required phpVMS v7 version is `phpVms 7.0.52-dev.g0421186c64` / 05.JAN.2025

> [!TIP]
> * Module supports **only** php8.1+ and laravel10
> * _php8.0 and laravel9 compatible latest version: v3.3.1_
> * _php7.4 and laravel8 compatible latest version: v3.0.19_

Module blades are designed for themes using **Bootstrap v5.x** and **FontAwesome v5.x** icons.

This module pack aims to cover basic needs of any Virtual Airline with some new pages, widgets and backend tools. Provides;

* Airlines page (with details of selected airline)
* Fleet page (with details of selected subfleet and/or aircraft)
* Hubs page (with details of selected hub)
* Statistics page (with basic leaderboard options)
* My Sceneries page (allows keeping track of personal installed sceneries, provides available flight counts)
* Support for variable aircraft addons and related displays (mainly for SimBrief integration)
* Additional customizable pre-defined content pages (apart from phpVMS v7 pages system)
* Live Weather Map (uses Windy as its source)
* Configurable JumpSeat Travel and Aircraft Transfer possibilities for pilots at frontend
* Daily random flight assignments/offerings
* Fleet/Flights/Pireps Maps (based on leaflet, mostly customizable)
* Manual Awarding and Manual Payment features
* Aircraft state control (in use, in air, on ground)
* IVAO/VATSIM Network Presence Check (with additional callsign checks)
* Support for IVAO/VATSIM audits (works with presence check results and data)
* Pirep Auto Rejecting capabilities,
* Some widgets to enhance any page/layout as per virtual airline needs
* Database checks (to identify errors easily when needed)
* API endpoints to support data display at landing pages (for roster, live flights, latest pireps, news, stats)
* Discord Notifications (separate from v7's core feature, can be customized to use different webhooks etc.)

## Compatibility with other addons

This addon is fully compatible with phpVMS v7 and it will work with any other addon, specially acars softwares which are %100 compatible with phpVMS v7 too.  

If the acars solution you are using is not compatible with phpVMS v7, then it is highly probable that you will face errors over and there. In this case, please speak with your addon provider not me 'cause I can not fix something I did not broke, or I can not cover somebody else's mistakes, poor compatibility problems etc.

If an addon is fully compatible with phpVMS v7 and needs/uses some custom features, then I can work on this module to support that addon's special needs too.

As of date, module supports vmsACARS.

## Installation and Updates

* Manual Install : Upload contents of the package to your phpvms root `/modules` folder via ftp or your control panel's file manager
* GitHub Clone : Clone/pull repository to your phpvms root `/modules/DisposableBasic` folder
* PhpVms Module Installer : Go to admin -> addons/modules , click Add New , select downloaded file then click Add Module
* Go to admin > addons/modules enable the module
* Go to admin > dashboard (or /update) to trigger module migrations
* When migration is completed, go to admin > maintenance and clean `application` cache

> [!WARNING]
> :information_source: *There is a known bug in v7 core, which causes an error/exception when enabling/disabling modules manually. If you see a server error page or full stacktrace debug window when you enable a module just close that page and re-visit admin area in a different browser tab/window. You will see that the module is enabled and active, to be sure just clean your `application` cache*

### Update (from v3.xx to v3.yy)

Just upload updated files by overwriting your old module files, visit /update and clean `application` cache when update process finishes.

### Update (from v2.xx series to v3.xx)

Below order and steps are really important for proper update from old modules to new combined module pack

> [!CAUTION]
> :warning: **There is no easy going back to v2 series once v3 is installed !!!** :warning:  
> **Backup your database tables and old module files before this process**  
> **Only database tables starting with `disposable_` is needed to be backed up**

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
DBasic.livewx      /dlivewx           // Live Weather Map index page
DBasic.news        /dnews             // News index page
DBasic.pireps      /dpireps           // All Pireps index page
DBasic.ranks       /dranks            // Ranks index page
DBasic.reports     /dreports          // All Pireps index page (public, for IVAO/VATSIM)
DBasic.roster      /droster           // Roster index page (full roster)
DBasic.scenery     /dscenery          // My Sceneries page
DBasic.stats       /dstats            // Statistics index page
DBasic.statistics  /dstatistics       // Statistics index page (public, for IVAO/VATSIM)

DBasic.ivao        /divao             // Audit ready page for IVAO (public)
DBasic.vatsim      /dvatsim           // Audit ready page for VATSIM (public)
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

For runways, simply check `Support Files` folder. There is a world runways database shipped with the module. You can import those runways and have runway selection at SimBrief flight planning form. This is an optional feature like the maintenance details definitions. Default length for runways is meters, module provides automated conversion for runway details, also imperial and metric attributes are provided.

If you want to display subfleet or aircraft images, just put images under public/image/aircraft or public/image/subfleet folders. Files should be in all lowercase including the extension (like tc-grd.jpg). Aircraft images use registration, subfleet images use subfleet type code. (Disposable Theme offers some examples)

## API Endpoints

Module offers below endpoints for API Access with authorization, so data can be placed on landing pages easily. Check module admin page to define your service key, which is needed for authorization.

### Endpoints

```php
/dbapi/events  // Events (both Upcoming and Current)
/dbapi/news    // News
/dbapi/pireps  // Latest Accepted Pireps and Ongoing Live Flights
/dbapi/roster  // Pilot Roster
/dbapi/stats   // Statistics
```
### Header Options and Example Request

```php
Content-Type:application/json
x-service-key:{your service key}
x-roster-type:full // by default api follows your v7 settings, if you want to keep v7 roster with active pilots only but see the full list with api then use this.
x-pirep-type:live // by default api returns latest accepted pireps, if you want to see live flights then use this
x-pirep-count:10 // only used when accepted pireps are being shown, default value is 25
x-news-count:5 // can be used to define the news items being pulled, default is 3
```

```php
// Example CURL Request for Roster
$service_key = "YOUR SERVICE KEY";
$url = "https://your-phpvms-v7-site.com/dbapi/roster";
// This will give you the roster by following your v7 settings, no additional headers are being used
$headers = [
    'Content-Type:application/json',
    'x-service-key:' . $service_key,
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$json = curl_exec($ch);
if (!$json) {
    echo curl_error($ch);
}

curl_close($ch);
$roster = json_decode($json, true);

echo $roster;
```

```php
// Example CURL Request for Live Pireps
$service_key = "YOUR SERVICE KEY";
$url = "https://your-phpvms-v7-site.com/dbapi/pireps";
// This will give you only LIVE FLIGHTS
$headers = [
    'Content-Type:application/json',
    'x-service-key:' . $service_key,
    'x-pirep-type:live',
];

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL,$url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$json = curl_exec($ch);
if (!$json) {
    echo curl_error($ch);
}

curl_close($ch);
$live_flights = json_decode($json, true);

echo $live_flights;
```

You can use Postman or Apidog (or a similar tool) to test api access easily and see returned data for landing page development.  

## Auto Rejection of Pireps

It is possible to auto reject pireps with below checks;  

* Network Presence (IVAO and VATSIM only, percentage)
* Network Callsign (IVAO and VATSIM only, percentage)
* Score (minimum score)
* Landing Rate (maximum landing rate, ft/min)
* Flight Time (minimum flight time, minutes)
* Flight Time Difference (between planned time vs actual time, minutes)
* Pause Time (maximum pause time allowed, minutes)
* Landing Threshold Difference (between first touchdown and runway threshold, feet)
* G-Force (maximum landing g-force)
* Fuel Burn (minimum fuel burn, pounds - lbs)
* Aircraft Title
* Aircraft ICAO Type Code (flown vs. reported aircraft)

For the system to work properly set your acars pireps to be auto approved, let phpvms handle acceptance and module to reject only when needed. Be advised, while using scenery based checks like landing rate and threshold difference, you may get false positives. Also flight time difference is risky to use, you should get reports if long holdings and extra ordinary stuff happens during flights to identify false positives before correcting them.

Aircraft Title and ICAO Type Code checks are tricky, enable with caution only if you are providing aircraft repaints/liveries which you have control over. For FSX/P3D/MSFS series, aircraft title needs to contain the airline ICAO code, like `PMDG B737-800 Winglets THY` or `Fenix A321 TSW CFM Sharklets`. X-Plane is not supported as it does not have a system reporting back the livery being used. To provide support for multi airline setups, code checks both aircraft's and flight's airline code, either of one needs to be in the title. Otherwise the pirep will be rejected due to title mismatch.

Also, Aircraft ICAO Code may not be entered correctly to aircraft files (either by the developer itself or by the repaint artist), don't get surprised if you have false positive rejections while using these title and icao based checks.  

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

System will save individual checks in a separate table which is cleaned up periodically and saves the results as new pirep field values. `network-online` (identified/selected network), `network-presence-check` (ratio of presence), `network-callsign-check` (ratio of callsign usage), `network-callsign-used` (used callsign or callsigns during flight) are the slugs used for those pirep fields.  

When oAuth is enabled for IVAO/VATSIM, presence checks can be done by obtained Network IDs, and custom profile fields will not be required/used.  

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
@widget('DBasic::Events')
@widget('DBasic::FleetOverview')
@widget('DBasic::FlightBoard')
@widget('DBasic::FlightTimeMultiplier')
@widget('DBasic::FuelCalculator')
@widget('DBasic::JournalDetails')
@widget('DBasic::JumpSeat')
@widget('DBasic::LeaderBoard')
@widget('DBasic::Map')
@widget('DBasic::NextRank')
@widget('DBasic::Notams')
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
@widget('DBasic::AirportInfo', ['type' => 'hubs'])
```

* `'type'` can be `'all'` or `'hubs'`

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

### Events

Displays event flights. Flights must have start and end dates, also a departure time for both API endpoints and Widget to work properly. Event flight `route_code` can be defined from module admin page, you can set `EVN` or `E` or anything you wish (max 5 chars). Be sure to set your flight's route_code properly when defining or importing them otherwise neither widget, not the api endpoint will list them. Also flights can be not active and not visible (if you are running schedules with cron and want to hide event flights from being displayed during searches until the day they need to be flown)

```php
@widget('DBasic::Events', ['type' => 'upcoming'])
```

* `'type'` can be `'upcoming'` or left empty to get current (today's) event.


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
* `'fdates'` must be an array of months and days (like ['0101', '0423', '0501', '0519']) in which the transfer will be free

For "Free Dates" logic, month and day numbers should have leading zero's, for example for 1st of May, you should use '0501'.

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

*If you have less flights/pireps than expected displayed on map, check your laravel log for errors, widget simply skips records with faulty data and logs their details*  

### Next Rank

This widget has no special settings except the optional user id, it displays the next available rank with a progress bar. Can be placed to any page protected with login like dashboard.

```php
@widget('DBasic::NextRank')
@widget('DBasic::NextRank', ['user' => $user->id])
```

* `'card'` can be `true` or `false`. When enabled widget will render the card, if not it will be plain text with progress bar. By default it is `true`
* `'user'` can be a user's id like `$user->id` or `3` for profile placements (if not provided widget will use the authorized user's id automatically)

### Notams

Fetches real world NOTAMs for a given airport. 

```php
@widget('DBasic::Notams', ['icao' => $airport->id])
@widget('DBasic::Notams', ['icao' => $current_airport, 'filter' => true])
```

* `'icao'` must be a valid ICAO code of an airport (like `$airport->id` or `$flight->dep_airport_id` or `$pirep->arr_airport_id`) which is present in your v7 airports database
* `'filter'` can be either `true` or `false`. When enabled only Serie A notams will be displayed, which works fine for Europe and America

*To reduce server load and traffic, notams are cached for 45 minutes until a new set is fetched/required.*

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
* `'ftime'` can be any number you wish like `90` (except 0 of course), maximum flight time (in minutes) to have a more defined filter

Above example will pick 3 random flights departing from that user's hub/home airport for that day.

In any config, random flights will be refreshed each day. Be careful when using flight time limitations, you may end up getting no random flights and this is not something running with conditions to revert the logic.

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
* `'fdates'` must be an array of months and days (like ['0101', '0423', '0501', '0519']) in which the transfer will be free

For "Free Dates" logic, month and day numbers should have leading zero's, for example for 1st of May, you should use '0501'.

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

Provides live server data for IVAO and VATSIM networks. It can use oAuth provided Network ID's or custom profile field entries, depending on setup.

```php
@widget('DBasic::WhazzUp', ['network' => 'IVAO', 'field_name' => 'IVAO ID', 'refresh' => 300])
```

* `'network'` should be `'IVAO'` or `'VATSIM'`
* `'refresh'` can be any number in seconds greater than `15` (as per network requirements)
* `'field_name'` should be the exact custom profile field name you defined at phpvms > admin > users > custom fields page for that Network. Necessary only when oAuth is not being used.  

When oAuth is not being used, providing a wrong `field name` value will result in `No Online .... flights found` result even if you have pilots flying online.

## Duplicating Module Blades/Views

Technically all blade files should work with your template but they are mainly designed for Bootstrap v5.* compatible themes. So if something looks weird in your template then you need to edit the blades files. I kindly suggest copying/duplicating them under your theme folder and do your changes there, directly editing module files will only make updating harder for you.

All Disposable Modules are capable of displaying customized files located under your theme folders;

* Original Location : phpvms root `/modules/DisposableBasic/Resources/views/widgets/some_file.blade.php`
* Target Location   : phpvms root `/resources/views/layouts/YourTheme/modules/DisposableBasic/widgets/some_file.blade.php`

As you can see from the above example, filename and sub-folder location is not changed. We only copy a file from a location to another and have a copied version of it.  
If you have duplicated blades and encounter problems after updating the module or after editing, just rename them to see if the provided original works fine.

## License Compatibility & Attribution Link

As per the license, **addon name should be always visible in all pages**. It is best placed in the footer without a logo to save space but link **SHOULD BE** always visible.
```html
Powered by <a href="https://www.phpvms.net" target="_blank">phpVMS v7</a> & <a href="https://github.com/FatihKoz" target="_blank">DH Addons</a>
```
or
```html
Enhanced by <a href="https://github.com/FatihKoz" target="_blank">DH Addons</a>
```
_Not providing attribution link will result in removal of access and no support is provided afterwards._

## Known Bugs / Problems

* SmartCars v3 users reported problems with some of the widgets, root cause is SC3 being not fully phpVMS v7 compatible yet and not sending proper data. So it is highly probable that more features of this module may fail when SC3 is in use too. With latest improvements done to SC3 implementation incompatibilities are reduced but still it may behave different than expected. Please follow changes/updates of SC3 modules being developed by other devs.

## Release / Update Notes

29.OCT.25

* Added Auto Price discount logic for Aircraft Transfer and JumpSeat Widgets _(check module settings)_
* Added safety and reduced fuel burn criteria for GA Aircraft at Auto Reject _(ICAO Default Definitions used)_
* Improved Average Taxi Time calculation logic _(for better performance and less resource usage)_

27.FEB.25

* Fixed Notams Widget  

25.JAN.25

* Added Aircraft Title/Livery/Name checks for auto rejection
* Added Aircraft ICAO Code checks for auto rejection
* Added version info to module admin area
* Added support for oAuth provided Network IDs for presence checks
* Added support for oAuth provided Network IDs for WhazzUp Widget

12.JAN.25

* Version rounding and required minimum phpVMS version change

15.NOV.24

* Added Turkish language support

09.NOV.24

* Added parking stands and runway info to pirep api endpoints
* Eliminated some php warnings  

29.SEP.24

* Added Japanese traslation (Thanks to Minnsch, ANA Virtual Group)  

17.SEP.24

* Updated Aircraft Transfer widget (to consider booked/bidded aircraft)
* Reverted back module view path changes with a slight improvement for active theme
* Updated module.json for future development

24.JUL.24

* Added another failsafe for Notams Widget (more compatibility checks for the source provided data)
* Improved LeaderBoard and PersonalStats Widgets (locale powered month/day names being used)
* Updated Spanish translation 

09.JUL.24

* Added failsafe for Notams Widget (simply logging and skipping process when data is not compatible)

30.JUN.24

* Added another failsafe for Map Widget (for missing airlines)
* Added GDPR compliant IVAO and VATSIM audit support pages (last 90 days is considered)  
  _Admins can export data as csv files and see their all/active network members_

07.JUN.24

* Added NextRank Widget
* Improved WhazzUp Widget staff/admin checks (for IVAO's new FPL ITEM 18 requirement)

01.JUN.24

* Added support to Specs code for VA's using IVAO VA system requirements (FPL ITEM 18 change)

27.MAY.24

* Improved module view path registering code  
  _Change requires latest dev build as of 24th May 2024 or newer_

11.MAY.24

* Improved performance of stats  
  _Specially pax/cgo counts with a reversed lookup, needs periodic crap record cleanup_

09.MAY.24

* Fixed Notams Widget

07.MAY.24

* Fixed Events Widget (bad function call)
* Added Notams Widget

09.APR.24

* Added flight listing capabilities to My Sceneries feature/page

07.APR.24

* Added My Sceneries feature
* Updated Map Widget to support new scenery feature (**Check your duplicated blades**)
* Updated Network Presence checks, added used callsign(s) to pirep field values

31.MAR.24

* Helper changes to match phpvms v7 improvements  
  _Change require latest dev build as of 28th March 2024_

09.MAR.24

* Fixed a width resolving issue on Jumpseat Travel and Airport Info widgets (select2 dropdown)
* Not considering softdeleted pireps for network statistics

22.FEB.24

* Fixed Jumpseat Travel and Aircraft Transfer widget blades (form actions)
* Fixed admin manual awarding (form action)

11.FEB.24

* Removed `laravelcollective/html` package features/usage
  **WARNING: Code Breaking Changes Present, update your duplicated blades**
* Fixed API Stats (aircraft count)

27.JAN.24

* Added Discord News posting feature (separate from v7's internal logic)
* Fixed a possible _division by zero_ error in API endpoint (Pirep display)

25.JAN.24

* Added API endpoint for News
* Improved Pirep and User endpoints with more usable/needed data

21.JAN.24

* Added API endpoints to support data display at landing pages
* Added Events Widget
* Fixed a php8.x warning being generated by not properly ordered definitions

05.JAN.24

* Fixed a typo in Disposable Runways (preventing update/edit)
* License update (Two new disallowed VA's are added)

01.JAN.24

* Fixed sortable columns pagination bug for fleet page **(blade change)**
* Added sortable columns to roster page **(blade change)** and skipped deleted users 
  _(pending, active, on leave, suspended and rejected will be shown only)_
* Updated airlines index page, now some basic counts on index is visible like (aircraft, flights, pireps) **(blade change)**
* Updated world runways database with latest airac 2313, fixed magnetic heading error in old data (was using airac 2311)
  _(truncate the table and import, if you do not have any custom runways)_

12.DEC.23

* Added flight time filtering/limiting option to Random Flights widget
* Added automated free dates option to JumpSeat and Aircraft Transfer widget
* Added a failsafe to Map Widget to skip faulty flight records

18.NOV.23

* Update Disposable Runways (current worldwide data included in release)

08.NOV.23

* Fix ActiveUsers widget
* License update (Another disallowed VA was added)

22.OCT.23

* Added a failsafe for network presence checks (to reduce IVAO/VATSIM server related errors)
* Improved statistics slightly (to eliminate migration based update errors)
* Added pirep quick check icons for admins/staff (for flight time, fuel burn, comments) | _view/blade change_

21.OCT.23

* Added two new options for Auto Reject (Flight Time Difference and Pause Time)
* Added gate/stand display to FlightBoard widget and Aircraft details page | _view/blade change_

17.SEP.23

* FR translation fix (Thanks to @arthurpar06)
* License update (Another disallowed VA was added)

19.AUG.23

* Added support for sortable pagination/results | _view/blade change_ 
* Added support for airport search dropdowns (JumpSeat and AirportInfo widgets)
* Updated Auto Reject (score will be checked only for acars pireps)  
  _Warning: Both changes require latest dev as of 19.AUG.23_

05.AUG.23

* Compatibility update for core v7 changes (Softdelete support and PirepState changes)

23.JUN.23

* Updated module to be compatible with Laravel10

16.JUN.23

**WARNING: THIS IS THE LAST VERSION SUPPORTING PHP 7.4.xx AND LARAVEL 8**

* PT-BR translation updated (Thanks to [FsBrasil](https://fsbrasil.net.br/))

11.JUN.23

* Rounded up version, added compatibility notice
* Added some missing Spanish translations for widgets (Thanks to @arv187)

08.APR.23

* Updated Auto Reject (Manual pireps will not be considered during Network Presence checks)

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
