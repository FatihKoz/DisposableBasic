<div class="row">
  <div class="col">
    <div class="card mb-2">
      <div class="card-header p-1">
        <h5 class="m-1">
          @lang('DBasic::common.reports')
          <i class="fas fa-file-upload float-end"></i>
        </h5>
      </div>
      <div class="card-body p-0 overflow-auto table-responsive">
        @include('DBasic::pireps.table')
      </div>
      <div class="card-footer p-0 px-1 small fw-bold text-end">
        <span class="float-start">
          @lang('DBasic::common.total') {{ $pireps->total() }}
        </span>
        @lang('DBasic::common.latest') {{ $pireps->lastItem() }}
      </div>
    </div>
  </div>
</div>

{{-- $pireps->links('pagination.default') --}}