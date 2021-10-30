<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      {{ $aircraft->registration }}
      @if($aircraft->name != $aircraft->registration)
        &nbsp;"{{ $aircraft->name }}"
      @endif
      <i class="fas fa-plane float-end"></i>
    </h5>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-sm table-borderless table-striped mb-0">
      <tr>
        <th style="width: 30%;" scope="row">ICAO / IATA @lang('DBasic::common.type')</th>
        <td>{{ $aircraft->icao }} / {{ $aircraft->iata }}</td>
      </tr>
      @if($aircraft->subfleet && $aircraft->subfleet->fares->count())
        <tr>
          <th scope="row">@lang('DBasic::common.config')</th>
          <td>
            @foreach($aircraft->subfleet->fares as $fare)
              @if(!$loop->first) &bull; @endif
              {{ $fare->name }}
              {{ number_format($fare->pivot->capacity) }}
              @if($fare->type == 1) {{ $units['weight'] }} @else Pax @endif
            @endforeach
          </td>
        </tr>
      @endif
      <tr>
        <th scope="row">@lang('DBasic::common.status') / @lang('DBasic::common.state')</th>
        <td>{!! DB_AircraftStatus($aircraft).' '.DB_AircraftState($aircraft) !!}</td>
      </tr>
      <tr>
        <th scope="row">@lang('DBasic::common.airline')</th>
        <td>
          <a href="{{ route('DBasic.airline', [$aircraft->subfleet->airline->icao ?? '']) }}">{{ $aircraft->subfleet->airline->name ?? '' }}</a>
        </td>
      </tr>
      <tr>
        <th scope="row">@lang('DBasic::common.subfleet')</th>
        <td>
          <a href="{{ route('DBasic.subfleet', [$aircraft->subfleet->type ?? '']) }}">{{ $aircraft->subfleet->name ?? '' }}</a>
        </td>
      </tr>
      <tr>
        <th scope="row">@lang('DBasic::common.base')</th>
        <td>
          <a href="{{ route('DBasic.hub', [$aircraft->subfleet->hub_id ?? '']) }}">{{ $aircraft->subfleet->hub->full_name ?? '' }}</a>
        </td>
      </tr>
      @if($aircraft->airport_id)
        <tr>
          <th scope="row">@lang('DBasic::common.location')</th>
          <td>
            <a href="{{ route('frontend.airports.show', [$aircraft->airport_id]) }}">{{ $aircraft->airport->full_name ?? $aircraft->airport_id }}</a>
          </td>
        </tr>
      @endif
      @if($aircraft->fuel_onboard > 0)
        <tr>
          <th scope="row">@lang('DBasic::common.fuelob')</th>
          <td>{{ DB_ConvertWeight($aircraft->fuel_onboard, $units['fuel']) }}</td>
        </tr>
      @endif
      @if($aircraft->landing_time)
        <tr>
          <th scope="row">@lang('DBasic::common.lastlnd')</th>
          <td>{{ $aircraft->landing_time->diffForHumans() }}</td>
        </tr>
      @endif
    </table>
  </div>
</div>
