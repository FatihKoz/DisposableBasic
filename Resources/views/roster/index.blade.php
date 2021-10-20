@extends('app')
@section('title', @lang('DB::common.roster'))

@section('content')
  <div class="row">
    <div class="col">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1 p-0">
            @lang('DB::common.roster')
            <i class="fas fa-users float-end m-1"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          @include('DisposableBasic::roster_table')
        </div>
        <div class="card-footer p-1 text-end small">
          @lang('DB::common.totpilots'): {{ $users->total() }}
        </div>
      </div>
    </div>
  </div>

  {{ $users->links('pagination.auto') }}
@endsection
