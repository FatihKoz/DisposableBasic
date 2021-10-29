@extends('admin.app')
@section('title', 'Disposable Basic VA Pack')

@section('content')
  <div class="card border-blue-bottom" style="margin-bottom: 10px;">
    <div class="content">
      <p>This module is designed to basic features for a Virtual Airline to operate.</p>
      <hr>
      <p><b>To Be Completed...</b></p>
      <hr>
      <p>By <a href="https://github.com/FatihKoz" target="_blank">B.Fatih KOZ</a> &copy; @php echo date('Y'); @endphp</p>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <h4 style="margin:5px;"><b>Admin Features</b></h4>
    <div class="col-sm-12">
      <div class="col-sm-4">
        <div class="card border-blue-bottom" style="padding:10px;">
            <a href="{{ route('DBasic.specs') }}">Define ICAO Type, Subfleet or Aircraft Specs</a>
            <br><br>
            Specs will be used for detailed SimBrief Flight planning, also they will be shown at Aircraft and Subfleet listing pages.
        </div>
      </div>
      <div class="col-sm-4">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-sm-4">
        {{-- Intentionally Left Blank --}}
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
          <br>
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
        {{-- IVAO and VATSIM --}}
        <div class="card border-blue-bottom" style="padding:10px;">
          <b>IVAO</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'IVAO'])
        </div>
        <div class="card border-blue-bottom" style="padding:10px;">
          <b>VATSIM</b>
          <br>
          @include('DBasic::admin.settings_table', ['group' => 'VATSIM'])
        </div>
      </div>
    </div>
  </div>
@endsection
