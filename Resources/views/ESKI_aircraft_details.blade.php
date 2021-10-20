<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1 p-0">
      {{ $aircraft->registration }}
      <i class="fas fa-plane float-right"></i>
    </h5>
  </div>
  <div class="card-body p-0">
    <table class="table table-sm table-borderless table-striped mb-0">
      @if ($aircraft->name != $aircraft->registration)
        <tr>
          <th style="width: 30%;" scope="row">@lang('common.name')</th>
          <td>{{ $aircraft->name }}</td>
        </tr>
      @endif
      <tr>
        <th style="width: 30%;" scope="row">ICAO/IATA @lang('DisposableAirlines::common.type')</th>
        <td>{{ $aircraft->icao }} / {{ $aircraft->iata }}</td>
      </tr>
      @if ($aircraft->subfleet->fares->count())
        <tr>
          <th scope="row">@lang('DisposableAirlines::common.config')</th>
          <td>
            @foreach($aircraft->subfleet->fares as $fare)
              @if(!$loop->first) &bull; @endif
              {{ $fare->name }}
              {{ number_format($fare->pivot->capacity) }}
              @if($fare->type == 1) {{ setting('units.weight') }} @else Pax @endif
            @endforeach
          </td>
        </tr>
      @endif
      <tr>
        <th scope="row">@lang('common.status')</th>
        <td>{!! Dispo_AcStatusBadge($aircraft->status) !!}</td>
      </tr>
      <tr>
        <th scope="row">@lang('common.state')</th>
        <td>{!! Dispo_AcStateBadge($aircraft->state, $aircraft->id) !!}</td>
      </tr>
      <tr>
        <th scope="row">@lang('DisposableAirlines::common.airline')</th>
        <td>
          <a href="{{ route('DisposableAirlines.ashow', [$aircraft->subfleet->airline->icao]) }}">{{ $aircraft->subfleet->airline->name }}</a>
        </td>
      </tr>
      <tr>
        <th scope="row">@lang('DisposableAirlines::common.subfleet')</th>
        <td>
          <a href="{{ route('DisposableAirlines.dsubfleet', [$aircraft->subfleet->type]) }}">{{ $aircraft->subfleet->name }}</a>
        </td>
      </tr>
      <tr>
        <th scope="row">@lang('DisposableAirlines::common.base')</th>
        <td>
          @if($aircraft->subfleet->hub_id && $disphubs)
            <a href="{{ route('DisposableHubs.hshow', [$aircraft->subfleet->hub_id]) }}">{{ $aircraft->subfleet->hub->name }}</a>
          @else
            {{ $aircraft->subfleet->hub_id ?? '' }}
          @endif
        </td>
      </tr>
      @if($aircraft->airport_id)
        <tr>
          <th scope="row">@lang('DisposableAirlines::common.location')</th>
          <td>
            <a href="{{ route('frontend.airports.show', [$aircraft->airport_id]) }}">{{ $aircraft->airport->name }}</a>
          </td>
        </tr>
      @endif
      @if($aircraft->fuel_onboard > 0)
        <tr>
          <th scope="row">@lang('DisposableAirlines::common.fuelob')</th>
          <td>{{ Dispo_Fuel($aircraft->fuel_onboard) }}</td>
        </tr>
      @endif
      @if($aircraft->landing_time)
        <tr>
          <th scope="row">@lang('DisposableAirlines::common.lastlnd')</th>
          <td>{{ Carbon::parse($aircraft->landing_time)->diffForHumans() }}</td>
        </tr>
      @endif
    </table>
  </div>
</div>
