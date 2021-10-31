<div class="row row-cols-2">
  <div class="col">
    @if($aircraft_hub->count() > 0)
      <div class="card">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.haircraft')
            <i class="fas fa-plane m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::fleet.table', ['aircraft' => $aircraft_hub, 'compact_view' => true])
        </div>
        <div class="card-footer p-0 px-1 small text-end">
          <b>@lang('DBasic::common.total') {{ $aircraft_hub->count() }}</b>
        </div>
      </div>
    @endif
  </div>
  <div class="col">
    @if($aircraft_off->count() > 0)
      <div class="card">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.vaircraft')
            <i class="fas fa-plane m-1 float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::fleet.table', ['aircraft' => $aircraft_off, 'compact_view' => true])
        </div>
        <div class="card-footer p-0 px-1 small text-end">
          <b>@lang('DBasic::common.total') {{ $aircraft_off->count() }}</b>
        </div>
      </div>
    @endif
  </div>
</div>
   