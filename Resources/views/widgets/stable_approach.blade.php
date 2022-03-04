@if($is_visible)
  @if($use_button === true)
    {{-- Modal Button --}}
    <button type="button" class="btn btn-sm @if($is_stable) btn-success @else btn-danger @endif m-0 mx-1 p-0 px-1" data-bs-toggle="modal" data-bs-target="#FDM{{$sap->id}}">
      @if($is_stable) STABLE @else UNSTABLE @endif
    </button>
  @else
    {{-- Modal Badge --}}
    <span type="button" class="badge @if($is_stable) bg-success @else bg-danger @endif text-black" data-bs-toggle="modal" data-bs-target="#FDM{{$sap->id}}">
      @if($is_stable) STABLE @else UNSTABLE @endif
    </span>
  @endif

  {{-- Modal --}}
  <div class="modal fade" id="FDM{{$sap->id}}" tabindex="-1" aria-labelledby="FDMLBL{{$sap->id}}" aria-hidden="true">
    <div class="modal-dialog mx-auto">
      <div class="modal-content">
        <div class="modal-header border-0 p-1">
          <h5 class="modal-title m-0" id="FDMLBL{{$sap->id}}">
            Stable Approach Report
          </h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body border-0 p-0">
          @include('DBasic::sap.report_body', ['analysis' => $report->analysis])
        </div>
        <div class="modal-footer border-0 p-1">
          <button type="button" class="btn btn-sm btn-warning m-0 mx-1 p-0 px-1" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endif
