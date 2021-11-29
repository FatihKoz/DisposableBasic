@if($is_visible)
  @if($use_button === true)
    {{-- Modal Button --}}
    <button type="button" class="btn btn-sm @if($is_stable) btn-success @else btn-danger @endif m-0 mx-1 p-0 px-1" data-bs-toggle="modal" data-bs-target="#StableApproachModal">
      @if($is_stable) STABLE @else UNSTABLE @endif
    </button>
  @else
    {{-- Modal Badge --}}
    <span type="button" class="badge @if($is_stable) bg-success @else bg-danger @endif text-black" data-bs-toggle="modal" data-bs-target="#StableApproachModal">
      @if($is_stable) STABLE @else UNSTABLE @endif
    </span>
  @endif

  {{-- Modal --}}
  <div class="modal fade" id="StableApproachModal" tabindex="-1" aria-labelledby="StableApproachModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header p-1">
          <h5 class="modal-title m-0" id="StableApproachModalLabel">
            Stable Approach Report
          </h5>
          <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-0">
          @include('DBasic::sap.report_body', ['stable' => $stable])
        </div>
        <div class="modal-footer p-1">
          <button type="button" class="btn btn-sm btn-warning m-0 mx-1 p-0 px-1" data-bs-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
@endif
