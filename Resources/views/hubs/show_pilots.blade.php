<div class="row row-cols-lg-2">
  <div class="col-lg">
    @if($users_hub->count() > 0)
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.hpilots')
            <i class="fas fa-users float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::roster.table', ['users' => $users_hub, 'type' => 'hub'])
        </div>
        <div class="card-footer p-0 px-1 small fw-bold text-end">
          @lang('DBasic::common.total') {{ $users_hub->count() }}
        </div>
      </div>
    @endif
  </div>
  <div class="col-lg">
    @if($users_off->count() > 0)
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.vpilots')
            <i class="fas fa-user-friends float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::roster.table', ['users' => $users_off, 'type' => 'visitor'])
        </div>
        <div class="card-footer p-0 px-1 small fw-bold text-end">
          @lang('DBasic::common.total') {{ $users_off->count() }}
        </div>
      </div>
    @endif
  </div>
</div>