@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ $title }}
        <i class="fas fa-file-signature float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 overflow-auto table-responsive">
      <table class="table table-borderless table-sm table-striped text-start text-nowrap align-middle mb-0">
        <tr>
          @if(!$bids)
            <th>@lang('DBasic::common.aircraft')</th>
          @endif
          <th>@lang('DBasic::common.flightno')</th>
          <th>@lang('DBasic::common.orig_abr')</th>
          <th>@lang('DBasic::common.dest_abr')</th>
          <th>@lang('DBasic::common.pilot')</th>
          @if(!$bids)
            <th>ETD</th>
          @endif
          <th class="text-end">@lang('DBasic::common.expire')</th>
        </tr>
        @foreach($active_bookings as $booking)
          @if($booking->flight)
            <tr>
              @if(!$bids)
                <td>
                  <a href="{{ route('DBasic.aircraft', [$booking->aircraft->registration]) }}" title="{{ optional($booking->aircraft)->name }}">{{ optional($booking->aircraft)->ident }}</a>
                </td>
              @endif
              <td>
                <a href="{{ route('frontend.flights.show', [$booking->flight_id]) }}" title="{{ $booking->flight->ident }}">{{ optional($booking->flight->airline)->code.' '.$booking->flight->flight_number }}</a>
              </td>
              <td>
                <a href="{{ route('frontend.airports.show', [$booking->flight->dpt_airport_id]) }}" title="{{ optional($booking->flight->dpt_airport)->name }}">{{ $booking->flight->dpt_airport_id }}</a>
              </td>
              <td>
                <a href="{{ route('frontend.airports.show', [$booking->flight->arr_airport_id]) }}" title="{{ optional($booking->flight->arr_airport)->name }}">{{ $booking->flight->arr_airport_id }}</a>
              </td>
              <td>
                <a href="{{ route('frontend.profile.show', [$booking->user_id]) }}">{{ $booking->user->name_private }}</a>
              </td>
              @if(!$bids)
                <td><b>{{ date('H:i', $booking->xml->times->est_out->__toString()) }}</b></td>
              @endif
              <td class="text-end">
                @ability('admin', 'admin-access')
                  @if(!$bids) 
                    <a href="{{ route('frontend.simbrief.briefing', [$booking->id]) }}" target="_blank">
                      <i class="fas fa-info-circle text-secondary" title="Click to view Briefing"></i>
                    </a>
                  @endif
                @endability
                {{ $booking->created_at->addHours($expire)->diffForHumans() }}
              </td>
            </tr>
          @endif
        @endforeach
      </table>
    </div>
  </div>
@endif
