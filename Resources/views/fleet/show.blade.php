@extends('app')
@section('title', $aircraft->registration)

@section('content')
  <div class="row">
    <div class="col-6">
      {{ optional($aircraft->landing_time)->diffForHumans() }}

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

    {{-- Show ac or subfleet image if there is one --}}
    <div class="col-6">

    </div>
  </div>

  {{-- Second Row For Aircraft Pireps and Stats --}}
  <div class="row">
    <div class="col-8">

    </div>
    <div class="col-4">

    </div>
  </div>
@endsection
