@extends('app')
@section('title', $subfleet->type)

@section('content')
  <div class="row">
    {{-- Left --}}
    <div class="col-lg-5">
      @include('DBasic::fleet.subfleet_details')
      {{-- Subfleet Image --}}
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
      {{-- Fleet Members --}}
      @if($aircraft && $aircraft->count())
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DBasic::common.members')
              <i class="fas fa-plane float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0 overflow-auto table-responsive" style="max-height: {{ $over_mh.'vh' }};">
            @include('DBasic::fleet.table', ['compact_view' => true])
          </div>
          <div class="card-footer p-0 px-1 small text-end">
            <b>@lang('DBasic::common.total') {{ $aircraft->count() }}</b>
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
            @include('DBasic::pireps.table', ['compact_view' => true])
          </div>
          <div class="card-footer p-0 px-1 small text-end">
            <b>@lang('DBasic::common.latest') {{ $pireps->count() }}</b>
          </div>
        </div>
      @endif

      <div class="row row-cols-lg-2">
        <div class="col-lg">
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
        <div class="col-lg">
          {{-- Stats --}}
        </div>
      </div>
    </div>
  </div>
@endsection
