@extends('admin.app')
@section('title', 'Disposable Basic')

@section('content')
  <div class="card border-blue-bottom" style="margin-bottom: 10px;">
    <div class="content">
      <p>This module is designed to provide basic features for a Virtual Airline to operate</p>
      <p>
        Documentation about this module can be found in the <b>README.md</b> file or at GitHub via this link 
        <a href="https://github.com/FatihKoz/DisposableBasic#readme" target="_blank" title="Online Readme">Online Readme</a>
      </p>
      <hr>
      <p><a href="https://github.com/FatihKoz" target="_blank">&copy; B.Fatih KOZ</a></p>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <h5 style="margin:5px;"><b>Module Features & Settings</b></h5>
    <div class="col-sm-12">
      <div class="col-sm-3">
        <div class="card border-blue-bottom" style="padding:10px;">
          <a href="{{ route('DBasic.specs') }}">Manage ICAO Type, Subfleet or Aircraft Specs</a>
          <br><br>
          Specs will be used for detailed SimBrief Flight planning, also they will be shown at Aircraft and Subfleet listing pages.
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card border-blue-bottom" style="padding:10px;">
          <a href="{{ route('DBasic.tech')}}">Manage Maintenance Periods, Pitch, Roll, Flap and Gear Limits</a>
          <br><br>
          Tech details, Flap and Gear Speeds may be used for Pirep evaluation purposes.
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card border-blue-bottom" style="padding:10px;">
          <a href="{{ route('DBasic.runway')}}">Manage Runways</a>
          <br><br>
          Runways may be used for proper SimBrief flight planning and Pirep evaluation purposes.
        </div>
      </div>
      <div class="col-sm-3">
        <div class="card border-blue-bottom" style="padding:10px;">
          <a href="{{ route('DBasic.health_check')}}">Database Check</a>
          <br><br>
          See missing airports or possible problematic records about mandatory relationships.
        </div>
      </div>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <div class="col-sm-12">
      <div class="col-sm-4">
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Manual Awarding</b>
          <br>
          @include('DBasic::admin.manual_awards')
          {{-- <span class="text-info">Award must be defined</span> --}}
        </div>
      </div>
      <div class="col-sm-4">
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Manual Payments</b>
          <br>
          @include('DBasic::admin.manual_payment')
          <span class="text-info">User's Airline must have enough funds to complete the transfer</span>
        </div>
      </div>
      <div class="col-sm-4">
        {{-- Manual Aircraft State Fix --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Fix Aircraft State</b>
          <br>
          <div style="margin-bottom: 5px;">
            {{ Form::open(array('route' => 'DBasic.park_aircraft', 'method' => 'post')) }}
            <table class="table table-striped text-left" style="margin-bottom: 2px;">
              <tr>
                <td>Enter Aircraft Registration</td>
                <td>
                  <input class="form-control" type="text" name="aircraft_reg" placeholder="TC-DSP" maxlength="7">
                </td>
              </tr>
            </table>
            <input type="submit" value="Park Aircraft">
            {{ Form::close() }}
          </div>
          <span class="text-danger"><b>If the aircraft has an active (in-progress) PIREP, it gets CANCELLED too !!!</b></span>
        </div>
      </div>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <div class="col-sm-12">
      <div class="col-sm-7">
        {{-- Discord Notification Group --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Discord</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Discord'])
          <span class="text-info">Create your webhook before enabling it here, also check laravel logs if the messages does not appear at your Discord Server</span>
        </div>
        {{-- Network Presence Check Group --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Network Presence Checks</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Network Checks'])
          <span class="text-info">"User Field Name" MUST MATCH your custom user field holding network id numbers</span>
        </div>
      </div>
      <div class="col-sm-5">
        {{-- Aircraft Group --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Aircraft</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Aircraft'])
          <span class="text-info">When enabled, module will change Aircraft states (Ground, In Use, In Flight) according to Pirep events</span>
        </div>
        {{-- Stable Approach Plugin Settings --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Stable Approach Plugin</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Stable Approach'])
          <span class="text-info">When enabled, module will be able to receive reports sent by Stable Approach plugin</span>
        </div>
        {{-- Auto Reject Group --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Auto Reject Settings</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Auto Reject'])
          <span class="text-info">Set margin to 0 if you do not want to use definable items (score, landing rate etc)</span>
        </div>        
      </div>
    </div>
  </div>
@endsection
