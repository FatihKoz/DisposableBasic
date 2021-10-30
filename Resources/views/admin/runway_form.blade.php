<div class="row" style="margin-bottom: 5px; padding-left: 5px;">
  {{ Form::open(array('route' => 'DBasic.runway_store', 'method' => 'post')) }}
  <input name="id" type="hidden" value="{{ $runway->id ?? '' }}">
  <div class="col-sm-1">
    <label class="pl-1 mb-1" for="airport_id">ICAO</label>
    <input id="airport_id" name="airport_id" type="text" class="form-control" placeholder="LTAI" maxlenght="5" value="{{ $runway->airport_id ?? $selected_a }}" readonly>
  </div>
  <div class="col-sm-1">
    <label class="pl-1 mb-1" for="runway_ident">Runway Ident</label>
    <input name="runway_ident" type="text" class="form-control" placeholder="36C" maxlength="3" value="{{ $runway->runway_ident ?? '' }}">
  </div>
  <div class="col-sm-2">
    <label class="pl-1 mb-1" for="lat">Threshold Coordinates (lat/lon)</label>
    <div class="input-group">
      <input name="lat" type="text" title="Latitude" class="form-control" placeholder="48.32158" maxlength="10" value="{{ $runway->lat ?? '' }}">
      <input name="lon" type="text" title="Longitude" class="form-control" placeholder="32.12547" maxlength="10" value="{{ $runway->lon ?? '' }}">
    </div>
  </div>
  <div class="col-sm-1">
    <label class="pl-1 mb-1" for="heading">Heading</label>
    <input name="heading" type="number" class="form-control" placeholder="358" min="0" max="360" value="{{ $runway->heading ?? '' }}">
  </div>
  <div class="col-sm-1">
    <label class="pl-1 mb-1" for="lenght">Lenght</label>
    <input name="lenght" type="number" class="form-control" placeholder="3200" min="0" max="99999" value="{{ ltrim($runway->lenght ?? '', '0') }}">
  </div>
  <div class="col-sm-2">
    <label class="pl-1 mb-1" for="ils_freq">ILS/LOC Frequency</label>
    <input name="ils_freq" type="text" class="form-control" placeholder="109.90" maxlength="7" value="{{ $runway->ils_freq ?? '' }}">
  </div>
  <div class="col-sm-2">
    <label class="pl-1 mb-1" for="loc_course">Localizer Course</label>
    <input name="loc_course" type="number" class="form-control" placeholder="358" min="0" max="360" value="{{ $runway->loc_course ?? ''}}">
  </div>
  <div class="col-sm-1">
    <label class="pl-1 mb-1" for="airac">AIRAC Cycle</label>
    <input name="airac" type="number" class="form-control" placeholder="2109" min="0" max="9999" value="{{ $runway->airac ?? ''}}">
  </div>
  {{-- Form Actions --}}
  <div class="col-sm-1 text-left align-middle">
    <br>
    <button class="btn btn-primary pl-1 mb-1" type="submit">@if($runway && $runway->id) Update @else Add @endif</button>
    @if($runway && $runway->id)
      <a id="delete_link" href="{{ route('DBasic.runway') }}?runway_delete={{ $runway->id }}@if($selected_a)&airport={{ $selected_a }}@endif" class="btn btn-danger pl-1 mb-1">Delete !</a>
    @endif
  </div>
  {{ Form::close() }}
</div>