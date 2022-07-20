@extends('admin.app')
@section('title', 'Disposable Aircraft Specifications')

@section('content')
  <div class="card border-blue-bottom" style="margin-left:5px; margin-right:5px; margin-bottom:5px;">
    <div class="content">
      <p>Specifications saved for an ICAO Type will be used for all aircraft sharing the same type, Subfleet definitions will apply to all members of that fleet, finally aircraft based definitions is also possible (but not advised).</p>
      <p>(1) : Most important fields for proper SimBrief flight planning according to the addon being used.</p>
      <p>(2) : When used all three fields must be defined otherwise it will not be used for SimBrief planning.</p>
      <p>Order of loading/selection during usage is simple as <b>Aircraft > SubFleet > ICAO Type</b></p>
      <br>
      <p><a href="https://github.com/FatihKoz" target="_blank">&copy; B.Fatih KOZ</a></p>
    </div>
  </div>

  <div class="row text-center" style="margin:10px;">
    <h4 style="margin: 5px; padding:0px;"><b>ICAO Type, Subfleet or Aircraft Specifications</b></h4>
  </div>

  <div class="row" style="margin-left:5px; margin-right:5px;">
    <div class="card border-blue-bottom" style="padding:10px;">
      {{ Form::open(array('route' => 'DBasic.specs_store', 'method' => 'post')) }}
        <input type="hidden" name="id" value="{{ $spec->id ?? '' }}">

        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-4">
            <label class="pl-1 mb-1" for="subfleet_id">Select Pre-Recorded Specifications for Editing</label>
            <select id="spec_selection" class="form-control select2" onchange="check_selection()">
              <option value="0">Please Select A Record</option>
              @foreach($allspecs as $listspec)
                @php
                  if ($listspec->subfleet) { $listdesc = $listspec->subfleet->type; }
                  elseif ($listspec->aircraft) { $listdesc = $listspec->aircraft->registration; }
                  elseif (!empty($listspec->icao_id)) { $listdesc = $listspec->icao_id; }
                  else { $listdesc = 'Mandatory Object Not Found !'; }
                @endphp
                <option value="{{ $listspec->id }}" @if($spec && $listspec->id == $spec->id) selected @endif>{{ $listspec->id }} : {{ $listspec->saircraft }} | {{ $listdesc }}</option>
              @endforeach
            </select>
          </div>

          <div class="col-sm-4 text-left align-middle"><br>
            <a id="edit_link" style="visibility: hidden" href="{{ route('DBasic.specs') }}" class="btn btn-primary pl-1 mb-1">Load Selected Record For Edit</a>
          </div>

          <div class="col-sm-4 text-left align-middle"><br>
            <a id="delete_link" style="visibility: hidden" href="{{ route('DBasic.specs') }}" class="btn btn-danger pl-1 mb-1">Delete !</a>
          </div>
        </div>

        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="icao_id">ICAO Type</label>
            <select id="icao_selection" name="icao_id" class="form-control select2" onchange="check_icao()">
              <option value="">Please Select An ICAO Type</option>
              @foreach($icaotypes as $icao_id)
                <option value="{{ $icao_id }}" @if($spec && $icao_id == $spec->icao_id) selected @endif>{{ $icao_id }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-4">
            <label class="pl-1 mb-1" for="subfleet_id">or SubFleet</label>
            <select id="subfleet_selection" name="subfleet_id" class="form-control select2" onchange="check_subfleet()">
              <option value="">or Please Select A Subfleet</option>
              @foreach($subfleets as $subfleet)
                <option value="{{ $subfleet->id }}" @if($spec && $subfleet->id == $spec->subfleet_id) selected @endif>{{ $subfleet->type }} : {{ $subfleet->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-sm-3">
            <label class="pl-1 mb-1" for="aircraft_id">or Aircraft</label>
            <select id="aircraft_selection" name="aircraft_id" class="form-control select2" onchange="check_aircraft()">
              <option value="">or Please Select An Aircraft</option>
              @foreach($aircraft as $ac)
                <option value="{{ $ac->id }}" @if($spec && $ac->id == $spec->aircraft_id) selected @endif>{{ $ac->registration }} @if($ac->registration != $ac->name)'{{ $ac->name }}'@endif : {{ $ac->icao }}</option>
              @endforeach
            </select>
          </div>
        </div>
        {{-- Addon Name, Sim Title --}}
        <hr>
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-3">
            <label class="pl-1 mb-1" for="saircraft">Aircraft or Addon Name</label>
            <input name="saircraft" type="text" class="form-control" placeholder="Zibo B737-800X" maxlength="50" value="{{ $spec->saircraft ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="stitle">Simulator Aircraft Title</label>
            <input name="stitle" type="text" class="form-control" placeholder="Boeing B737-800X" maxlength="30" value="{{ $spec->stitle ?? '' }}">
          </div>
        </div>
        {{-- ICAO Type Code, Engine Name --}}
        <hr>
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="icao">SimBrief ICAO Type (OFP & ATC)</label>
            <input name="icao" type="text" class="form-control" placeholder="B738" maxlength="4" value="{{ $spec->icao ?? '' }}">
          </div>
          <div class="col-sm-3">
            <label class="pl-1 mb-1" for="name">Aircraft Type Name (OFP)</label>
            <input name="name" type="text" class="form-control" placeholder="B737-800" maxlength="20" value="{{ $spec->name ?? '' }}">
          </div>
          <div class="col-sm-3">
            <label class="pl-1 mb-1" for="engines">Engine Name (OFP)</label>
            <input name="engines" type="text" class="form-control" placeholder="CFM56-7B26" maxlength="20" value="{{ $spec->engines ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="paxwgt">Passenger Weight</label>
            <input name="paxwgt" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="500" value="{{ $spec->paxwgt ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="bagwgt">Baggage Weight</label>
            <input name="bagwgt" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="300" value="{{ $spec->bagwgt ?? '' }}">
          </div>
        </div>
        {{-- Operational Weights --}}
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="bew">Basic Empty Weight</label>
            <input name="bew" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->bew ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="dow">Dry Operating Weight (1)</label>
            <input name="dow" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->dow ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mzfw">Max Zero Fuel Weight (1)</label>
            <input name="mzfw" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->mzfw ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mrw">Max Ramp/Taxi Weight</label>
            <input name="mrw" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->mrw ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mtow">Max Take Off Weight (1)</label>
            <input name="mtow" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->mtow ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mlw">Max Landing Weight (1)</label>
            <input name="mlw" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->mlw ?? '' }}">
          </div>
        </div>
        {{-- Design Specs --}}
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mfuel">Max Fuel Capacity (1)</label>
            <input name="mfuel" type="number" class="form-control" placeholder="{{ $units['weight'] }}" min="0" max="999999" value="{{ $spec->mfuel ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mrange">Max Range</label>
            <input name="mrange" type="number" class="form-control" placeholder="{{ $units['distance'] }}" min="0" max="99999" value="{{ $spec->mrange ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mceiling">Max Ceiling</label>
            <input name="mceiling" type="number" class="form-control" placeholder="{{ $units['altitude'] }}" min="0" max="99999" value="{{ $spec->mceiling ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mspeed">Max Speed</label>
            <input name="mspeed" type="number" class="form-control" placeholder="mach or {{ $units['speed'] }}" min="0" step="0.01" max="9999" value="{{ $spec->mspeed ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="cspeed">Optimum Speed</label>
            <input name="cspeed" type="number" class="form-control" placeholder="mach or {{ $units['speed'] }}" min="0" step="0.01" max="9999" value="{{ $spec->cspeed ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="mpax">Max Seat Capacity</label>
            <input name="mpax" type="number" class="form-control" placeholder="189" min="0" max="999" value="{{ $spec->mpax ?? '' }}">
          </div>
        </div>
        {{-- ATC --}}
        <hr>
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="cat">ATC Category (2)</label>
            <input name="cat" type="text" class="form-control" placeholder="M" maxlength="1" value="{{ $spec->cat ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="equip">ATC Equipment (2)</label>
            <input name="equip" type="text" class="form-control" placeholder="SDE1FGHIJ2J3J5RWY" maxlength="30" value="{{ $spec->equip ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="transponder">ATC Transponder (2)</label>
            <input name="transponder" type="text" class="form-control" placeholder="SB1" maxlength="30" value="{{ $spec->transponder ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="pbn">ATC PBN</label>
            <input name="pbn" type="text" class="form-control" placeholder="A1B1D1O1S1" maxlength="30" value="{{ $spec->pbn ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="selcal">SELCAL</label>
            <input name="selcal" type="text" class="form-control" placeholder="BKFS" maxlength="4" value="{{ $spec->selcal ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="hexcode">HEXCODE</label>
            <input name="hexcode" type="text" class="form-control" placeholder="2A7FK8" maxlength="6" value="{{ $spec->hexcode ?? '' }}">
          </div>
        </div>
        {{-- SimBrief Performance and Misc Items --}}
        <hr>
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="fuelfactor">Fuel Factor</label>
            <input name="fuelfactor" type="text" class="form-control" placeholder="P05" maxlength="3" value="{{ $spec->fuelfactor ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="cruiselevel">Cruise Level Offset</label>
            <input name="cruiselevel" type="text" class="form-control" placeholder="P1000" maxlength="5" value="{{ $spec->cruiselevel ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="airframe_id">SimBrief Airframe ID</label>
            <input name="airframe_id" type="text" class="form-control" placeholder="1234_197815072021" maxlength="50" value="{{ $spec->airframe_id ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="rvr">RVR</label>
            <input name="rvr" type="number" class="form-control" placeholder="550" min="0" max="800" value="{{ $spec->rvr ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="rmk">Add. Remark</label>
            <input name="rmk" type="text" class="form-control" placeholder="WX RADAR INOP" maxlength="25" value="{{ $spec->rmk ?? '' }}">
          </div>
          <div class="col-sm-2">
            <label class="pl-1 mb-1" for="crew">Operating Crew</label>
            <input name="crew" type="number" class="form-control" placeholder="6" min="0" max="20" value="{{ $spec->crew ?? '' }}">
          </div>
        </div>
        {{-- Form Actions --}}
        <hr>
        <div class="row" style="margin-bottom: 10px;">
          <div class="col-sm-2 text-left">
            <input type="hidden" name="active" value="0">
            <label class="pl-1 mb-1" for="active">Active <input name="active" type="checkbox" @if($spec && $spec->active == 1) checked="true" @endif class="form-control" value="1"></label>
          </div>
          <div class="col-sm-10 text-right">
            <button class="btn btn-primary pl-1 mb-1" type="submit">@if($spec && $spec->id) Update @else Save @endif</button>
          </div>
        </div>
      {{ Form::close() }}
    </div>
  </div>

  {{-- Custom Colored Placeholders --}}
  <style>
    ::placeholder { color: indianred !important; opacity: 0.6 !important; }
    :-ms-input-placeholder { color: indianred !important; }
    ::-ms-input-placeholder { color: indianred !important; }
  </style>
@endsection

@section('scripts')
  @parent
  <script type="text/javascript">
    // Simple Selection With Dropdown Change
    // Also keep buttons hidden until a valid selection
    const $oldlink = document.getElementById('edit_link').href;

    function check_selection() {
      if (document.getElementById('spec_selection').value === '0') {
        document.getElementById('edit_link').style.visibility = 'hidden';
        document.getElementById('delete_link').style.visibility = 'hidden';
      } else {
        document.getElementById('edit_link').style.visibility = 'visible';
        document.getElementById('delete_link').style.visibility = 'visible';
      }
      const selected_item = document.getElementById('spec_selection').value;
      const link_edit = '?spec_edit='.concat(selected_item);
      const link_delete = '?spec_delete='.concat(selected_item);

      document.getElementById('edit_link').href = $oldlink.concat(link_edit);
      document.getElementById('delete_link').href = $oldlink.concat(link_delete);
    }

    // De-Select others when one is selected (for select2 dropdowns)
    function check_icao() {
      if ($('#icao_selection').val() != '') {
        $('#subfleet_selection').val('').trigger('change');
        $('#aircraft_selection').val('').trigger('change');
      }
    }

    function check_subfleet() {
      if ($('#subfleet_selection').val() != '') {
        $('#icao_selection').val('').trigger('change');
        $('#aircraft_selection').val('').trigger('change');
      }
    }

    function check_aircraft() {
      if ($('#aircraft_selection').val() != '') {
        $('#icao_selection').val('').trigger('change');
        $('#subfleet_selection').val('').trigger('change');
      }
    }
  </script>
@endsection
