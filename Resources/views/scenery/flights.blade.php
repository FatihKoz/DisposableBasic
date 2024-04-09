@extends('app')
@section('title', 'My Sceneries')

@section('content')
  <div class="row">
    <div class="col-lg-10">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            Flights For My Sceneries @if(filled($type)) | {{ ucfirst($type) }} @endif
            <i class="fas fa-book-open float-end"></i>
          </h5>
        </div>
        <div class="card-body table-responsive p-0">
          @if($flights->count() > 0)
            @include('DBasic::scenery.flights_table')
          @else
            <span class="m-1">No flights found compatible with your sceneries</span>
          @endif
        </div>
        <div class="card-footer p-0 px-1 text-end small fw-bold">
          @lang('DBasic::common.paginate', ['first' => $flights->firstItem(), 'last' => $flights->lastItem(), 'total' => $flights->total()])
        </div>
      </div>
      {{ $flights->withQueryString()->links('pagination.default') }}
    </div>
    <div class="col-lg-2">
      <div class="text-start d-grid mb-2">
        <a href="{{ route('DBasic.scenery') }}" class="btn btn-sm btn-success px-1 py-0 mb-2">My Sceneries</a>
        @if(!setting('pilots.only_flights_from_current'))
          <a href="{{ route('DBasic.scenery.flights', ['type' => 'departures']) }}" class="btn btn-sm btn-warning px-1 py-0 mb-2">Search Departure Flights</a>
        @endif
        <a href="{{ route('DBasic.scenery.flights', ['type' => 'arrivals']) }}" class="btn btn-sm btn-warning px-1 py-0 mb-2">Search Arrival Flights</a>
        <a href="{{ route('DBasic.scenery.flights', ['type' => 'trips']) }}" class="btn btn-sm btn-warning px-1 py-0 mb-2">Search Round Trip Flights</a>
      </div>
    </div>
  </div>

  @if(setting('bids.block_aircraft', false))
    <div class="modal fade" id="bidModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBidLabel" aria-hidden="true">
      <div class="modal-dialog mx-auto">
        <div class="modal-content">
          <div class="modal-header border-0 p-1">
            <h5 class="modal-title m-0" id="bidModalLabel">Aircraft Selection</h5>
            <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <select name="bidaircraftdropdown" id="aircraft_select" class="bid_aircraft form-control"></select>
          </div>
          <div class="modal-footer border-0 p-1">
            <button type="button" id="without_aircraft" class="btn btn-sm btn-danger m-0 mx-1 p-0 px-1" data-bs-dismiss="modal">Don't Book Aircraft</button>
            <button type="button" id="with_aircraft" class="btn btn-sm btn-success m-0 mx-1 p-0 px-1" data-bs-dismiss="modal">Book Aircraft</button>
          </div>
        </div>
      </div>
    </div>
  @endif
@endsection

@include('DBasic::scenery.flights_scripts')