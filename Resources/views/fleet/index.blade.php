@extends('app')
@section('title', __('DBasic::common.fleet'))

@section('content')
  @if(!$aircraft->count())
    <div class="alert alert-info mb-1 p-1 px-2 fw-bold">No Aircraft!</div>
  @else
    <div class="row">
      <div class="col">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @isset($subfleet) {{ $subfleet->airline->name.' | '.$subfleet->name }} @else {{ config('app.name') }} @endisset @lang('DBasic::common.fleet')
              <i class="fas fa-plane float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 77vh;">
            @include('DBasic::fleet.table')
          </div>
          <div class="card-footer p-0 px-1 small fw-bold text-end">
            @lang('DBasic::common.paginate', ['first' => $aircraft->firstItem(), 'last' => $aircraft->lastItem(), 'total' => $aircraft->total()])
          </div>
        </div>
      </div>
    </div>
    {{ $aircraft->links('pagination.default') }}
  @endif
@endsection
