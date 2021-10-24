<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SunriseSunset extends Widget
{
  protected $config = ['location' => null, 'type' => null];

  public function run()
  {
    $location = $this->config['location'];
    $type = ($this->config['type'] === 'civil') ? 'civil' : 'nautical';
    
    // Get location
    $icon = 'fa-bomb';
    $airport = DB::table('airports')->select('id', 'name', 'location', 'lat', 'lon')->where('id', $location)->first();

    if (!$airport) {
      $error = 'Airport not found!';

      return view('DBasic::widgets.sunrise_sunset', [
        'error' => $error,
        'icon'  => $icon,
      ]);
    }

    $footer_note = filled($airport->location) ? $airport->name.' / '.$airport->location : $airport->name;

    // Calculate Sunrise/Sunset with details
    $current_time = time();
    $details = date_sun_info($current_time, $airport->lat, $airport->lon);

    if (!$details) {
      $error = 'Can not calculate details for given location!';

      return view('DBasic::sunrise_sunset', [
        'error' => $error,
        'icon'  => $icon,
      ]);
    }

    foreach ($details as $key => $value) {
      if ($key === $type.'_twilight_begin') { $twilight_begin = $value; }
      if ($key === $type.'_twilight_end') { $twilight_end = $value; }
      if ($key === 'sunrise') { $sunrise = $value; }
      if ($key === 'sunset') { $sunset = $value; }
    }

    $icon = ($current_time > $twilight_begin && $current_time < $twilight_end) ? 'fa-sun' : 'fa-moon';
    $not_able = 'Not able to calculate!';

    return view('DBasic::widgets.sunrise_sunset', [
      'details'        => $details,
      'icon'           => $icon,
      'footer_note'    => isset($footer_note) ? $footer_note : null,
      'location'       => isset($location) ? ' | '.$location : null,
      'twilight_begin' => ($twilight_begin > 1) ? Carbon::parse($twilight_begin)->format('H:i').' UTC' : $not_able,
      'twilight_end'   => ($twilight_end > 1) ? Carbon::parse($twilight_end)->format('H:i').' UTC' : $not_able,
      'sunrise'        => ($sunrise > 1) ? Carbon::parse($sunrise)->format('H:i').' UTC' : $not_able,
      'sunset'         => ($sunset > 1) ? Carbon::parse($sunset)->format('H:i').' UTC' : $not_able,
    ]);
  }
}