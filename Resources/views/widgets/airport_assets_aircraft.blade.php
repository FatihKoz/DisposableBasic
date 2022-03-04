<table class="table table-sm table-striped table-borderless text-center text-nowrap align-middle mb-0">
  <tr>
    <th class="text-start">@lang('DBasic::common.reg')</th>
    <th>@lang('DBasic::common.airline')</th>
    <th>@lang('DBasic::common.subfleet')</th>
    <th>@lang('DBasic::common.fuelob')</th>
    <th class="text-end">@lang('DBasic::common.lastlnd')</th>
  </tr>
  @foreach($assets as $ac)
    <tr @if($ac->status === 'M') 
          class="table-danger" title="@lang('DBasic::widgets.maintenance')" 
        @elseif($ac->simbriefs_count > 0) 
          class="table-primary" title="Booked with SimBrief OFP" 
        @endif
        >
      <td class="text-start">
        <a href="{{ route('DBasic.aircraft', [$ac->registration]) }}" @if($ac->name != $ac->registration) {{ 'title="'.$ac->name.'"' }} @endif>{{ $ac->ident }}</a>
      </td>
      <td>
        <a href="{{ route('DBasic.airline', [optional($ac->airline)->icao ?? '']) }}">{{ optional($ac->airline)->name }}</a>
      </td>
      <td>
        <a href="{{ route('DBasic.subfleet', [optional($ac->subfleet)->type ?? '']) }}">{{ optional($ac->subfleet)->type }}</a>
      </td>
      <td>
        {{ DB_ConvertWeight($ac->fuel_onboard, $units['fuel']) }}
      </td>
      <td class="text-end">
        {{ optional($ac->landing_time)->diffForHumans() }}</a>
      </td>
    </tr>
  @endforeach
</table>