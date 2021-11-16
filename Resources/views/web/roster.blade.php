@extends('app', ['plain' => true, 'disable_nav' => true])
@section('title', __('DBasic::common.roster'))

@section('content')
  <div class="row">
    <div class="col mt-1">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.roster')
            <i class="fas fa-users float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          @include('DBasic::web.roster_table')
        </div>
        <div class="card-footer p-0 px-1 text-end small fw-bold">
          <span class="float-start">{{ config('app.name') }}</span>
          @if($users->hasPages())
            @lang('DBasic::common.paginate', ['first' => $users->firstItem(), 'last' => $users->lastItem(), 'total' => $users->total()])
          @endif
        </div>
      </div>
    </div>
  </div>
  {{ $users->links('pagination.default') }}
@endsection
