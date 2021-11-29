@php
  if ($stable) {
    $requirements = is_array($stable->requirementResultsGroups) ? collect($stable->requirementResultsGroups) : null;
    $touchdowns = is_array(optional($stable->analysis)->touchdowns) ? collect($stable->analysis->touchdowns) : null;
  } else {
    $requirements = null;
    $touchdowns = null;
  }

  $analysis = isset($stable) ? $stable->analysis : null;
  $airport = isset($analysis) ? $analysis->apt : null;
  $approach = isset($analysis) ? $analysis->app : null;
  $rollout = isset($analysis) ? $analysis->rollout : null;
  $runway = isset($analysis) ? $analysis->rwy : null;
  $touchdown_combined = isset($analysis) ? $analysis->touchdown_combined : null;
@endphp
{{-- Main Display --}}
<div class="d-grid text-center p-1">
  @if($report->is_stable == 1)
    <span class="badge bg-success py-2 text-black">
      <h6 class="m-0 p-0 fw-bold">
        STABLE
      </h6>
    </span>
  @elseif($report->is_stable == 0)
    <span class="badge bg-danger py-2 text-black">
      <h6 class="m-0 p-0 fw-bold">
        UNSTABLE
      </h6>
    </span>
  @endif
</div>
<div class="accordion accordion-flush" id="StableApproachReport{{ $report->id }}">
  {{-- Results --}}
  @if(isset($requirements))
    <div class="accordion-item">
      <h5 class="accordion-header" id="stable-results-heading{{ $report->id }}">
        <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-results{{ $report->id }}" aria-expanded="false" aria-controls="stable-results{{ $report->id }}">
          Analysis
        </button>
      </h5>
      <div id="stable-results{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="stable-results-heading{{ $report->id }}" data-bs-parent="#StableApproachReport{{ $report->id }}">
        <div class="accordion-body p-0">
          <table class="table table-sm table-borderless table-striped mb-0 align-middle">
            @foreach($requirements as $result)
              <tr>
                <th>{{ $result->name }}</th>
                <td>
                  <i
                    @if($result->type == 0)
                      class="fas fa-check-circle text-success pe-1"
                    @elseif($result->type == 1)
                      class="fas fa-check-circle text-primary pe-1"
                    @else
                      class="fas fa-times-circle text-danger pe-1"
                    @endif
                    @if(isset($result->description))
                      title="{!! $result->description !!}"
                    @endif
                  ></i>
                </td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
    </div>
  @endif
  {{-- Airport --}}
  <div class="accordion-item">
    <h5 class="accordion-header" id="stable-airport-heading{{ $report->id }}">
      <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-airport{{ $report->id }}" aria-expanded="false" aria-controls="stable-airport{{ $report->id }}">
        Airport & Runway Details
      </button>
    </h5>
    <div id="stable-airport{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="stable-airport-heading{{ $report->id }}" data-bs-parent="#StableApproachReport{{ $report->id }}">
      <div class="accordion-body p-0">
        <table class="table table-sm table-borderless table-striped mb-0 align-middle">
          <tr>
            <td class="text-center">{{ $airport->icao.' '.$airport->name }}</td>
          </tr>
          <tr>
            <td class="text-center">
              {{ 'Rwy '.$runway->begin->name.'/'.$runway->end->name }}
              {{ ' ('.number_format(floor($runway->width)).'m | '.number_format($runway->length->useable).'m)' }}
            </td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  {{-- Approach --}}
  <div class="accordion-item">
    <h5 class="accordion-header" id="stable-approach-heading{{ $report->id }}">
      <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-approach{{ $report->id }}" aria-expanded="false" aria-controls="stable-approach{{ $report->id }}">
        Approach
      </button>
    </h5>
    <div id="stable-approach{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="stable-approach-heading{{ $report->id }}" data-bs-parent="#StableApproachReport{{ $report->id }}">
      <div class="accordion-body p-0">
        <table class="table table-sm table-borderless table-striped mb-0 align-middle">
          <tr>
            <th colspan="2" class="text-center">{{ $approach->checkHeight->description }}</th>
          </tr>
          <tr>
            <th>Centerline Deviation</th>
            <td>{{ number_format($approach->loc_dev->max, 1).' dots' }}</td>
          </tr>
          <tr>
            <th>Glide Path Deviation</th>
            <td>{{ number_format($approach->gs_dev->max, 1).' dots' }}</td>
          </tr>
          <tr>
            <th>Sink Rate (Min/Max)</th>
            <td>{{ number_format($approach->sinkrate->min, 2).' fpm | '.number_format($approach->sinkrate->max, 2).' fpm' }}</td>
          </tr>
          <tr>
            <th>Indicated Air Speed (Min/Max)</th>
            <td>{{ number_format($approach->kias->min, 2).' kt | '.number_format($approach->kias->max, 2).' kt' }}</td>
          </tr>
          <tr>
            <th>Threshold Crossing Height</th>
            <td>{{ number_format($approach->threshold->crossing_height, 2).' ft' }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
  {{-- Touchdowns --}}
  @if(isset($touchdowns))
    <div class="accordion-item">
      <h5 class="accordion-header" id="stable-touchdowns-heading{{ $report->id }}">
        <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-touchdowns{{ $report->id }}" aria-expanded="false" aria-controls="stable-touchdowns{{ $report->id }}">
          @if(count($touchdowns) > 1) Touchdowns @else Touchdown @endif
        </button>
      </h5>
      <div id="stable-touchdowns{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="stable-touchdowns-heading{{ $report->id }}" data-bs-parent="#StableApproachReport{{ $report->id }}">
        <div class="accordion-body p-0">
          @foreach($touchdowns as $touchdown)
            @if(!$loop->first) <hr class="m-1"> @endif
            <table class="table table-sm table-borderless table-striped mb-0 align-middle">
              @if(count($touchdowns) > 1)
                <tr>
                  <td class="fw-bold" colspan="2">
                    <span class="pe-2">{{ $loop->iteration }}</span>
                  </td>
                </tr>
              @endif
              <tr>
                <th>Threshold Distance</th>
                <td>{{ number_format($touchdown->threshold_dist, 2).' m' }}</td>
              </tr>
              <tr>
                <th>Centerline Deviation</th>
                <td>{{ number_format($touchdown->centerline_dist, 2).' m' }}</td>
              </tr>
              <tr>
                <th>Descend Rate</th>
                <td>{{ number_format($touchdown->fpm, 2).' fpm' }}</td>
              </tr>
              <tr>
                <th>G-Force</th>
                <td>{{ number_format($touchdown->g_vertical, 2).' g' }}</td>
              </tr>
              <tr>
                <th>Pitch Angle</th>
                <td>{{ number_format($touchdown->pitch_gnd, 2).' degrees' }}</td>
              </tr>
              <tr>
                <th>Bank Angle</th>
                <td>{{ number_format($touchdown->bank, 2).' degrees' }}</td>
              </tr>
              <tr>
                <th>Crab Angle</th>
                <td>{{ number_format($touchdown->crab, 2).' degrees' }}</td>
              </tr>
              <tr>
                <th>Wind Speed/Direction</th>
                <td>{{ number_format($touchdown->wind_kn).' kt / '.number_format($touchdown->wind_dir).' degrees' }}</td>
              </tr>
            </table>
          @endforeach
        </div>
      </div>
    </div>
  @endif
  {{-- Touchdown Combined --}}
  @if(isset($touchdowns) && count($touchdowns) > 1 || empty($touchdowns))
    <div class="accordion-item">
      <h5 class="accordion-header" id="stable-touchdown-heading{{ $report->id }}">
        <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-touchdown{{ $report->id }}" aria-expanded="false" aria-controls="stable-touchdown{{ $report->id }}">
          Touchdown Combined Analysis
        </button>
      </h5>
      <div id="stable-touchdown{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="stable-touchdown-heading{{ $report->id }}" data-bs-parent="#StableApproachReport{{ $report->id }}">
        <div class="accordion-body p-0">
          <table class="table table-sm table-borderless table-striped mb-0 align-middle">
            <tr>
              <th>Threshold Distance (Min/Max)</th>
              <td>{{ number_format($touchdown_combined->threshold_dist->min, 2).' m | '.number_format($touchdown_combined->threshold_dist->max, 2).' m' }}</td>
            </tr>
            <tr>
              <th>Pitch (Min/Max)</th>
              <td>{{ number_format($touchdown_combined->pitch->min, 2).' degrees | '.number_format($touchdown_combined->pitch->max, 2).' degrees' }}</td>
            </tr>
            <tr>
              <th>Max Rate & G-Force</th>
              <td>{{ number_format($touchdown_combined->fpm_agl->max, 2).' fpm | '.number_format($touchdown_combined->g_vertical->max, 2).' g' }}</td>
            </tr>
            <tr>
              <th>Max Bank & Crab</th>
              <td>{{ number_format($touchdown_combined->bank->max, 2).' degrees | '.number_format($touchdown_combined->crab->max, 2).' degrees' }}</td>
            </tr>
            <tr>
              <th>Centerline Deviation</th>
              <td>{{ number_format($touchdown_combined->centerline_dist->max, 2).' m' }}</td>
            </tr>
            <tr>
              <th>Count</th>
              <td>{{ number_format($touchdown_combined->touchdown_count) }}</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  @endif
  {{-- Rollout --}}
  <div class="accordion-item">
    <h5 class="accordion-header" id="stable-rollout-heading{{ $report->id }}">
      <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-rollout{{ $report->id }}" aria-expanded="false" aria-controls="stable-rollout{{ $report->id }}">
        Rollout
      </button>
    </h5>
    <div id="stable-rollout{{ $report->id }}" class="accordion-collapse collapse" aria-labelledby="stable-rollout-heading{{ $report->id }}" data-bs-parent="#StableApproachReport{{ $report->id }}">
      <div class="accordion-body p-0">
        <table class="table table-sm table-borderless table-striped mb-0 align-middle">
          <tr>
            <th>Rollout Distance</th>
            <td>{{ number_format($rollout->roll_dist).' m' }}</td>
          </tr>
          <tr>
            <th>Remaining Runway</th>
            <td>{{ number_format($rollout->rwy_remaining).' m' }}</td>
          </tr>
          <tr>
            <th>Landing Distance</th>
            <td>{{ number_format($rollout->landing_dist50).' m' }}</td>
          </tr>
        </table>
      </div>
    </div>
  </div>
</div>
<style>
  #StableApproachReport{{ $report->id }} .table td { text-align: end;}
  #StableApproachReport{{ $report->id }} .table th { text-align: start;}
</style>