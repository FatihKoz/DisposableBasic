@extends('app', ['plain' => true, 'disable_nav' => true])
@section('title', __('DBasic::common.reports'))

@section('content')
  <div class="row">
    <div class="col mt-1">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.reports')
            <i class="fas fa-file-upload float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          @include('DBasic::web.pireps_table')
        </div>
        <div class="card-footer p-0 px-1 text-end small fw-bold">
          <span class="float-start">{{ config('app.name') }}</span>
        </div>
      </div>
    </div>
  </div>
@endsection
