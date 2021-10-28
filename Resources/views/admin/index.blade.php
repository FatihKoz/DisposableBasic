@extends('admin.app')
@section('title', 'Disposable Basic VA Pack')

@section('content')
  <div class="card border-blue-bottom">
    <div class="content">
      <p>This module is designed to basic features for a Virtual Airline to operate.</p>
      <hr>
      <p><b>To Be Completed...</b></p>
      <hr>
      <p>By <a href="https://github.com/FatihKoz" target="_blank">B.Fatih KOZ</a> &copy; @php echo date('Y'); @endphp</p>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <div class="col-sm-12">
        <h4 style="margin:5px;">
          <b>Admin Features</b>
        </h4>
        <hr>
      <div class="col-sm-1">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-sm-5">
        <div class="card border-blue-bottom" style="padding:10px;">
            <a href="{{ route('DBasic.specs') }}">Define ICAO Type, Subfleet or Aircraft Specs</a>
            <br><br>
            Specs will be used for detailed SimBrief Flight planning, also they will be shown at Aircraft and Subfleet listing pages.
        </div>
      </div>
      <div class="col-sm-5">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-sm-1">
        {{-- Intentionally Left Blank --}}
      </div>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <div class="col-sm-12">
        <h4 style="margin:5px;">
          <b>Module Settings</b>
        </h4>
        <hr>
      <div class="col-sm-1">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-sm-5">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-sm-5">
        {{-- Intentionally Left Blank --}}
      </div>
      <div class="col-sm-1">
        {{-- Intentionally Left Blank --}}
      </div>
    </div>
  </div>
@endsection
