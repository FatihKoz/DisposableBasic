@extends('app')
@section('title', trans_choice('common.pirep', 2))

@section('content')
  <div class="row">
    <div class="col">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1 p-0">
            @lang('DBasic::common.reports')
            <i class="fas fa-upload float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          @include('DBasic::pireps.table')
        </div>
        <div class="card-footer p-1 text-end small">
          <b>@lang('DBasic::common.total') {{ $pireps->total() }}</b>
        </div>
      </div>
    </div>
  </div>

  {{ $pireps->links('pagination.auto') }}
@endsection
