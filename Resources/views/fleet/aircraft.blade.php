@extends('app')
@section('title', $aircraft->registration)

@section('content')
  <div class="row">
    {{-- Left --}}
    <div class="col-lg-5">
      @include('DBasic::fleet.aircraft_details')

      @widget('DBasic::FuelCalculator', ['aircraft' => $aircraft->id])

      @if($image)
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              {{ $image['title'] }}
              <i class="fas fa-camera-retro float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0">
            <img class="card-img" src="{{ public_asset($image['url']) }}">
          </div>
        </div>
      @endif
    </div>
    {{-- Right --}}
    <div class="col-lg-7">
      {{-- Specifications --}}
      @if($specs)
        @include('DBasic::specs.card')
      @endif
      {{-- Maintenance Status --}}
      @if($maint)
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              Maintenance
              <i class="fas fa-tools float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0">
            @include('DSpecial::maintenance.table')
          </div>
        </div>
      @endif
      {{-- Latest Pireps --}}
      @if($pireps)
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DBasic::common.reports')
              <i class="fas fa-file-upload float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0 overflow-auto table-responsive">
            @include('DBasic::pireps.table_compact', ['ac_page' => true])
          </div>
          <div class="card-footer p-0 px-1 small text-end">
            <b>@lang('DBasic::common.latest') {{ $pireps->count() }}</b>
          </div>
        </div>
      @endif
      <div class="row row-cols-lg-2">
        {{-- Files --}}
        <div class="col-md">
          @if($files)
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1">
                  @lang('DBasic::common.downloads')
                  <i class="fas fa-download float-end"></i>
                </h5>
              </div>
              <div class="card-body p-0">
                @include('downloads.table', ['files' => $files])
              </div>
            </div>
          @endif
        </div>
        {{-- Basic Stats --}}
        <div class="col-md">
          @if($stats)
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1">
                  @lang('DBasic::widgets.stats')
                  <i class="fas fa-cogs float-end"></i>
                </h5>
              </div>
              <div class="card-body p-0 table-responsive">
                <table class="table table-sm table-borderless table-striped text-start mb-0">
                  <tr>
                    <th>{{ $stats['pireps_desc'] }}</th>
                    <td class="text-end">{{ number_format($stats['pireps_value']) }}</td>
                  </tr>
                  <tr>
                    <th>{{ $stats['time_desc'] }}</th>
                    <td class="text-end">{{ DB_ConvertMinutes($stats['time_value'], '%02d h %02d m') }}</td>
                  </tr>
                  <tr>
                    <th>{{ $stats['dist_desc'] }}</th>
                    <td class="text-end">{{ number_format($stats['dist_value']->local(0)).' '.$units['distance'] }}</td>
                  </tr>
                  <tr>
                    <th>{{ $stats['fuel_desc'] }}</th>
                    <td class="text-end">{{ number_format($stats['fuel_value']->local(0)).' '.$units['fuel'] }}</td>
                  </tr>
                  <tr>
                    <th>{{ $stats['fuel_perhour_desc'] }}</th>
                    <td class="text-end">{{ number_format($stats['fuel_perhour_value']->local(0)).' '.$units['fuel'] }}</td>
                  </tr>
                  <tr>
                    <th>{{ $stats['lrate_avg_desc'] }}</th>
                    <td class="text-end">{{ number_format($stats['lrate_avg_value']).' ft/min' }}</td>
                  </tr>
                </table>
              </div>
              <div class="card-footer p-0 px-1 small text-end fw-bold">
                {{ $aircraft->ident }}
                @if($aircraft->name != $aircraft->registration)
                  {{ ' "'.ucfirst($aircraft->name).'"' }}
                @endif
              </div>
            </div>
          @endif
        </div>
      </div>
    </div>
  </div>
@endsection
