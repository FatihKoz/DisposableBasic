<div class="card mb-2">
  <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 84vh;">
    @include('DBasic::pireps.table')
  </div>
  <div class="card-footer p-0 px-1 small text-end">
    <b>@lang('DBasic::common.total') {{ $pireps->total() }}</b>
  </div>
</div>

{{ $pireps->links('pagination.auto') }}