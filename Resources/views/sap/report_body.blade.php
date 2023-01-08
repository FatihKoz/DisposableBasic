{{-- Main Display --}}
<div class="d-grid text-center m-0 p-0">
  @if($sap->stable === true)
    <span class="badge bg-success rounded-0 py-2 text-black">
      <h6 class="m-0 p-0 fw-bold">
        STABLE
      </h6>
    </span>
  @elseif($sap->stable === false)
    <span class="badge bg-danger rounded-0 py-2 text-black">
      <h6 class="m-0 p-0 fw-bold">
        UNSTABLE
      </h6>
    </span>
  @endif
</div>
<div class="accordion accordion-flush" id="StableApproachReport{{ $sap->id }}">
  {{-- Evaluation Messages --}}
  @if(isset($sap->messages))
    <div class="accordion-item">
      <h5 class="accordion-header" id="stable-messages-heading{{ $sap->id }}">
        <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-messages{{ $sap->id }}" aria-expanded="false" aria-controls="stable-messages{{ $sap->id }}">
          Analysis
        </button>
      </h5>
      <div id="stable-messages{{ $sap->id }}" class="accordion-collapse collapse" aria-labelledby="stable-messages-heading{{ $sap->id }}" data-bs-parent="#StableApproachReport{{ $sap->id }}">
        <div class="accordion-body p-0">
          <table class="table table-sm table-borderless table-striped mb-0 align-middle">
            @foreach($sap->messages as $message)
              <tr>
                <th>{{ ucwords($message->name) }}</th>
                <td>
                  <i
                    @if($message->type == 0)
                      class="fas fa-check-circle text-success pe-1"
                    @elseif($message->type == 1)
                      class="fas fa-check-circle text-primary pe-1"
                    @else
                      class="fas fa-times-circle text-danger pe-1"
                    @endif
                    @if(isset($message->description))
                      title="{!! $message->description !!}"
                    @endif
                  ></i>
                </td>
              </tr>
            @endforeach
            @if(filled($analysis->aircraft_profile_type))
              <tr>
                <th>
                  Aircraft Profile
                  @if(isset($sap->report->analysis->aircraft->acf->icao))
                    {{ ' ['.$sap->report->analysis->aircraft->acf->icao.']' }}
                  @endif
                </th>
                <td>
                  <i
                    @if($analysis->aircraft_profile_type->va === true)
                      class="fas fa-check-circle text-success pe-1" title="{{ config('app.name') }} Profile"
                    @elseif($analysis->aircraft_profile_type->official === true)
                      class="fas fa-check-circle text-primary pe-1" title="Official Profile"
                    @else
                      class="fas fa-times-circle text-danger pe-1" title="No Profile Defined !"
                    @endif
                  ></i>
                </td>
              </tr>
            @endif
            @if($sap->created_at != $sap->updated_at)
              <tr>
                <th>Notes</th>
                <td>Revised By Management</td>
              </tr>
            @endif
            @ability('admin', 'admin-user')
              @if($sap->is_stable == 0)
                <tr>
                  <th>Admin Functions</th>
                  <td>
                    {{ Form::open(['route' => 'DBasic.stable_update', 'method' => 'post']) }}
                      {{ Form::hidden('report_id', $sap->id) }}
                      {{ Form::hidden('current_page', url()->current()) }}
                      {{ Form::button('Approve as Stable', ['type' => 'submit', 'name' => 'operation', 'value' => 'update', 'class' => 'btn btn-success btn-sm m-0 mx-1 p-0 px-1', 'onclick' => "return confirm('Do you really want to accept this report as STABLE ?')"]) }}
                      {{ Form::button('Delete', ['type' => 'submit', 'name' => 'operation', 'value' => 'delete', 'class' => 'btn btn-danger btn-sm m-0 mx-1 p-0 px-1', 'onclick' => "return confirm('Do you really want to DELETE this report ?')"]) }}
                    {{ Form::close() }}
                  </td>
                </tr>
              @endif
            @endability
          </table>
        </div>
      </div>
    </div>
  @endif
  {{-- Airport --}}
  <div class="accordion-item">
    @php
      $airport = isset($analysis) ? $analysis->apt : null;
      $runway = isset($analysis) ? $analysis->rwy : null;
    @endphp
    <h5 class="accordion-header" id="stable-airport-heading{{ $sap->id }}">
      <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-airport{{ $sap->id }}" aria-expanded="false" aria-controls="stable-airport{{ $sap->id }}">
        Airport & Runway Details
      </button>
    </h5>
    <div id="stable-airport{{ $sap->id }}" class="accordion-collapse collapse" aria-labelledby="stable-airport-heading{{ $sap->id }}" data-bs-parent="#StableApproachReport{{ $sap->id }}">
      <div class="accordion-body p-0">
        <table class="table table-sm table-borderless table-striped mb-0 align-middle">
          <tr>
            <td class="text-center" colspan="2">{{ $airport->icao.' | '.$airport->name }}</td>
          </tr>
          <tr>
            <th>Ident</th>
            <td>{{ 'Rwy '.$runway->begin->name.'/'.$runway->end->name }}</td>
          </tr>
          <tr>
            <th>Surface Type</th>
            <td>{{ $runway_surface[$runway->surface] ?? '-' }}</td>
          </tr>
          <tr>
            <th>Width</th>
            <td>{{ number_format(floor($runway->width)).'m' }}</td>
          </tr>
          <tr>
            <th>Usable Length</th>
            <td>{{ number_format($runway->length->useable).'m' }}</td>
          </tr>
          <tr>
            <th>Touchdown Zone (TDZ)</th>
            <td>{{ number_format($runway->length->tdz).'m' }}</td>
          </tr>
          @if(isset($runway->begin->disp) && $runway->begin->disp > 0)
            <tr>
              <th>Displaced Threshold</th>
              <td>{{ number_format($runway->begin->disp).'m' }}</td>
            </tr>
          @endif
          @if(isset($runway->begin->overrun) && $runway->begin->overrun > 0)
            <tr>
              <th>Overrun Area</th>
              <td>{{ number_format($runway->begin->overrun).'m' }}</td>
            </tr>
          @endif
          @if(isset($runway->begin->marking))
            <tr>
              <th>Markings</th>
              <td>{{ $runway_marking[$runway->begin->marking] ?? '-' }}</td>
            </tr>
          @endif
          @if(isset($runway->begin->appLights))
            <tr>
              <th>Approach Lights System</th>
              <td>{{ $approach_lights[$runway->begin->appLights] ?? '-' }}</td>
            </tr>
          @endif
        </table>
      </div>
    </div>
  </div>
  {{-- Approach --}}
  <div class="accordion-item">
    @php $approach = isset($analysis) ? $analysis->app : null; @endphp
    <h5 class="accordion-header" id="stable-approach-heading{{ $sap->id }}">
      <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-approach{{ $sap->id }}" aria-expanded="false" aria-controls="stable-approach{{ $sap->id }}">
        Approach
      </button>
    </h5>
    <div id="stable-approach{{ $sap->id }}" class="accordion-collapse collapse" aria-labelledby="stable-approach-heading{{ $sap->id }}" data-bs-parent="#StableApproachReport{{ $sap->id }}">
      <div class="accordion-body p-0">
        <table class="table table-sm table-borderless table-striped mb-0 align-middle">
          <tr>
            <th colspan="2" class="text-center">
            @php
              // Quickfix For Stable Approach Plugin BETA version
              // which provides long descriptions
              $approach_text = $approach->checkHeight->description;
              $division_pos = strpos($approach_text, ':');
              if (is_numeric($division_pos) && $division_pos > 0) {
                $approach_text = substr($approach_text, 0, $division_pos);
              }
            @endphp
              {{ $approach_text }}
            </th>
          </tr>
          @if($approach->loc_dev->max < 9)
            <tr>
              <th>Localizer Deviation</th>
              <td>{{ number_format($approach->loc_dev->max, 1).' dots' }}</td>
            </tr>
          @endif
          @if($approach->gs_dev->max < 9)
            <tr>
              <th>Glide Path Deviation</th>
              <td>{{ number_format($approach->gs_dev->max, 1).' dots' }}</td>
            </tr>
          @endif
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
  @if(isset($sap->touchdowns))
    <div class="accordion-item">
      <h5 class="accordion-header" id="stable-touchdowns-heading{{ $sap->id }}">
        <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-touchdowns{{ $sap->id }}" aria-expanded="false" aria-controls="stable-touchdowns{{ $sap->id }}">
          @if(count($sap->touchdowns) > 1) Touchdowns @else Touchdown @endif
        </button>
      </h5>
      <div id="stable-touchdowns{{ $sap->id }}" class="accordion-collapse collapse" aria-labelledby="stable-touchdowns-heading{{ $sap->id }}" data-bs-parent="#StableApproachReport{{ $sap->id }}">
        <div class="accordion-body p-0">
          @foreach($sap->touchdowns as $touchdown)
            @if(!$loop->first) <hr class="m-1"> @endif
            <table class="table table-sm table-borderless table-striped mb-0 align-middle">
              @if(count($sap->touchdowns) > 1)
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
                <th>Touchdown Rate</th>
                <td>{{ number_format($touchdown->fpm_agl, 2).' fpm' }}</td>
              </tr>
              @if(isset($touchdown->kias))
                <tr>
                  <th>Touchdown Speed</th>
                  <td>{{ number_format($touchdown->kias, 2).' kt' }}</td>
                </tr>
              @endif
              <tr>
                <th>G-Force</th>
                <td>{{ number_format($touchdown->g_vertical, 2).' g' }}</td>
              </tr>
              <tr>
                <th>Pitch Angle</th>
                <td>{{ number_format($touchdown->pitch_gnd, 2).' deg' }}</td>
              </tr>
              <tr>
                <th>Roll (Bank) Angle</th>
                <td>{{ number_format($touchdown->bank, 2).' deg' }}</td>
              </tr>
              <tr>
                <th>Yaw (Crab) Angle</th>
                <td>{{ number_format($touchdown->crab, 2).' deg' }}</td>
              </tr>
              <tr>
                <th>Wind Direction & Speed</th>
                <td>{{ number_format($touchdown->wind_dir).' deg / '.number_format($touchdown->wind_kn).' kt' }}</td>
              </tr>
            </table>
          @endforeach
        </div>
      </div>
    </div>
  @endif
  {{-- Touchdowns Combined --}}
  @if(isset($sap->touchdowns) && count($sap->touchdowns) > 1)
    @php $touchdown_combined = isset($analysis) ? $analysis->touchdown_combined : null; @endphp
    <div class="accordion-item">
      <h5 class="accordion-header" id="stable-touchdown-heading{{ $sap->id }}">
        <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-touchdown{{ $sap->id }}" aria-expanded="false" aria-controls="stable-touchdown{{ $sap->id }}">
          Touchdowns Combined Analysis
        </button>
      </h5>
      <div id="stable-touchdown{{ $sap->id }}" class="accordion-collapse collapse" aria-labelledby="stable-touchdown-heading{{ $sap->id }}" data-bs-parent="#StableApproachReport{{ $sap->id }}">
        <div class="accordion-body p-0">
          <table class="table table-sm table-borderless table-striped mb-0 align-middle">
            <tr>
              <th>Threshold Distance (Min/Max)</th>
              <td>{{ number_format($touchdown_combined->threshold_dist->min, 2).' m | '.number_format($touchdown_combined->threshold_dist->max, 2).' m' }}</td>
            </tr>
            <tr>
              <th>Pitch (Min/Max)</th>
              <td>{{ number_format($touchdown_combined->pitch->min, 2).' deg | '.number_format($touchdown_combined->pitch->max, 2).' deg' }}</td>
            </tr>
            <tr>
              <th>Max Roll & Yaw</th>
              <td>{{ number_format($touchdown_combined->bank->max, 2).' deg | '.number_format($touchdown_combined->crab->max, 2).' deg' }}</td>
            </tr>
            <tr>
              <th>Max Rate & G-Force</th>
              <td>{{ number_format($touchdown_combined->fpm_agl->max, 2).' fpm | '.number_format($touchdown_combined->g_vertical->max, 2).' g' }}</td>
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
    @php $rollout = isset($analysis) ? $analysis->rollout : null; @endphp
    <h5 class="accordion-header" id="stable-rollout-heading{{ $sap->id }}">
      <button class="accordion-button p-1 collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#stable-rollout{{ $sap->id }}" aria-expanded="false" aria-controls="stable-rollout{{ $sap->id }}">
        Rollout
      </button>
    </h5>
    <div id="stable-rollout{{ $sap->id }}" class="accordion-collapse collapse" aria-labelledby="stable-rollout-heading{{ $sap->id }}" data-bs-parent="#StableApproachReport{{ $sap->id }}">
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
  #StableApproachReport{{ $sap->id }} .table td { text-align: end;}
  #StableApproachReport{{ $sap->id }} .table th { text-align: start;}
</style>