@extends('app')
@section('title', __('DBasic::common.hdetails'))

@section('content')
  <div class="row">
    {{-- Hub Data and Sub Navigation Buttons --}}
    <div class="col-5">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            {{ $hub->name ?? $hub->id }}
            <i class="fas fa-info float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped align-middle text-start mb-0">
            <tr>
              <th>@lang('DBasic::common.icao')</th>
              <td class="text-end">{{ $hub->id }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.iata')</th>
              <td class="text-end">{{ $hub->iata ?? '--' }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.location')</th>
              <td class="text-end">{{ $hub->location }}</td>
            </tr>
            <tr>
              <th>@lang('common.country')</th>
              <td class="text-end">
                @if(strlen($hub->country) == 2)
                  {{ $country->alpha2($hub->country)['name'] }} ({{ $hub->country }})
                @else
                  {{ $hub->country }}
                @endif
              </td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.timezone')</th>
              <td class="text-end">{{ $hub->timezone }}</a></td>
            </tr>
            @if($hub->ground_handling_cost > 0)
              <tr>
                <th>@lang('DBasic::common.groundhc')</th>
                <td class="text-end">{{ number_format($hub->ground_handling_cost).' '.$units['currency'] }}</td>
              </tr>
            @endif
            @if($hub->fuel_100ll_cost > 0)
              <tr>
                <th>@lang('DBasic::common.fuelc') | 100LL</th>
                <td class="text-end">{{ number_format($hub->fuel_100ll_cost, 3).' '.$units['currency'] }}</tr>
            @endif
            @if($hub->fuel_mogas_cost > 0)
              <tr>
                <th>@lang('DBasic::common.fuelc') | MOGAS</th>
                <td class="text-end">{{ number_format($hub->fuel_mogas_cost, 3).' '.$units['currency'] }}</td>
              </tr>
            @endif
            @if($hub->fuel_jeta_cost > 0)
              <tr>
                <th>@lang('DBasic::common.fuelc') | JETA1</th>
                <td class="text-end">{{ number_format($hub->fuel_jeta_cost, 3).' '.$units['currency'] }}</td>
              </tr>
            @endif
          </table>
        </div>
      </div>
      {{-- Sub Navigation Pills --}}
      <ul class="nav nav-pills nav-fill mb-2" id="pills-Hub" role="tablist">
        @if($is_visible['pilots'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link" id="pilots-tab" data-bs-toggle="pill" data-bs-target="#pilots" type="button" role="tab" aria-controls="pilots" aria-selected="false">
              @lang('DBasic::common.pilots')
            </button>
          </li>
        @endif
        @if($is_visible['aircraft'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link" id="aircraft-tab" data-bs-toggle="pill" data-bs-target="#aircraft" type="button" role="tab" aria-controls="aircraft" aria-selected="false">
              @lang('DBasic::common.aircraft')
            </button>
          </li>
        @endif
        @if($is_visible['flights'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link" id="flights-tab" data-bs-toggle="pill" data-bs-target="#flights" type="button" role="tab" aria-controls="flights" aria-selected="false">
              @lang('DBasic::common.flights')
            </button>
          </li>
        @endif
        @if($is_visible['reports'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">
              @lang('DBasic::common.pireps')
            </button>
          </li>
        @endif
        @if($is_visible['downloads'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link" id="downloads-tab" data-bs-toggle="pill" data-bs-target="#downloads" type="button" role="tab" aria-controls="downloads" aria-selected="false">
              @lang('DBasic::common.downloads')
            </button>
          </li>
        @endif
      </ul>
    </div>
    <div class="col-7">
      {{-- Hub Map --}}
      <div class="card mb-2">
        {{ Widget::AirspaceMap(['width' => '100%', 'height' => '400px', 'lat' => $hub->lat, 'lon' => $hub->lon,]) }}
      </div>
    </div>
  </div>

  <div class="tab-content" id="pills-HubContent">
    @if($is_visible['pilots'])
      <div class="tab-pane fade" id="pilots" role="tabpanel" aria-labelledby="pilots-tab">
        @include('DBasic::hubs.show_pilots')
      </div>
    @endif
    @if($is_visible['aircraft'])
      <div class="tab-pane fade" id="aircraft" role="tabpanel" aria-labelledby="aircraft-tab">
        @include('DBasic::hubs.show_fleet')
      </div>
    @endif
    @if($is_visible['flights'])
      <div class="tab-pane fade" id="flights" role="tabpanel" aria-labelledby="flights-tab">
        @include('DBasic::hubs.show_flights')
      </div>
    @endif
    @if($is_visible['reports'])
      <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
        @include('DBasic::hubs.show_reports')
      </div>
    @endif
    @if($is_visible['downloads'])
      <div class="tab-pane fade" id="downloads" role="tabpanel" aria-labelledby="downloads-tab">
        <div class="row row-cols-2">
          <div class="col">
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1">
                  {{ trans_choice('DBasic::common.hub', 1) }} {{ trans_choice('common.download',2) }}
                  <i class="fas fa-download float-end m-1"></i>
                </h5>
              </div>
              <div class="card-body p-0 table-responsive">
                @include('downloads.table', ['files' => $hub->files])
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
@endsection
