<div class="row row-cols-2">
  <div class="col">
    @if($users_hub->count() > 0)
      <div class="card">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.hpilots')
            <i class="fas fa-users m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::roster.table', ['users' => $users_hub, 'type' => 'hub'])
        </div>
        <div class="card-footer p-0 px-1 small text-end">
          <b>@lang('DBasic::common.total') {{ $users_hub->count() }}</b>
        </div>
      </div>
    @endif
  </div>
  <div class="col">
    @if($users_off->count() > 0)
      <div class="card">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.vpilots')
            <i class="fas fa-user-friends m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::roster.table', ['users' => $users_off, 'type' => 'visitor'])
        </div>
        <div class="card-footer p-0 px-1 small text-end">
          <b>@lang('DBasic::common.total') {{ $users_off->count() }}</b>
        </div>
      </div>
    @endif
  </div>
</div>