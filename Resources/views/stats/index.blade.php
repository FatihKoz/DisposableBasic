@extends('app')
@section('title', __('DBasic::stats.stats'))

@section('content')
  {{-- BEGIN OVERALL STATS --}}
    <div class="row row-cols-3">
      {{-- LEFT --}}
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DBasic::stats.stats_gen')
              <i class="fas fa-sitemap float-end m-1"></i>
            </h5>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-borderless table-striped align-middle mb-0">
              @foreach($stats_basic as $key => $value)
                <tr>
                  <th class="text-start">{{ $key }}</th>
                  <td class="text-end">{{ $value }}</td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
      {{-- MIDDLE --}}
      <div class="col">
        @if($departures->count() > 0 && $arrivals->count() > 0)
          <div class="card mb-2">
            <div class="card-header p-1">
              <h5 class="m-1">
                Top Departures
                <i class="fas fa-plane-departure float-end m-1"></i>
              </h5>
            </div>
            <div class="card-body p-0 table-responsive">
              {{ $departures }}
            </div>
          </div>

          <div class="card mb-2">
            <div class="card-header p-1">
              <h5 class="m-1">
                Top Arrivals
                <i class="fas fa-plane-arrival float-end m-1"></i>
              </h5>
            </div>
            <div class="card-body p-0">
            {{ $arrivals }}
            </div>
          </div>
        @endif
      </div>
      {{-- RIGHT --}}
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DBasic::stats.stats_rep')
              <i class="fas fa-file-upload float-end m-1"></i>
            </h5>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-borderless table-striped align-middle mb-0">
              @foreach($stats_pirep as $key => $value)
                <tr>
                  <th class="text-start">{{ $key }}</th>
                  <td class="text-end">{{ $value }}</td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      </div>
    </div>
  {{-- END OVERALL STATS --}}
@endsection
