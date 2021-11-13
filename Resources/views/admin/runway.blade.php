@extends('admin.app')
@section('title', 'Disposable Runway Management')

@section('content')
  <div class="card border-blue-bottom" style="margin-left:5px; margin-right:5px; margin-bottom:5px;">
    <div class="content">
      <p>All of the fields are mandatory except ILS Frequency, Localizer Course and AIRAC cycle code.</p>
      <p>
        This section is designed for quick management of a runway or some runways of an airport, it is not designed to insert all runways at once or for mass updating. 
        I highly and kindly advise to keep your runways updated via direct database imports periodically.
      </p>
      <br>
      <p><a href="https://github.com/FatihKoz" target="_blank">&copy; B.Fatih KOZ</a></p>
    </div>
  </div>

  <div class="row text-center" style="margin:5px;">
    <h4 style="margin: 5px; padding:0px;"><b>Runway Management</b></h4>
  </div>

  <div class="row" style="margin-left:5px; margin-right:5px;">
    <div class="card border-blue-bottom" style="padding:10px;">
      <div class="row" style="margin-bottom: 10px;">
        <div class="col-sm-4">
          <label class="pl-1 mb-1" for="airport_id">Select an Airport to load it's runways</label>
          <select id="icao_selection" name="airport_id" class="form-control select2" style="width: 100%;" onchange="Check_Airport_Selection()">
            <option value="0">Please select an airport...</option>
            @foreach($airports_r as $airport)
              <option value="{{ $airport->id }}" @if($selected_a && $selected_a == $airport->id) selected @endif>{{ $airport->id.' : '.$airport->name }}</option>
            @endforeach
          </select>
        </div>
        <div class="col-sm-3 text-left align-middle"><br>
          <a id="load_link" style="visibility: hidden" href="{{ route('DBasic.runway') }}" class="btn btn-primary pl-1 mb-1">Load Runways of Selected Airport</a>
        </div>
        <div class="col-sm-1">
          {{-- Left Blank For Separation --}}
        </div>
        @if(!$runways)
          <div class="col-sm-4">
            <label class="pl-1 mb-1" for="new_selection">Or select an Airport to create a new runway record</label>
            <select id="new_selection" class="form-control select2" style="width: 100%;" onchange="Check_New_Selection()">
              <option value="0">Please select an airport...</option>
              @foreach($airports_n as $airport)
                <option value="{{ $airport->id }}">{{ $airport->id.' : '.$airport->name }}</option>
              @endforeach
            </select>
          </div>
        @endif
      </div>
      {{-- Selected Airport's Runways --}}
      @if($runways)
      <hr>
        @foreach($runways as $runway)
          @include('DBasic::admin.runway_form')
          @if(!$loop->last) <hr> @endif
        @endforeach
      @endif
      {{-- New Runway Entry --}}
      <hr>
      @include('DBasic::admin.runway_form', ['runway' => null])
    </div>
  </div>

  {{-- Custom placeholder colors --}}
  <style>
    ::placeholder { color: indianred !important; opacity: 0.6 !important; }
    :-ms-input-placeholder { color: indianred !important; }
    ::-ms-input-placeholder { color: indianred !important; }
  </style>
@endsection

@section('scripts')
  @parent
  <script type="text/javascript">
    // Simple selection with dropdown change
    // Also keeps buttons hidden until a valid selection
    const $main_link = String("{{ route('DBasic.runway') }}");

    function Check_Airport_Selection() {
      if (document.getElementById('icao_selection').value === '0') {
        document.getElementById('load_link').style.visibility = 'hidden';
      } else {
        document.getElementById('load_link').style.visibility = 'visible';
      }

      const selected_airport = document.getElementById('icao_selection').value;
      const link_load = '?airport='.concat(selected_airport);

      document.getElementById('load_link').href = $main_link.concat(link_load);
    }

    function Check_New_Selection() {
      if (document.getElementById('new_selection').value != '0') {
        var selected_icao = document.getElementById('new_selection').value;
        document.getElementById('airport_id').value = selected_icao;
      } else {
        document.getElementById('airport_id').value = null;
      }
    }
  </script>
@endsection
