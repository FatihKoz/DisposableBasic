@php
if (!isset($units)) {
  $units = array(
    'altitude' => setting('units.altitude'), 
    'distance' => setting('units.distance'), 
    'fuel'     => setting('units.fuel'), 
    'weight'   => setting('units.weight')
  );
}
@endphp
<div class="row row-cols-2">
  <div class="col">
    <table class="table table-sm table-borderless table-striped mb-0">
      <tr>
        <th>@lang('DisposableTech::common.bew')</th>
        <td class="text-right">{{ number_format($sp->bew).' '.$units['weight'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.dow')</th>
        <td class="text-right">{{ number_format($sp->dow).' '.$units['weight'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.mzfw')</th>
        <td class="text-right">
          @ability('admin', 'admin-access')
            <i class="fas fa-info-circle mr-2 text-danger" title="@lang('DisposableTech::common.iconfare') {{ number_format(($sp->mzfw - $sp->dow) - ($paxwgt * $sp->mpax)).' '.$units['weight'] }}"></i>
          @endability
          {{ number_format($sp->mzfw).' '.$units['weight'] }}
        </td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.mrw')</th>
        <td class="text-right">{{ number_format($sp->mrw).' '.$units['weight'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.mtow')</th>
        <td class="text-right">{{ number_format($sp->mtow).' '.$units['weight'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.mlw')</th>
        <td class="text-right">{{ number_format($sp->mlw).' '.$units['weight'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.fuelcap')</th>
        <td class="text-right">
          @if($sp->mfuel && ($sp->mtow - $sp->mzfw) < $sp->mfuel)
            <i class="fas fa-info-circle mr-2 text-danger" title="@lang('DisposableTech::common.iconfuel') {{ number_format($sp->mtow - $sp->mzfw).' '.$units['weight'] }}"></i>
          @endif
          {{ number_format($sp->mfuel).' '.$units['fuel'] }}
        </td>
      </tr>
    </table>
  </div>
  <div class="col">
    <table class="table table-sm table-borderless table-striped mb-0">
      <tr>
        <th>@lang('DisposableTech::common.mseat')</th>
        <td class="text-right">{{ $sp->mpax.' (+ '.$sp->crew.' Crew)' }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.range')</th>
        <td class="text-right">{{ number_format($sp->mrange).' '.$units['distance'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.mlevel')</th>
        <td class="text-right">{{ number_format($sp->mceiling).' '.$units['units.altitude'] }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.speeds')</th>
        <td class="text-right">@lang('DisposableTech::common.max') {{ $sp->mspeed }} , @lang('DisposableTech::common.optimum') {{ $sp->cspeed }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.atcc')</th>
        <td class="text-right">{{ $sp->cat }}</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.atce')</th>
        <td class="text-right">{{ $sp->equip }} @if(filled($sp->equip)) / {{ $sp->transponder }}@endif</td>
      </tr>
      <tr>
        <th>@lang('DisposableTech::common.atcp')</th>
        <td class="text-right">{{ $sp->pbn }}</td>
      </tr>
    </table>
  </div>
</div>
