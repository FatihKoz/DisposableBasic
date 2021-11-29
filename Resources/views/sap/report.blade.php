<div id="stable_report{{ $report->id }}" class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      Stable Approach Report
      <i class="fas fa-plane-arrival float-end m-1"></i>
    </h5>
  </div>
  <div class="card-body p-0 table-responsive">
    @include('DBasic::sap.report_body')
  </div>
  <div class="card-footer p-0 px-1 text-end small fw-bold">
    @if($report->pirep)
      <span class="float-start">
        <a href="{{ route('frontend.pireps.show', [$report->pirep->id]) }}">
          {{ $report->pirep->ident.' ('.$report->pirep->dpt_airport_id.'-'.$report->pirep->arr_airport_id.')' }}
        </a>
      </span>
    @else
      <span class="float-start">{{ $analysis->id }}</span>
    @endif
    <a href="https://stableapproach.net" target="_blank">Version {{ $stable->plugin_version }}</a>
  </div>
</div>