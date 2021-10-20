<table class="table table-sm table-borderless table-striped text-start align-middle mb-0">
  <thead class="table-dark">
    <tr>
      <td>Name</td>
      <td>Rank</td>
      <td>Base</td>
      <td>Location</td>
      <td>Flight Time</td>
    </tr>
  </thead>
  <tbody>
    @foreach($users as $pilot)
    <tr>
      <td>{{ $pilot->name_private }}</td>
      <td>{{ $pilot->rank->name }}</td>
      <td>{{ $pilot->home_airport->name ?? $pilot->home_airport_id }}</td>
      <td>{{ $pilot->curr_airport->name ?? $pilot->curr_airport_id }}</td>
      <td>{{ $pilot->flight_time }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
