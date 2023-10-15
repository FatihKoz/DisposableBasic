<div class="card mb-2">
  <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 82vh;">
    @include('DBasic::fleet.table', ['aircraft' => $aircraft, 'airline_view' => true])
  </div>
  <div class="card-footer p-0 px-1 small fw-bold text-end">
    @lang('DBasic::common.total') {{ $aircraft->count() }}
  </div>
</div>