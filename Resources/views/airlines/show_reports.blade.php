<div class="card mb-2">
  <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 77vh;">
    @include('DBasic::pireps.table')
  </div>
  <div class="card-footer p-0 px-1 small fw-bold text-end">
    @lang('DBasic::common.paginate', ['first' => $pireps->firstItem(), 'last' => $pireps->lastItem(), 'total' => $pireps->total()])
  </div>
</div>

{{-- $pireps->links('pagination.default') --}}