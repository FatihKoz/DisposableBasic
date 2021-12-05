<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use App\Models\Aircraft;
use App\Models\Enums\AircraftState;
use App\Models\Enums\AircraftStatus;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TransferAircraft extends Widget
{
    protected $config = ['aircraft' => null, 'landing' => null, 'list' => null, 'price' => 'auto'];

    public function run()
    {
        $fixed_ac = is_numeric($this->config['aircraft']) ? $this->config['aircraft'] : null;
        $landing_time_margin = is_numeric($this->config['landing']) ? $this->config['landing'] : null;
        $list = $this->config['list'];
        $price = $this->config['price'];
        if ($price != 'auto' && $price != 'free' && !is_numeric($price)) {
            $price = 'auto';
        }
        $rank_restriction = setting('pireps.restrict_aircraft_to_rank', true);
        $rate_restriction = setting('pireps.restrict_aircraft_to_typerating', false);

        $user = Auth::user();

        $form_route = 'DBasic.transferac';
        $icon_color = 'danger';
        $icon_title = __('DBasic::widgets.ta_title_auto');

        if ($price === 'free') {
            $icon_color = 'success';
            $icon_title = __('DBasic::widgets.ta_title_free');
        } elseif (is_numeric($price)) {
            $icon_color = 'primary';
            $icon_title = __('DBasic::widgets.ta_title_fixed') . ' ' . number_format($price) . ' ' . setting('units.currency');
        }

        if ($user) {
            $dest = filled($user->curr_airport_id) ? $user->curr_airport_id : $user->home_airport_id;

            $where = [];
            $where[] = ['airport_id', '!=', $dest];
            $where['state'] = AircraftState::PARKED;
            $where['status'] = AircraftStatus::ACTIVE;

            if (is_numeric($fixed_ac)) {
                $where['id'] = $fixed_ac;
            }

            if ($rank_restriction || $rate_restriction) {
                $userSvc = app(UserService::class);
                $restricted_to = $userSvc->getAllowableSubfleets($user);
                $allowed_subfleets = $restricted_to->pluck('id')->toArray();
            } else {
                $allowed_subfleets = null;
            }

            if (isset($list)) {
                $hubs_array = DB::table('airports')->where('hub', 1)->pluck('id')->toArray();
            } else {
                $hubs_array = null;
            }

            if (is_numeric($landing_time_margin)) {
                $before = Carbon::now()->subHours($landing_time_margin);
            } else {
                $before = null;
            }

            // Get Aircraft which are NOT at User's current location, parked and active
            // Conditionally apply filters for hubs and/or subfleets
            $ts_aircraft = Aircraft::with('airline')
                ->where($where)
                ->when(($list === 'hubs' || $list === 'hub'), function ($query) use ($hubs_array) {
                    return $query->whereIn('airport_id', $hubs_array);
                })->when($list === 'nohubs', function ($query) use ($hubs_array) {
                    return $query->whereNotIn('airport_id', $hubs_array);
                })->when(($rank_restriction || $rate_restriction), function ($query) use ($allowed_subfleets) {
                    return $query->whereIn('subfleet_id', $allowed_subfleets);
                })->when(is_numeric($landing_time_margin), function ($query) use ($before) {
                    return $query->where(function ($group) use ($before) {
                        return $group->whereNull('landing_time')->orWhere('landing_time', '<', $before);
                    });
                })->orderBy('icao')->orderby('registration')->get();

            // List Check
            if ($list === 'hub') {
                $ts_aircraft = $ts_aircraft->filter(function ($ac) {
                    return $ac->airport_id === $ac->subfleet->hub_id;
                });
            }
        }

        return view('DBasic::widgets.transfer_aircraft', [
            'dest'        => isset($dest) ? $dest : null,
            'form_route'  => $form_route,
            'icon_color'  => $icon_color,
            'icon_title'  => $icon_title,
            'is_visible'  => Auth::check(),
            'price'       => $price,
            'fixed_ac'    => $fixed_ac,
            'ts_aircraft' => isset($ts_aircraft) ? $ts_aircraft : null,
        ]);
    }

    public function placeholder()
    {
        $loading_style = '<div class="alert alert-info mb-2 p-1 px-2 small fw-bold"><div class="spinner-border spinner-border-sm text-dark me-2" role="status"></div>Loading aircraft data...</div>';
        return $loading_style;
    }
}
