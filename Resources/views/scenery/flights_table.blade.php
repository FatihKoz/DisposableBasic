<table class="table table-sm table-borderless table-striped text-start text-nowrap align-middle mb-0">
  <tr>
    <th>@sortablelink('airline_id', 'Airline')</th>
    <th>@sortablelink('flight_number', 'Flight No')</th>
    <th>@sortablelink('route_code', 'Code')</th>
    <th>@sortablelink('route_leg', 'Leg #')</th>
    <th>@sortablelink('dpt_airport_id', 'Origin')</th>
    <th class="text-center">STD</th>
    <th class="text-center">STA</th>
    <th>@sortablelink('arr_airport_id', 'Destination')</th>
    <th class="text-center">@sortablelink('distance', 'Distance')</th>
    <th class="text-center">@sortablelink('flight_time', 'Block Time')</th>
    <th class="text-end">Actions</th>
  </tr>
  @foreach($flights as $flight)
    <tr>
      <td>
        <a href="{{ route('DBasic.airline', [optional($flight->airline)->icao ?? '']) }}">
          {{ optional($flight->airline)->name }}
        </a>
      </td>
      <td>
        <a href="{{ route('frontend.flights.show', [$flight->id]) }}">
          {{ optional($flight->airline)->code.' '.$flight->flight_number }}
        </a>
      </td>
      <td>{{ $flight->route_code }}</td>
      <td>{{ $flight->route_leg }}</td>
      <td>
        <img class="img-mh25 me-1" title="{{ strtoupper(optional($flight->dpt_airport)->country) }}" src="{{ public_asset('/image/flags_new/'.strtolower(optional($flight->dpt_airport)->country).'.png') }}" alt=""/>
        <a href="{{ route('frontend.airports.show', [$flight->dpt_airport_id]) }}">{{ optional($flight->dpt_airport)->full_name ?? $flight->dpt_airport_id }}</a>
      </td>
      <td class="text-center">{{ DB_FormatScheduleTime($flight->dpt_time) }}</td>
      <td class="text-center">{{ DB_FormatScheduleTime($flight->arr_time) }}</td>
      <td>
        <img class="img-mh25 me-1" title="{{ strtoupper(optional($flight->arr_airport)->country) }}" src="{{ public_asset('/image/flags_new/'.strtolower(optional($flight->arr_airport)->country).'.png') }}" alt=""/>
        <a href="{{ route('frontend.airports.show', [$flight->arr_airport_id]) }}">{{ optional($flight->arr_airport)->full_name ?? $flight->arr_airport_id }}</a>
      </td>
      <td class="text-center">{{ $flight->distance->local(0).' '.$units['distance'] }}</td>
      <td class="text-center">{{ DB_ConvertMinutes($flight->flight_time) }}</td>
      <td class="text-end">
        @if(!setting('pilots.only_flights_from_current') || $flight->dpt_airport_id == $user->curr_airport_id)
          {{-- Bid --}}
          @if(setting('bids.allow_multiple_bids') === true || setting('bids.allow_multiple_bids') === false && count($saved) === 0)
            <button class="btn btn-sm m-0 mx-1 p-0 px-1 save_flight {{ isset($saved[$flight->id]) ? 'btn-danger':'btn-success' }}"
                  x-id="{{ $flight->id }}"
                  x-saved-class="btn-danger"
                  type="button" title="Add/Remove Bid">
              <i class="fas fa-map-marker"></i>
            </button>
          @endif
          {{-- Simbrief --}}
          @if($simbrief !== false && $flight->simbrief && $flight->simbrief->user_id === $user->id)
            <a href="{{ route('frontend.simbrief.briefing', $flight->simbrief->id) }}" class="btn btn-sm m-0 mx-1 p-0 px-1 btn-secondary">
              <i class="fas fa-file-pdf"  title="View SimBrief OFP"></i>
            </a>
          @elseif($simbrief !== false && ($simbrief_bids === false || $simbrief_bids === true && isset($saved[$flight->id])))
            @php
              $aircraft_id = isset($saved[$flight->id]) ? App\Models\Bid::find($saved[$flight->id])->aircraft_id : null;
            @endphp
            <a href="{{ route('frontend.simbrief.generate') }}?flight_id={{ $flight->id }}@if($aircraft_id)&aircraft_id={{ $aircraft_id }} @endif" class="btn btn-sm m-0 mx-1 p-0 px-1 {{ isset($saved[$flight->id]) ? 'btn-success':'btn-primary' }}">
              <i class="fas fa-file-pdf" title="Generate SimBrief OFP"></i>
            </a>
          @endif
          {{-- vmsAcars Load --}}
          @if($acars_plugin && isset($saved[$flight->id]))
            <a href="vmsacars:bid/{{ $saved[$flight->id] }}" class="btn btn-sm m-0 mx-1 p-0 px-1 btn-warning">
              <i class="fas fa-file-download" title="Load in vmsAcars"></i>
            </a>
          @elseif($acars_plugin)
            <a href="vmsacars:flight/{{ $flight->id }}" class="btn btn-sm m-0 mx-1 p-0 px-1 btn-warning">
              <i class="fas fa-file-download" title="Load in vmsAcars"></i>
            </a>
          @endif
          @if(Theme::getSetting('pireps_manual'))
            <a href="{{ route('frontend.pireps.create', ['flight_id' => $flight->id]) }}" class="btn btn-sm btn-info m-0 mx-1 p-0 px-1">
              <i class="fas fa-file-upload" title="New Manual PIREP"></i>
            </a>
          @endif
        @endif
        {{-- v7 core Search Page --}}
        <a href="{{ route('frontend.flights.search', ['airline_id' => $flight->airline_id, 'flight_number' => $flight->flight_number, 'dep_icao' => $flight->dpt_airport_id, 'arr_icao' => $flight->arr_airport_id]) }}" class="btn btn-sm btn-danger m-0 mx-1 p-0 px-1">
          <i class="fas fa-search" title="v7 Search"></i>
        </a>
      </td>
    </tr>
  @endforeach
</table>