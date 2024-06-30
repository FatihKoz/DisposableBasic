@extends('app')
@section('title', 'VATSIM Audit Records')

@section('content')
  <div class="row">
    <div class="col-8">
      <div class="card mb-2">
        <div class="card-header p-0">
          <h5 class="m-1 p-1">Accepted Pireps</h5>
        </div>
        <div class="card-body table-responsive overflow-auto p-0" style="max-height: 70vh;">
          <table class="table table-sm table-borderless table-striped text-center mb-0">
            <tr>
              <th class="text-start">Callsign</th>
              <th>Orig</th>
              <th>Dest</th>
              <th>Date</th>
              <th>Off Block</th>
              <th>On Block</th>
              <th>Aircraft</th>
              <th class="text-end">Remarks</th>
            </tr>
            @foreach($audit_pireps as $p)
              <tr>
                <td class="text-start">{{ $p->fields->firstWhere('slug', 'network-callsign-used')->value ?? $p->airline->icao.$p->flight_number }}</td>
                <td>{{ $p->dpt_airport_id }}</td>
                <td>{{ $p->arr_airport_id }}</td>
                <td>{{ $p->submitted_at->format('d.M.Y') }}</td>
                <td>{{ $p->block_off_time->format('H:i') }}</td>
                <td>{{ $p->block_on_time->format('H:i') }}</td>
                <td>{{ $p->aircraft->icao }}</td>
                <td class="text-end">{{ optional($p->fields->firstWhere('slug', 'network-presence-check'))->value.'%' }}</td>
              </tr>
            @endforeach
          </table>
        </div>
        <div class="card-footer p-1 text-end small fw-bold">
          @if($is_admin)
            <form class="form" method="post" action="{{ route('DBasic.audit.export') }}">
              @csrf
              <input type="hidden" name="network" value="vatsim">
              <input type="hidden" name="start" value="{{ $audit_start }}">
              <input type="hidden" name="end" value="{{ $audit_end }}">
              <input type="hidden" name="pireps" value=" {{ $audit_pids }}">
              <input class="btn btn-success btn-sm mx-1 p-0 px-1 float-start" type="submit" value="Export Pireps">
            </form>
          @endif
          @if(filled($audit_pireps) && $audit_pireps->count() > 0)
            Displaying {{ $audit_pireps->count() }} PIREPs submitted between {{ $audit_start->format('d.M.Y H:i') }} and {{ $audit_end->format('d.M.Y H:i') }}
          @endif
        </div>
      </div>
      <span class="small float-start">Audit report prepared in {{ round(microtime(true) - LARAVEL_START, 2) }} seconds</span>
    </div>
    @if($is_admin)
      {{-- Active Network Members --}}
      <div class="col-2">
        <div class="card mb-2">
          <div class="card-header p-0">
            <h5 class="m-1 p-1">VATSIM Pilots (Active)</h5>
          </div>
          <div class="card-body table-responsive overflow-auto p-0" style="max-height: 70vh;">
            <table class="table table-sm table-borderless table-striped mb-0">
              <tr>
                <th>Network ID</th>
                <th class="text-end">Member Since</th>
              </tr>
              @foreach($audit_pilots as $u)
                <tr>
                  <td>{{ optional($u->fields->firstWhere('name', $field_name))->value }}</td>
                  <td class="text-end">{{ $u->created_at->format('M Y') }}</td>
                </tr>
              @endforeach
            </table>
          </div>
          <div class="card-footer p-1 text-end small fw-bold">
            @if(filled($audit_pilots) && $audit_pilots->count() > 0)
              Listing {{ $audit_pilots->count() }} active members
            @endif
          </div>
        </div>
      </div>
      {{-- All Network Members --}}
      <div class="col-2">
        <div class="card mb-2">
          <div class="card-header p-0">
            <h5 class="m-1 p-1">VATSIM Pilots (All)</h5>
          </div>
          <div class="card-body table-responsive overflow-auto p-0" style="max-height: 70vh;">
            <table class="table table-sm table-borderless table-striped mb-0">
              <tr>
                <th>Network ID</th>
                <th class="text-end">Member Since</th>
              </tr>
              @foreach($network_pilots as $u)
                <tr>
                  <td>{{ optional($u->fields->firstWhere('name', $field_name))->value }}</td>
                  <td class="text-end">{{ $u->created_at->format('M Y') }}</td>
                </tr>
              @endforeach
            </table>
          </div>
          <div class="card-footer p-1 text-end small fw-bold">
            @if(filled($network_pilots) && $network_pilots->count() > 0)
              Listing {{ $network_pilots->count() }} members
            @endif
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection
