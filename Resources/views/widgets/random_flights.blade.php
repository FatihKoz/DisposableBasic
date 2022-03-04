@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::widgets.random_flights')
        <i class="fas fa-random float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-sm table-borderless table-striped text-center text-nowrap align-middle mb-0">
        <tr>
          <th class="text-start">@lang('DBasic::common.flightno')</th>
          <th>@lang('DBasic::common.orig')</th>
          <th>@lang('DBasic::common.dest')</th>
          <th class="text-end">@lang('DBasic::common.expire')</th>
        </tr>
        @foreach($random_flights as $rf)
          <tr>
            @if($rf->flight)
              <td class="text-start">
                <a href="{{ route('frontend.flights.show', [$rf->flight_id]) }}">{{ optional($rf->flight->airline)->code.' '.$rf->flight->flight_number }}</a>
              </td>
              <td>
                <a href="{{ route('frontend.airports.show', [$rf->flight->dpt_airport_id]) }}" title="{{ optional($rf->flight->dpt_airport)->name }}">{{ $rf->flight->dpt_airport_id }}</a>
              </td>
              <td>
                <a href="{{ route('frontend.airports.show', [$rf->flight->arr_airport_id]) }}" title="{{ optional($rf->flight->arr_airport)->name }}">{{ $rf->flight->arr_airport_id }}</a>
              </td>
              <td class="text-end">
                @if($rf->completed)
                  @lang('DBasic::common.completed')
                  <i class="fas fa-check-circle ms-2 text-success"></i>
                @else
                  {{ $today->endOfDay()->DiffForHumans() }}
                  <i class="fas fa-stopwatch ms-2 text-danger"></i>
                @endif
              </td>
            @else 
              <td colspan="4" class="small fw-bold text-danger">Error: Flight Not Found !</td>
            @endif
          </tr>
        @endforeach
      </table>
    </div>
  </div>
@endif
