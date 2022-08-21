@extends('app')
@section('title', $airline->name)

@section('content')
  <div class="row">
    {{-- LEFT --}}
    <div class="col-lg-9">
      {{-- Navigation --}}
      <div class="nav nav-pills nav-justified mb-3" id="airline-nav" role="tablist">
        @if($subfleets->count() > 0)
          <button class="nav-link active mx-1 p-1" id="airline-fleet" data-bs-toggle="pill" data-bs-target="#al_fleet" type="button" role="tab" aria-controls="al_fleet" aria-selected="true">
            @lang('DBasic::common.fleet')
          </button>
        @endif
        @if($users->count() > 0)
          <button class="nav-link mx-1 p-1" id="airline-pilots" data-bs-toggle="pill" data-bs-target="#al_pilots" type="button" role="tab" aria-controls="al_pilots" aria-selected="false">
            @lang('DBasic::common.roster')
          </button>
        @endif
        @if($pireps->count() > 0)
          <button class="nav-link mx-1 p-1" id="airline-pireps" data-bs-toggle="pill" data-bs-target="#al_pireps" type="button" role="tab" aria-controls="al_pireps" aria-selected="false">
            @lang('DBasic::common.reports')
          </button>
        @endif
      </div>
      {{-- Content --}}
      <div class="tab-content" id="airline-navContent">
        @if($subfleets->count() > 0)
          <div class="tab-pane fade show active" id="al_fleet" role="tabpanel" aria-labelledby="airline-fleet">
            @include('DBasic::airlines.show_fleet')
          </div>
        @endif
        @if($users->count() > 0)
          <div class="tab-pane fade" id="al_pilots" role="tabpanel" aria-labelledby="airline-pilots">
            @include('DBasic::airlines.show_roster')
          </div>
        @endif
        @if($pireps->count() > 0)
          <div class="tab-pane fade" id="al_pireps" role="tabpanel" aria-labelledby="airline-pireps">
            @include('DBasic::airlines.show_reports')
          </div>
        @endif
      </div>
    </div>
    {{-- RIGHT --}}
    <div class="col-lg-3">
      {{-- Airline Details --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.aldetails')
            <i class="fas fa-info float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start text-nowrap mb-0">
            <tr>
              <th style="width:30%;">@lang('common.name')</th>
              <td class="text-end">{{ $airline->name }}</td>
            </tr>
            <tr>
              <th>@lang('DBasic::common.icao')</th>
              <td class="text-end">{{ $airline->icao }}</td>
            </tr>
            @if(filled($airline->iata))
              <tr>
                <th>@lang('DBasic::common.iata')</th>
                <td class="text-end">{{ $airline->iata }}</td>
              </tr>
            @endif
            @if(filled($airline->callsign))
              <tr>
                <th>@lang('DBasic::common.callsign')</th>
                <td class="text-end">{{ $airline->callsign }}</td>
              </tr>
            @endif
            @if(strlen($airline->country) === 2)
              <tr>
                <th>@lang('common.country')</th>
                <td class="text-end">{{ $country->alpha2($airline->country)['name'] ?? '' }} {{ ' ('.strtoupper($airline->country).')' }}</td>
              </tr>
            @endif
          </table>
        </div>
        @if(filled($airline->logo))
          <div class="card-footer p-1 text-center">
            <img src="{{ $airline->logo }}" alt="" style="max-width: 90%; max-height: 50px;">
          </div>
        @endif
      </div>
      {{-- Two Map side by side positioning --}}
      <div class="row mb-2">
        <div class="col">
          @widget('DBasic::Map', ['source' => $airline->id])
        </div>
        <div class="col">
          @widget('DBasic::Map', ['source' => 'fleet', 'airline' => $airline->id])
        </div>
      </div>
      {{-- Overall Finance --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.finance')
            <i class="fas fa-receipt float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start text-nowrap mb-0">
            @foreach($finance as $key => $value)
              <tr>
                <th>{{ $key }}</th>
                <td class="text-end">{!! $value !!}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      {{-- Basic Stats --}}
      <div class="card mb-1">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::widgets.stats')
            <i class="fas fa-cogs float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped text-start text-nowrap mb-0">
            @foreach($stats_b as $key => $value)
              <tr>
                <th>{{ $key }}</th>
                <td class="text-end">{{ $value }}</td>
              </tr>
            @endforeach
            @if($stats_p)
              @foreach($stats_p as $key => $value)
                <tr>
                  <th>{{ $key }}</th>
                  <td class="text-end">{{ $value }}</td>
                </tr>
              @endforeach
            @endif
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection
