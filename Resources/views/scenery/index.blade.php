@extends('app')
@section('title', 'My Sceneries')

@section('content')
  <div class="row">
    <div class="col-lg-9">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            My Sceneries
            <i class="fas fa-book-open float-end"></i>
          </h5>
        </div>
        <div class="card-body table-responsive p-0">
          @if($sceneries->count() > 0)
            @include('DBasic::scenery.table')
          @else
            <span class="m-1">No sceneries found for this user</span>
          @endif
        </div>
        <div class="card-footer p-0 px-1 text-end small fw-bold">
          @lang('DBasic::common.paginate', ['first' => $sceneries->firstItem(), 'last' => $sceneries->lastItem(), 'total' => $sceneries->total()])
        </div>
      </div>
      {{ $sceneries->withQueryString()->links('pagination.default') }}
    </div>
    <div class="col-lg-3">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">Add Scenery</h5>
        </div>
        <form action="{{ route('DBasic.scenery.store') }}" method="POST">
          @csrf
          <div class="card-body p-1">
            <div class="form-floating mb-1">
              <input class="form-control" type="text" name="airport_id" id="formairport" pattern="[A-Za-z]{4}" title="Four letter ICAO code">
              <label for="formairport">Airport ICAO Code</label>
            </div>
            <div class="form-floating mb-1">
              <select class="form-select" name="region" id="formregion">
                @foreach($regions as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
              <label for="formregion">Airport Region (Optional)</label>
            </div>
            <div class="form-floating mb-1">
              <select class="form-select" name="simulator" id="formsimulator">
                @foreach($simulators as $key => $value)
                  <option value="{{ $key }}">{{ $value }}</option>
                @endforeach
              </select>
              <label for="formsimulator">Simulator (Optional)</label>
            </div>
            <div class="form-floating mb-0">
              <textarea class="form-control" name="notes" id="formnotes" style="height: 70px"></textarea>
              <label for="formnotes">Notes (Optional)</label>
            </div>
          </div>
          <div class="card-footer p-1 text-end small fw-bold">
            <input name="user_id" type="hidden" value="{{ $user_id }}">
            <button type="submit" class="btn btn-sm btn-success px-1 py-0">Save Scenery</button>
          </div>
        </form>
      </div>
      @if($sceneries->count() > 0)
        <div class="text-start mb-2">
          @widget('DBasic::Map', ['source' => 'scenery'])
        </div>
      @endif
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">Flight Counts</h5>
        </div>
        <div class="card-body table-responsive p-0">
          <table class="table table-sm table-striped table-secondary mb-0">
            <tr>
              <th>Departures from My Sceneries</th>
              <td class="text-end">{{ $flights['deps'] }}</td>
            </tr>
            <tr>
              <th>Arrivals to My Sceneries</th>
              <td class="text-end">{{ $flights['arrs'] }}</td>
            </tr>
            <tr>
              <th>Trips between My Sceneries</th>
              <td class="text-end">{{ $flights['trip'] }}</td>
            </tr>
          </table>
        </div>
        <div class="card-footer p-1 text-end small fw-bold">
          Only active and visible flights are considered.
        </div>
      </div>
      <div class="text-start mb-2">
        @if(!setting('pilots.only_flights_from_current'))
          <a href="{{ route('DBasic.scenery.flights', ['type' => 'departures']) }}" class="btn btn-sm btn-warning px-1 py-0">Search Departure Flights</a>
        @endif
        <a href="{{ route('DBasic.scenery.flights', ['type' => 'arrivals']) }}" class="btn btn-sm btn-warning px-1 py-0">Search Arrival Flights</a>
        <a href="{{ route('DBasic.scenery.flights', ['type' => 'trips']) }}" class="btn btn-sm btn-warning px-1 py-0">Search Round Trip Flights</a>
      </div>
      @if(count($user_regs) > 1)
        <div class="text-end mb-2">
          @foreach($user_regs as $reg)
            <a href="{{ route('DBasic.scenery', ['region' => $reg]) }}" class="btn btn-sm btn-warning px-1 py-0">{{ DB_DecodeRegion($reg) }}</a>
          @endforeach
        </div>
      @endif
      @if(count($user_sims) > 1)
        <div class="text-end mb-2">
          @foreach($user_sims as $sim)
            <a href="{{ route('DBasic.scenery', ['sim' => $sim]) }}" class="btn btn-sm btn-warning px-1 py-0">{{ DB_DecodeSimulator($sim) }}</a>
          @endforeach
        </div>
      @endif
      @if(count($user_sims) > 1 || count($user_regs) > 1)
        <div class="text-end mb-2">
          <a href="{{ route('DBasic.scenery') }}" class="btn btn-sm btn-danger px-1 py-0">Reset Filters</a>
        </div>
      @endif
    </div>
  </div>
@endsection
