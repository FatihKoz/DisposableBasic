<div id="stable_report{{ $sap->id }}" class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      Stable Approach Report
      <i class="fas fa-plane-arrival float-end"></i>
    </h5>
  </div>
  <div class="card-body p-0 table-responsive">
    @include('DBasic::sap.report_body', ['analysis' => optional($sap->report)->analysis])
  </div>
  <div class="card-footer p-0 px-1 text-end small fw-bold">
    @if($sap->pirep)
      <span class="float-start">
        <a href="{{ route('frontend.pireps.show', [$sap->pirep->id]) }}">
          @if(isset($sap->report->analysis->aircraft->acf->icao))
            {{ '['.$sap->report->analysis->aircraft->acf->icao.'] ' }}
          @endif
          {{ $sap->pirep->ident.' ('.$sap->pirep->dpt_airport_id.'-'.$sap->pirep->arr_airport_id.')' }}
        </a>
      </span>
    @else
      <span class="float-start">{{ $sap->report->analysis->id ?? '' }}</span>
    @endif
    @if($sap->report)
      <a href="https://stableapproach.net" target="_blank">Version {{ $sap->report->plugin_version }}</a>
    @endif
  </div>
</div>