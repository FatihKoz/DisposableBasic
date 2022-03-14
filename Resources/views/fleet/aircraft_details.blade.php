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
    <table class="table table-sm table-borderless table-striped text-nowrap mb-0">
      <tr>
        <th style="width: 30%;">ICAO / IATA @lang('DBasic::common.type')</th>
        <td>{{ $aircraft->icao }} / {{ $aircraft->iata }}</td>
      </tr>
      @if($aircraft->subfleet && $aircraft->subfleet->fares->count())
        <tr>
          <th>@lang('DBasic::common.config')</th>
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
      @if(filled(optional($aircraft->subfleet)->typeratings))
        <tr>
          <th scope="row">Type Rating(s)</th>
          <td>
            @foreach($aircraft->subfleet->typeratings as $rating)
              @if(!$loop->first) &bull; @endif
              {{ $rating->name }}
            @endforeach
          </td>
        </tr>
      @endif
      <tr>
        <th>@lang('DBasic::common.airline') / @lang('DBasic::common.subfleet')</th>
        <td>
          <a href="{{ route('DBasic.airline', [$aircraft->airline->icao ?? '']) }}">{{ $aircraft->airline->name ?? '' }}</a>
          /
          <a href="{{ route('DBasic.subfleet', [$aircraft->subfleet->type ?? '']) }}">{{ $aircraft->subfleet->name ?? '' }}</a>
        </td>
      </tr>
      @if(filled($aircraft->hub_id) || filled(optional($aircraft->subfleet)->hub_id))
        <tr>
          <th>@lang('DBasic::common.base')</th>
          <td>
            @if(filled($aircraft->hub_id))
              <a href="{{ route('DBasic.hub', [$aircraft->hub_id ?? '']) }}">{{ $aircraft->hub->full_name ?? '' }}</a>
            @else
              <a href="{{ route('DBasic.hub', [$aircraft->subfleet->hub_id ?? '']) }}">{{ $aircraft->subfleet->hub->full_name ?? '' }}</a>
            @endif
          </td>
        </tr>
      @endif
      <tr>
        <th>@lang('DBasic::common.status') / @lang('DBasic::common.state')</th>
        <td>{!! DB_AircraftStatus($aircraft).' '.DB_AircraftState($aircraft) !!}</td>
      </tr>
      @if($aircraft->airport_id)
        <tr>
          <th>@lang('DBasic::common.location')</th>
          <td>
            <a href="{{ route('frontend.airports.show', [$aircraft->airport_id]) }}">{{ $aircraft->airport->full_name ?? $aircraft->airport_id }}</a>
          </td>
        </tr>
      @endif
    </table>
  </div>
  @if($aircraft->fuel_onboard->local() > 0 || $aircraft->landing_time)
    <div class="card-footer p-0 px-1 small fw-bold">
      @if($aircraft->fuel_onboard->local() > 0)
        <span class="float-end">
          @lang('DBasic::common.fuelob'): {{ DB_ConvertWeight($aircraft->fuel_onboard, $units['fuel']) }}
        </span>
      @endif
      @if($aircraft->landing_time)
        <span class="float-start">
          @lang('DBasic::common.lastlnd'): {{ $aircraft->landing_time->diffForHumans() }}
        </span>
      @endif
    </div>
  @endif
</div>
