<table class="table table-sm table-borderless table-striped text-start align-middle mb-0">
  <thead class="table-dark">
    <tr>
      <td>Flight #</td>
      <td>Origin</td>
      <td>Destination</td>
      <td>Aircraft</td>
      <td>Pilot In Command</td>
    </tr>
  </thead>
  <tbody>
    @foreach($pireps as $pirep)
    <tr>
      <td>{{ $pirep->flight_number }}</td>
      <td>{{ $pirep->dpt_airport->name }}</td>
      <td>{{ $pirep->arr_airport->name }}</td>
      <td>{{ $pirep->aircraft->registration }}</td>
      <td>{{ $pirep->user->name_private }}</td>
    </tr>
    @endforeach
  </tbody>
</table>
