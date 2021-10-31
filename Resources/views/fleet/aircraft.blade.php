@extends('app')
@section('title', $aircraft->registration)

@section('content')
  <div class="row">
    {{-- Left --}}
    <div class="col-5">
      @include('DBasic::fleet.aircraft_details')

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

      @widget('DBasic::FuelCalculator')
    </div>
    {{-- Right --}}
    <div class="col-7">
      {{-- Specifications --}}
      @if($specs)
        @include('DBasic::specs.card')
      @endif
      {{-- Maintenance Status --}}
      @if($maint)
        {{-- Include Maintenance Card --}}
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
            @include('DBasic::pireps.table', ['ac_page' => true])
          </div>
          <div class="card-footer p-0 px-1 small text-end">
            <b>@lang('DBasic::common.latest') {{ $pireps->count() }}</b>
          </div>
        </div>
      @endif

      <div class="row row-cols-2">

        <div class="col">
          {{-- Files --}}
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

        <div class="col">
          {{-- Stats --}}
          @widget('DBasic::Stats', ['type' => 'aircraft', 'id' => $aircraft->id])
        </div>

      </div>
    </div>
  </div>
@endsection
