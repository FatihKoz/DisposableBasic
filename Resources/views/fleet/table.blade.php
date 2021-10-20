<table class="table table-sm table-borderless table-striped align-middle text-center mb-0 {{ $coll ?? '' }}" id="{{ $coll_id ?? '' }}">
  <tr>
    <th class="text-start">@lang('DBasic::common.reg')</th>
    <th>@lang('DBasic::common.icao')</th>
    @empty($coll)
      <th>@lang('DBasic::common.airline')</th>
      @empty($type)
        <th>@lang('DBasic::common.subfleet')</th>
      @endempty
    @endempty
    <th>@lang('DBasic::common.base')</th>
    <th>@lang('DBasic::common.location')</th>
    <th>@lang('DBasic::common.fuelob')</th>
    @empty($type)
      <th>@lang('DBasic::common.btime')</th>
      <th>@lang('DBasic::common.lastlnd')</th>
    @endempty
    <th>@lang('common.state')</th>
    <th>@lang('common.status')</th>
  </tr>
  @foreach($fleet as $ac)
    <tr>
      <td class="text-start">
        <a href="{{ route('DBasic.aircraft', [$ac->registration]) }}">{{ $ac->registration }} @if($ac->registration != $ac->name) '{{ $ac->name }}' @endif</a>
      </td>
      <td>{{ $ac->icao }}</td>
      @empty($coll)
        <td>
          <a href="{{ route('DBasic.airline', [$ac->subfleet->airline->icao ?? '']) }}">{{ $ac->subfleet->airline->name ?? '' }}</a>
        </td>
        @empty($type)
          <td>
            <a href="{{ route('DBasic.subfleet', [$ac->subfleet->type ?? '']) }}">{{ $ac->subfleet->name ?? '' }}</a>
          </td>
        @endempty
      @endempty
      <td>
        @if(optional($ac->subfleet)->hub_id)
          <a href="{{ route('DBasic.hub', [strtoupper($ac->subfleet->hub_id) ?? '']) }}">{{ $ac->subfleet->hub_id ?? ''}}</a>
        @endif
      </td>
      <td>
        <a href="{{ route('frontend.airports.show', [$ac->airport_id ?? '']) }}">{{ $ac->airport_id ?? '' }}</a>
      </td>
      <td>
        {{ DB_ConvertWeight($ac->fuel_onboard, $units['fuel']) }}
      </td>
      @empty($type)
        <td>
          @if($ac->flight_time > 0)
            @minutestotime($ac->flight_time)
          @endif
        </td>
        <td>{{ optional($ac->landing_time)->diffForHumans() }}</td>
      @endempty
      <td>{!! DB_AircraftState($ac) !!}</td>
      <td>{!! DB_AircraftStatus($ac) !!}</td>
    </tr>
  @endforeach
</table>