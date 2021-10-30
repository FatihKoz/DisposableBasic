@extends('app')
@section('title', $aircraft->registration)

@section('content')
  <div class="row">
    {{-- Left --}}
    <div class="col-5">
      {{-- Standard Aircraft Details --}}
      @include('DBasic::fleet.show_aircraft_details')
      {{-- Aircraft or Subfleet Image --}}
      @if($image)
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              {{ $image_text }}
              <i class="fas fa-camera-retro m-1 float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0">
            <img class="card-img" src="{{ public_asset($image) }}">
          </div>
        </div>
      @endif
    </div>
    {{-- Right --}}
    <div class="col-7">
      {{-- Maintenance Status & Pireps --}}
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.reports')
            <i class="fas fa-file-upload m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          @include('DBasic::pireps.table', ['ac_page' => true])
        </div>
      </div>
      <div class="row row-cols-2">
        <div class="col">
          {{-- Files --}}
          @if(count($aircraft->files) > 0 && Auth::check())
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1 p-0">
                  {{ trans_choice('common.download',2) }}
                  <i class="fas fa-download float-right"></i>
                </h5>
              </div>
              <div class="card-body p-0">
                @include('downloads.table', ['files' => $aircraft->files])
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
