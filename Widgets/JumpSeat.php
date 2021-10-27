<?php

namespace Modules\DisposableBasic\Widgets;

use App\Contracts\Widget;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JumpSeat extends Widget
{
    protected $config = ['base' => null, 'dest' => null, 'hubs' => null, 'price' => 'auto'];

    public function run()
    {
        $base_price = is_numeric($this->config['base']) ? $this->config['base'] : 0.13;
        $fixed_dest = $this->config['dest'];
        $hubs = is_bool($this->config['hubs']) ? $this->config['hubs'] : false;
        $is_visible = Auth::check();
        $is_possible = (optional(Auth::user())->curr_airport_id != $fixed_dest) ? true : false;
        $price = $this->config['price'];

        if ($price != 'auto' && $price != 'free' && !is_numeric($price)) {
            $price = 'auto';
        }

        $form_route = 'DBasic.jumpseat';
        $icon_color = 'danger';
        $icon_title = __('DBasic::widgets.js_title_auto');

        if ($price === 'free') {
            $icon_color = 'success';
            $icon_title = __('DBasic::widgets.js_title_free');
        } elseif (is_numeric($price)) {
            $icon_color = 'primary';
            $icon_title = __('DBasic::widgets.js_title_fixed') . ' ' . number_format($price) . ' ' . setting('units.currency');
        }

        if (is_null($fixed_dest) && $is_visible) {
            $where = [];
            if ($hubs) {
                $where['hub'] = 1;
            }

            $js_airports = DB::table('airports')->select('id', 'name', 'location', 'country')->where($where)->orderBy('id')->get();
        }

        return view('DBasic::widgets.jumpseat_travel', [
            'base_price'  => $base_price,
            'fixed_dest'  => $fixed_dest,
            'form_route'  => $form_route,
            'icon_color'  => $icon_color,
            'icon_title'  => $icon_title,
            'is_visible'  => $is_visible,
            'is_possible' => $is_possible,
            'js_airports' => isset($js_airports) ? $js_airports : null,
            'price'       => $price,
        ]);
    }
}
