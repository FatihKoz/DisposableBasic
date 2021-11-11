@extends('admin.app')
@section('title', 'Disposable Basic VA Pack')

@section('content')
  <div class="card border-blue-bottom" style="margin-bottom: 10px;">
    <div class="content">
      <p>This module is designed to provide basic features for a Virtual Airline to operate</p>
      <hr>
      <p><b>Documentation to be completed...</b></p>
      <hr>
      <p>By <a href="https://github.com/FatihKoz" target="_blank">B.Fatih KOZ</a> &copy; @php echo date('Y'); @endphp</p>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <h4 style="margin:5px;"><b>Admin Features</b></h4>
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
    <h4 style="margin:5px;"><b>Module Settings</b></h4>
    <div class="col-sm-12">
      <div class="col-sm-7">
        {{-- Aircraft Group --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Aircraft</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Aircraft'])
          <span class="text-info">When enabled, module will change Aircraft states (Ground, In Use, In Flight) according to Pirep events</span>
        </div>
        {{-- Discord Notification Group --}}
        <div class="card border-blue-bottom" style="padding:5px;">
          <b>Discord</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'Discord'])
          <span class="text-info">Create your webhook before enabling it here, also check laravel logs if the messages does not appear at your Discord Server</span>
        </div>
      </div>
      <div class="col-sm-5">
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
        {{-- IVAO and VATSIM Related Settings --}}
        {{--}}
          <div class="card border-blue-bottom" style="padding:5px;">
            <b>IVAO</b>
            <br>
            @include('DBasic::admin.settings_table', ['group' => 'IVAO'])
          </div>
          <div class="card border-blue-bottom" style="padding:5px;">
            <b>VATSIM</b>
            <br>
            @include('DBasic::admin.settings_table', ['group' => 'VATSIM'])
          </div>
        {{--}}
      </div>
    </div>
  </div>
@endsection
