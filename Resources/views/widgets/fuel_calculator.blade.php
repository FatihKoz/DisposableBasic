@if (count($fuel_data) > 1)
  {{-- Modal Button --}}
  <div class="row">
    <div class="col">
      <div class="card p-0 mb-2 bg-transparent border-0">
        <button type="button" class="btn btn-sm bg-warning text-black p-1" data-toggle="modal" data-target="#fuel_calc">
          <b>Fuel Calculator</b>
          <i class="fas fa-gas-pump mx-1"></i>
        </button>
      </div>
    </div>
  </div>

  {{-- Modal Body --}}
  <div class="modal" id="fuel_calc" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="Fuel Calculator" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content shadow-none p-0" style="border-radius: 5px;">
        <div class="modal-header border-1 p-1">
          <h5 class="m-1">Fuel Calculator</h5>
          <span class="close m-1">
            <i class="fas fa-times-circle" title="Close" data-dismiss="modal" aria-label="Close" aria-hidden="true"></i>
          </span>
        </div>
        <div class="modal-body border-1 p-1">
          <form class="form-group" id="fuelcalculator">
            <div class="row mb-1">
                <div class="col-lg">
                  <label for="source">Source</label>
                  <input id="source" class="form-control form-control-sm" type="text" value="{{ $fuel_data['source'] }}" disabled/>
                </div>
                <div class="col-lg">
                  <label for="flight_time">Flight Time (minutes)</label>
                  <input id="flight_time" type="number" class="form-control form-control-sm" maxlength="4"/>
                </div>
                <div class="col-lg">
                  <label for="avg_fuel">Avg. Consumption (per minute)</label>
                  <div class="input-group">
                    <input id="avg_fuel" type="text" class="form-control form-control-sm" value="{{ $fuel_data['avg_pounds'] }} lbs" disabled/>
                    @if ($is_metric)
                      <input id="avg_fuel_kg" type="text" class="form-control form-control-sm" value="{{ $fuel_data['avg_metric'] }} kgs" disabled/>
                    @endif
                  </div>
                </div>
            </div>
            <div class="row mb-1">
              <div class="col-lg">
                <label for="fuel_burn">Fuel Burn</label>
                <input id="fuel_burn" type="text" class="form-control form-control-sm" disabled/>
                @if($is_metric)
                  <input id="fuel_burn_kg" type="text" class="form-control form-control-sm" disabled/>
                @endif
              </div>
              <div class="col-lg">
                <label for="fuel_reserve">Reserve (30 mins)</label>
                <input id="fuel_reserve" type="text" class="form-control form-control-sm" disabled/>
                @if($is_metric)
                  <input id="fuel_reserve_kg" type="text" class="form-control form-control-sm" disabled/>
                @endif
              </div>
              <div class="col-lg">
                <label for="fuel_total">Total Required</label>
                <input id="fuel_total" type="text" class="form-control form-control-sm" disabled/>
                @if($is_metric)
                  <input id="fuel_total_kg" type="text" class="form-control form-control-sm" disabled/>
                @endif
              </div>
            </div>
          </form>
        </div>
        <div class="modal-footer p-1 text-right">
          <button type="button" class="btn btn-sm btn-warning m-0 mx-1 p-0 px-1 text-black" id="calc_button" onclick="FuelCalculator()">
            <b>Calculate</b>
            <i class="fas fa-gas-pump ml-1 mr-1" style="color: black;"></i>
          </button>
        </div>
      </div>
    </div>
  </div>

  @section('scripts')
    @parent
    <script type="text/javascript">
      function FuelCalculator() {
        var fuel = Number({{ $avg_pounds }});
        var flight_time = Number(document.getElementById('flight_time').value);
        var metric = Boolean({{ $is_metric }});
        // Calculate
        if (flight_time > 0) {
          var fuel_burn = Math.round(fuel * flight_time);
          var fuel_reserve = Math.round(fuel * 30);
          var fuel_total = Math.round(fuel_burn + fuel_reserve);
          // Display Results (Imperial)
          document.getElementById('fuel_burn').value = String(fuel_burn) + ' lbs';
          document.getElementById('fuel_reserve').value = String(fuel_reserve) + ' lbs';
          document.getElementById('fuel_total').value = String(fuel_total) + ' lbs';
        }
        if (metric == true && flight_time > 0) {
          var fuel_kg = Number({{ $avg_metric }});
          var fuel_burn_kg = Math.round(fuel_kg * flight_time);
          var fuel_reserve_kg = Math.round(fuel_kg * 30);
          var fuel_total_kg = Math.round(fuel_burn_kg + fuel_reserve_kg);
          // Display Result (Metric)
          document.getElementById('fuel_burn_kg').value = String(fuel_burn_kg) + ' kgs';
          document.getElementById('fuel_reserve_kg').value = String(fuel_reserve_kg) + ' kgs';
          document.getElementById('fuel_total_kg').value = String(fuel_total_kg) + ' kgs';
        }
      }
    </script>
  @endsection
@endif
