<div class="card mb-2">
  <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 82vh;">
    @include('DBasic::pireps.table_compact')
  </div>
  <div class="card-footer p-0 px-1 small fw-bold text-end">
    <span class="float-start">
      @lang('DBasic::common.total') {{ $pireps->total() }}
    </span>
    @lang('DBasic::common.latest') {{ $pireps->lastItem() }}
  </div>
</div>
