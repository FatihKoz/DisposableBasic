@if($is_visible === true)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ $event_text }}
        @if($event_type === 'Upcoming')
          <i class="fas fa-hourglass-half float-end"></i>
        @else
          <i class="fas fa-calendar-check float-end"></i>
        @endif
      </h5>
    </div>
    <div class="card-body text-center table-responsive p-0">
      <table class="table table-borderless table-striped table-sm text-start align-middle mb-0 text-nowrap">
        <tr>
          <th>Flight</th>
          <th class="text-end">Time</th>
          @if($event_type === 'Upcoming')
            <th class="text-end">Date</th>
          @endif
        </tr>
        @foreach($events as $event)
          <tr>
            <td>
              <a href="{{ route('frontend.flights.show', [$event->id]) }}" title="{{ $event->dpt_airport->name.' > '.$event->arr_airport->name }}">
                {{ $event->airline->code.$event->flight_number.' | '.$event->dpt_airport_id.' > '.$event->arr_airport_id }}
              </a>
            </td>
            <td class="text-end">
            @if($event_type != 'Upcoming' && filled($event->dpt_time))
              {{ Carbon::CreateFromFormat('H:i', $event->dpt_time, 'UTC')->diffForHumans().' | ' }}
            @endif
              {{ $event->dpt_time }}
            </td>
            @if($event_type === 'Upcoming')
              <td class="text-end">
                {{ Carbon::CreateFromFormat('Y.m.d H:i', ($event->start_date->format('Y.m.d').' '.$event->dpt_time), 'UTC')->diffForHumans().' | '.$event->start_date->format('d.M') }}
              </td>
            @endif
          </tr>
        @endforeach
      </table>
    </div>
    <div class="card-footer small text-end p-0 pe-1">
      All times are <b>UTC</b>
    </div>
  </div>
@endif
