<div class="row row-cols-lg-2">
  <div class="col-lg">
    @if($flights_dpt->count() > 0)
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.hdeps')
            <i class="fas fa-plane-departure float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::flights.table', ['flights' => $flights_dpt, 'type' => 'dpt'])
        </div>
        <div class="card-footer p-0 px-1 small text-end fw-bold">
          @lang('DBasic::common.total') {{ $flights_dpt->count() }}
        </div>
      </div>
    @endif
  </div>
  <div class="col-lg">
    @if($flights_arr->count() > 0)
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::common.harrs')
            <i class="fas fa-plane-arrival float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive">
          @include('DBasic::flights.table', ['flights' => $flights_arr, 'type' => 'arr'])
        </div>
        <div class="card-footer p-0 px-1 small text-end fw-bold">
          @lang('DBasic::common.total') {{ $flights_arr->count() }}
        </div>
      </div>
    @endif
  </div>
</div>