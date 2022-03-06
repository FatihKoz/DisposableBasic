<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      Online Pilots | {{ $network }}
      <i class="fas fa-user-friends float-end"></i>
    </h5>
  </div>
  @if(isset($pilots))
    <div class="card-body p-0 table-responsive">
      @if(count($pilots) > 0)
        <table class="table table-borderless table-sm table-striped text-center text-nowrap mb-0">
          <tr>
            <th class="text-start">Name</th>
            <th>{{ $network }} ID</th>
            <th>Callsign</th>
            <th>ATC Flight Plan</th>
            <th>Server</th>
            <th class="text-end">Time Online</th>
          </tr>
          @foreach($pilots as $pilot)
            <tr>
              <td class="text-start">
                @if(isset($pilot['user_id']))
                  <a href="{{ route('frontend.profile.show', [$pilot['user_id']]) }}" target="_blank">{{ $pilot['name_private'] }}</a>
                @else
                  Deleted User
                @endif
              </td>
              <td>{{ $pilot['network_id'] }}</td>
              <td>{{ $pilot['callsign'] }}</td>
              <td>{{ $pilot['flightplan'] ?? 'ATC not filed...' }}</td>
              <td>{{ $pilot['server_name'] }}</td>
              <td class="text-end">
                @if($checks)
                  @if(!$pilot['airline'])
                    <i class="fas fa-exclamation-circle text-danger mx-1" title="Airline Not Found!"></i>
                  @endif
                  @if(!$pilot['pirep'])
                    <i class="fas fa-clipboard text-danger mx-1" title="Pirep Not Found!"></i>
                  @elseif($pilot['pirep'])
                    <i class="fas fa-clipboard-check text-success mx-1" title="{{ $pilot['pirep']->aircraft->icao.' | '.$pilot['pirep']->dpt_airport_id.' > '.$pilot['pirep']->arr_airport_id }}"></i>
                  @endif
                @endif
                {{ DB_ConvertMinutes($pilot['online_time']) }}
              </td>
            </tr>
          @endforeach
        </table>
      @else
        <span class="text-danger mx-1 fw-bold">No {{ $network }} Online Flights Found</span>
      @endif
    </div>
  @elseif(isset($error))
    <div class="card-body p-0">
      <span class="fw-bold text-danger mx-1">{{ $error }}</span>
    </div>
  @endif
  @if(isset($dltime))
    <div class="card-footer p-0 text-end fw-bold small">
      @if($checks)
        <span class="mx-1 float-start" title="Detailed Checks Enabled">*</span>
      @endif
      <span class="mx-1">{{ $dltime->diffForHumans() }}</span>
    </div>
  @endif
</div>
