<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\DisposableBasic\Models\DB_Jumpseat;
use Modules\DisposableBasic\Models\Enums\DB_RequestStates;

class JumpSeat extends Widget
{
    protected $config = ['base' => null, 'dest' => null, 'hubs' => null, 'price' => null, 'fdates' => null];

    public function run()
    {
        // Check widget config and use module settings if not set
        $base_price = is_numeric($this->config['base']) ? $this->config['base'] : DB_Setting('dbasic.js_base_price', 0.13);
        $free_dates = (is_array($this->config['fdates'] && filled($this->config['fdates']))) ? $this->config['fdates'] : explode(',', DB_Setting('dbasic.js_free_dates', '0101, 1231'));
        $fixed_dest = $this->config['dest'];
        $hubs = is_bool($this->config['hubs']) ? $this->config['hubs'] : DB_Setting('dbasic.js_hubs_only', false);
        $is_possible = (optional(Auth::user())->curr_airport_id != $fixed_dest) ? true : false;
        $price = isset($this->config['price']) ? $this->config['price'] : DB_Setting('dbasic.js_price_type', 'auto');

        if ($price == 'fixed') {
            $price = DB_Setting('dbasic.js_fixed_price', 100);
        } elseif ($price != 'auto' && $price != 'free' && !is_numeric($price)) {
            $price = 'auto';
        }

        if (in_array(Carbon::now()->format('md'), $free_dates)) {
            $price = 'free';
        }

        $pending_request = DB_Jumpseat::where('user_id', Auth::id())->where('status', DB_RequestStates::WAITING)->exists();

        $form_route = 'DBasic.jumpseat';
        $icon_color = 'danger';
        $icon_title = __('DBasic::widgets.js_title_auto');

        if ($price === 'free') {
            $icon_color = 'success';
            $icon_title = __('DBasic::widgets.js_title_free');
        } elseif (is_numeric($price)) {
            $icon_color = 'primary';
            $icon_title = __('DBasic::widgets.js_title_fixed').' '.number_format($price).' '.setting('units.currency');
        }

        return view('DBasic::widgets.jumpseat_travel', [
            'base_price'  => $base_price,
            'fixed_dest'  => $fixed_dest,
            'form_route'  => $form_route,
            'hubs_only'   => ($hubs === true) ? 'hubs_only' : null,
            'icon_color'  => $icon_color,
            'icon_title'  => $icon_title,
            'is_possible' => $is_possible,
            'is_visible'  => Auth::check(),
            'price'       => $price,
            'ar_enabled'  => DB_Setting('dbasic.js_auto_request', true),
            'pr_enabled'  => DB_Setting('dbasic.js_pilot_request', false),
            'req_exists'  => $pending_request,
        ]);
    }
}
