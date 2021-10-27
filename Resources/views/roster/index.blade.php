@extends('app')
@section('title', __('DBasic::common.roster'))

@section('content')
  <div class="row">
    <div class="col">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.roster')
            <i class="fas fa-users float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          @include('DBasic::roster.table')
        </div>
        <div class="card-footer p-0 px-1 text-end small">
          <b>@lang('DBasic::common.total') {{ $users->total() }}</b>
        </div>
      </div>
    </div>
  </div>

  {{ $users->links('pagination.auto') }}
@endsection
