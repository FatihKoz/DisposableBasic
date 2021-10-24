@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ $title }}
        <i class="fas fa-file-signature float-end m-1"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-borderless table-sm table-striped text-start align-middle mb-0">
        <tr>
          @if(!$bids)
            <th>@lang('DBasic::common.aircraft')</th>
          @endif
          <th>@lang('DBasic::common.flightno')</th>
          <th>@lang('DBasic::common.orig_abr')</th>
          <th>@lang('DBasic::common.dest_abr')</th>
          <th>@lang('DBasic::common.pilot')</th>
          <th class="text-end">@lang('DBasic::common.expire')</th>
        </tr>
        @foreach($active_bookings as $booking)
          <tr>
            @if(!$bids)
              <td>
                <a href="{{ route('DBasic.aircraft', [$booking->aircraft->registration]) }}" title="{{ $booking->aircraft->name }}">{{ $booking->aircraft->ident }}</a>
              </td>
            @endif
            <td>
              <a href="{{ route('frontend.flights.show', [$booking->flight_id]) }}" title="{{ $booking->flight->ident }}">{{ $booking->flight->airline->code.' '.$booking->flight->flight_number }}</a>
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
            <td class="text-end">
              {{ $booking->created_at->addHours($expire)->diffForHumans() }}
            </td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
@endif
