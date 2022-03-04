<table class="table table-sm table-borderless table-striped mb-0 text-center text-nowrap align-middle">
  @if(!isset($hide_title))
    <tr>
      <th colspan="6" class="text-start">{{ $sp->saircraft }}</th>
    </tr>
  @endif
  <tr>
    <th>@lang('DBasic::common.bew')</th>
    <th>@lang('DBasic::common.dow')</th>
    <th>@lang('DBasic::common.mzfw')</th>
    <th>@lang('DBasic::common.mrw')</th>
    <th>@lang('DBasic::common.mtow')</th>
    <th>@lang('DBasic::common.mlw')</th>
  </tr>
  <tr>
    <td>
      @if(filled($sp->bew))
        {{ number_format($sp->bew).' '.$units['weight'] }}
      @endif
    </td>
    <td>
      {{ number_format($sp->dow).' '.$units['weight'] }}
    </td>
    <td>
      {{ number_format($sp->mzfw).' '.$units['weight'] }}
      @ability('admin', 'admin-access')
        <i class="fas fa-info-circle mx-1 text-danger" title="@lang('DBasic::common.info_fare') {{ number_format(($sp->mzfw - $sp->dow) - ($units['pax_weight'] * $sp->mpax)).' '.$units['weight'] }}"></i>
      @endability
    </td>
    <td>
      @if(filled($sp->mrw))
        {{ number_format($sp->mrw).' '.$units['weight'] }}
      @endif
    </td>
    <td>
      {{ number_format($sp->mtow).' '.$units['weight'] }}
    </td>
    <td>
      {{ number_format($sp->mlw).' '.$units['weight'] }}
    </td>
  </tr>
  <tr>
    <th colspan="2">@lang('DBasic::common.mfuel')</th>
    <th colspan="2">@lang('DBasic::common.mrange')</th>
    <th colspan="1">@lang('DBasic::common.mspeed')</th>
    <th colspan="1">@lang('DBasic::common.cspeed')</th>
  </tr>
  <tr>
    <td colspan="2">
      @if(filled($sp->mfuel))
        {{ number_format($sp->mfuel).' '.$units['fuel'] }}
        @if($sp->mfuel && ($sp->mtow - $sp->mzfw) < $sp->mfuel)
        <i class="fas fa-info-circle mx-1 text-danger" title="@lang('DBasic::common.info_fuel') {{ number_format($sp->mtow - $sp->mzfw).' '.$units['weight'] }}"></i>
      @endif
    @endif
    </td>
    <td colspan="2">
      @if(filled($sp->mrange)) 
        {{ number_format($sp->mrange).' '.$units['distance'] }}
      @endif
    </td>
    <td colspan="1">
      @if(filled($sp->mspeed))
        {{ number_format($sp->mspeed, 2) }}
      @endif
    </td>
    <td colspan="1">
      @if(filled($sp->mspeed))
        {{ number_format($sp->cspeed, 2) }}
      @endif
    </td>
  </tr>
</table>