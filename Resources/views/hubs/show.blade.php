@extends('app')
@section('title', __('DBasic::common.hdetails'))

@section('content')
  <div class="row">
    {{-- Hub Data and Sub Navigation --}}
    <div class="col-lg-5">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            {{ $hub->name }}
            <i class="fas fa-info float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped align-middle text-start text-nowrap mb-0">
            <tr>
              <th>@lang('DBasic::common.icao')</th>
              <td class="text-end">{{ $hub->icao }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.iata')</th>
              <td class="text-end">{{ $hub->iata ?? '--' }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.location')</th>
              <td class="text-end">
                {{ $hub->location }}
                @if (strlen($hub->country) == 2)
                  {{ ' | '.$country->alpha2($hub->country)['name'].' ('.$hub->country.')' }}
                @else
                  {{ ' | '.$hub->country }}
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
                <td class="text-end">{{ DB_FuelCost($hub->fuel_100ll_cost, $units['fuel'], $units['currency']) }}</tr>
            @endif
            @if($hub->fuel_mogas_cost > 0)
              <tr>
                <th>@lang('DBasic::common.fuelc') | MOGAS</th>
                <td class="text-end">{{ DB_FuelCost($hub->fuel_mogas_cost, $units['fuel'], $units['currency']) }}</td>
              </tr>
            @endif
            @if($hub->fuel_jeta_cost > 0)
              <tr>
                <th>@lang('DBasic::common.fuelc') | JETA1</th>
                <td class="text-end">{{ DB_FuelCost($hub->fuel_jeta_cost, $units['fuel'], $units['currency']) }}</td>
              </tr>
            @endif
          </table>
        </div>
        @if(filled($sundetails))
          <div class="card-footer p-1 fw-bold small text-center">
            @if(isset($sundetails['twilight_begin']))
              <i class="fas fa-cloud-sun mx-2" title="@lang('DBasic::widgets.twilight_begin')"></i>{{ $sundetails['twilight_begin'] }}
            @endif
            @if(isset($sundetails['sunrise']))
              <i class="fas fa-sun mx-2" title="@lang('DBasic::widgets.sunrise')"></i>{{ $sundetails['sunrise'] }}
            @endif
            @if(isset($sundetails['sunset']))
              <i class="fas fa-moon mx-2" title="@lang('DBasic::widgets.sunset')"></i>{{ $sundetails['sunset'] }}
            @endif
            @if(isset($sundetails['twilight_end']))
              <i class="fas fa-cloud-moon mx-2" title="@lang('DBasic::widgets.twilight_end')"></i>{{ $sundetails['twilight_end'] }}
            @endif
          </div>
        @endif
      </div>
      @if($is_visible['flights'])
        <div class="mb-2">
          @widget('DBasic::Map', ['source' => $hub->id])
        </div>
      @endif
      {{-- Sub Navigation Pills --}}
      <ul class="nav nav-pills nav-fill mb-2" id="pills-Hub" role="tablist">
        @if(filled($hub->notes))
          <li class="nav-item mx-1 mb-1" role="presentation">
            <button class="nav-link p-0 px-1" id="notes-tab" data-bs-toggle="pill" data-bs-target="#notes" type="button" role="tab" aria-controls="notes" aria-selected="true">
              @lang('DBasic::common.notes')
            </button>
          </li>
        @endif
        @if($is_visible['pilots'])
          <li class="nav-item mx-1 mb-1" role="presentation">
            <button class="nav-link p-0 px-1" id="pilots-tab" data-bs-toggle="pill" data-bs-target="#pilots" type="button" role="tab" aria-controls="pilots" aria-selected="false">
              @lang('DBasic::common.pilots')
            </button>
          </li>
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link p-0 px-1" id="leaderboard-tab" data-bs-toggle="pill" data-bs-target="#leaderboard" type="button" role="tab" aria-controls="leaderboard" aria-selected="false">
              @lang('DBasic::widgets.leader_board')
            </button>
          </li>
        @endif
        @if($is_visible['aircraft'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link p-0 px-1" id="aircraft-tab" data-bs-toggle="pill" data-bs-target="#aircraft" type="button" role="tab" aria-controls="aircraft" aria-selected="false">
              @lang('DBasic::common.aircraft')
            </button>
          </li>
        @endif
        @if($is_visible['flights'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link p-0 px-1" id="flights-tab" data-bs-toggle="pill" data-bs-target="#flights" type="button" role="tab" aria-controls="flights" aria-selected="false">
              @lang('DBasic::common.flights')
            </button>
          </li>
        @endif
        @if($is_visible['reports'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link p-0 px-1" id="reports-tab" data-bs-toggle="pill" data-bs-target="#reports" type="button" role="tab" aria-controls="reports" aria-selected="false">
              @lang('DBasic::common.pireps')
            </button>
          </li>
        @endif
        @if($is_visible['downloads'])
          <li class="nav-item mx-1" role="presentation">
            <button class="nav-link p-0 px-1" id="downloads-tab" data-bs-toggle="pill" data-bs-target="#downloads" type="button" role="tab" aria-controls="downloads" aria-selected="false">
              @lang('DBasic::common.downloads')
            </button>
          </li>
        @endif
      </ul>
    </div>
    <div class="col-lg-7">
      {{-- Hub Map --}}
      <div class="card mb-2">
        {{ Widget::AirspaceMap(['width' => '100%', 'height' => '400px', 'lat' => $hub->lat, 'lon' => $hub->lon,]) }}
      </div>
    </div>
  </div>

  <div class="tab-content" id="pills-HubContent">
    @if (filled($hub->notes))
      <div class="tab-pane fade show active" id="notes" role="tabpanel" aria-labelledby="notes-tab">
        <div class="row row-cols-lg-2">
          <div class="col-lg">
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1">
                  @lang('DBasic::common.notes')
                  <i class="fas fa-info-circle float-end"></i>
                </h5>
              </div>
              <div class="card-body p-1 table-responsive">
                {!! $hub->notes !!}
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
    @if ($is_visible['pilots'])
      <div class="tab-pane fade" id="pilots" role="tabpanel" aria-labelledby="pilots-tab">
        @include('DBasic::hubs.show_pilots')
      </div>
      <div class="tab-pane fade" id="leaderboard" role="tabpanel" aria-labelledby="leaderboard-tab">
        <div class="row">
          <div class="col-lg">
            @widget('DBasic::LeaderBoard', ['hub' => $hub->id, 'source' => 'pilot', 'count' => 5, 'type' => 'flights'])
          </div>
          <div class="col-lg">
            @widget('DBasic::LeaderBoard', ['hub' => $hub->id, 'source' => 'pilot', 'count' => 5, 'type' => 'time'])
          </div>
          <div class="col-lg">
            @widget('DBasic::LeaderBoard', ['hub' => $hub->id, 'source' => 'pilot', 'count' => 5, 'type' => 'lrate'])
          </div>
        </div>
      </div>
    @endif
    @if ($is_visible['aircraft'])
      <div class="tab-pane fade" id="aircraft" role="tabpanel" aria-labelledby="aircraft-tab">
        @include('DBasic::hubs.show_fleet')
      </div>
    @endif
    @if ($is_visible['flights'])
      <div class="tab-pane fade" id="flights" role="tabpanel" aria-labelledby="flights-tab">
        @include('DBasic::hubs.show_flights')
      </div>
    @endif
    @if ($is_visible['reports'])
      <div class="tab-pane fade" id="reports" role="tabpanel" aria-labelledby="reports-tab">
        @include('DBasic::hubs.show_reports')
      </div>
    @endif
    @if ($is_visible['downloads'])
      <div class="tab-pane fade" id="downloads" role="tabpanel" aria-labelledby="downloads-tab">
        <div class="row row-cols-lg-2">
          <div class="col-lg">
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1">
                  {{ trans_choice('DBasic::common.hub', 1).' '.trans_choice('common.download',2) }}
                  <i class="fas fa-download float-end"></i>
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
