<table class="table table-sm table-borderless table-striped align-middle text-center text-nowrap mb-0">
  <tr>
    <th class="text-start">@lang('DBasic::common.reg')</th>
    <th>@lang('DBasic::common.icao')</th>
    @empty($compact_view)
      <th>@lang('DBasic::common.airline')</th>
      <th>@lang('DBasic::common.subfleet')</th>
    @endempty
    @empty($hub_ac)
      <th>@lang('DBasic::common.base')</th>
    @endempty
    @empty($visitor_ac)
      <th>@lang('DBasic::common.location')</th>
    @endempty
    <th>@lang('DBasic::common.fuelob')</th>
    <th>@lang('DBasic::common.btime')</th>
    <th>@lang('DBasic::common.lastlnd')</th>
    <th>@lang('DBasic::common.state')</th>
    <th>@lang('DBasic::common.status')</th>
  </tr>
  @foreach($aircraft as $ac)
    <tr @if($ac->simbriefs_count > 0) class="table-primary" @endif>
      <td class="text-start">
        <a href="{{ route('DBasic.aircraft', [$ac->registration]) }}">
          {{ $ac->registration }}
          @if($ac->registration != $ac->name) '{{ $ac->name }}' @endif
        </a>
      </td>
      <td>{{ $ac->icao }}</td>
      @empty($compact_view)
        <td>
          <a href="{{ route('DBasic.airline', [$ac->airline->icao ?? '']) }}">
            {{ $ac->airline->name ?? '' }}
          </a>
        </td>
        <td>
          <a href="{{ route('DBasic.subfleet', [$ac->subfleet->type ?? '']) }}">
            {{ $ac->subfleet->name ?? '' }}
          </a>
        </td>
      @endempty
      @empty($hub_ac)
        <td>
          @if(filled($ac->hub_id))
            <a href="{{ route('DBasic.hub', [$ac->hub_id ?? '']) }}">
              {{ $ac->hub_id ?? '' }}
            </a>
          @else          
            <a href="{{ route('DBasic.hub', [$ac->subfleet->hub_id ?? '']) }}">
              {{ $ac->subfleet->hub_id ?? ''}}
            </a>
          @endif
        </td>
      @endempty
      @empty($visitor_ac)
        <td>
          <a href="{{ route('frontend.airports.show', [$ac->airport_id ?? '']) }}">
            {{ $ac->airport_id ?? '' }}
          </a>
        </td>
      @endempty
      <td>
        {{ DB_ConvertWeight($ac->fuel_onboard, $units['fuel']) }}
      </td>
      <td>{{ DB_ConvertMinutes($ac->flight_time, '%2dh %2dm') }}</td>
      <td>{{ optional($ac->landing_time)->diffForHumans() }}</td>
      <td>{!! DB_AircraftState($ac) !!}</td>
      <td>{!! DB_AircraftStatus($ac) !!}</td>
    </tr>
  @endforeach
</table>