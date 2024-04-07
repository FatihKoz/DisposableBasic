<table class="table table-sm table-borderless table-striped text-start mb-0">
  <tr>
    <th>@sortablelink('airport_id', 'ICAO')</th>
    <th>IATA</th>
    <th>Name</th>
    <th>@sortablelink('region', 'Region')</th>
    <th>@sortablelink('simulator', 'Simulator')</th>
    <th>@sortablelink('notes', 'Notes')</th>
    <th class="text-center">Departure Flights</th>
    <th class="text-center">Arrival Flights</th>
    <th class="text-end">Actions</th>
  </tr>
  @foreach($sceneries as $scenery)
    <tr>
      <td>
        @if($scenery->departures_count > 0 || $scenery->arrivals_count > 0)
          <a href="{{ route('frontend.airports.show', [$scenery->airport_id]) }}">{{ $scenery->airport_id }}</a>
        @else
          {{ $scenery->airport_id }}
        @endif
      </td>
      <td>{{ optional($scenery->airport)->iata ?? '-' }}</td>
      <td>
        @if($scenery->airport)
          <img class="img-mh25 me-1" title="{{ strtoupper($scenery->airport->country) }}" src="{{ public_asset('/image/flags_new/'.strtolower($scenery->airport->country).'.png') }}" alt=""/>
          {{ $scenery->airport->name }}
        @else
          -
        @endif
      </td>
      <td>{{ DB_DecodeRegion($scenery->region) }}</td>
      <td>{{ DB_DecodeSimulator($scenery->simulator) }}</td>
      <td>{{ $scenery->notes }}</td>
      <td class="text-center">{{ $scenery->departures_count }}</td>
      <td class="text-center">{{ $scenery->arrivals_count }}</td>
      <td class="text-end">
        <form action="{{ route('DBasic.scenery.delete') }}" method="POST">
          @csrf
          <input name="id" type="hidden" value="{{ $scenery->id }}">
          <input name="user_id" type="hidden" value="{{ $user_id }}">
          <input name="airport_id" type="hidden" value="{{ $scenery->airport_id }}">
          <button type="submit" class="btn btn-sm btn-danger px-1 py-0">Delete</button>
        </form>
      </td>
    </tr>
  @endforeach
</table>
