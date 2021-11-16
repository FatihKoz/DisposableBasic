@extends('app')
@section('title', trans_choice('common.pirep', 2))

@section('content')
  @if(!$pireps->count())
    <div class="alert alert-info mb-1 p-1 px-2 fw-bold">No Pilot Reports!</div>
  @else
    <div class="row">
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DBasic::common.reports')
              <i class="fas fa-upload float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 78vh;">
            @include('DBasic::pireps.table')
          </div>
          <div class="card-footer p-0 px-1 text-end fw-bold small">
            @lang('DBasic::common.paginate', ['first' => $pireps->firstItem(), 'last' => $pireps->lastItem(), 'total' => $pireps->total()])
          </div>
        </div>
      </div>
    </div>
    {{ $pireps->links('pagination.default') }}
  @endif
@endsection
