<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      @lang('DBasic::widgets.ftimemultip')
      <i class="fas fa-calculator float-end"></i>
    </h5>
  </div>
  <div class="card-body p-1">
    <form class="form-group" id="ftmultip">
      <div class="row row-cols-2 mb-1">
        <div class="col">
          <label for="fh">@lang('DBasic::widgets.hours')</label>
          <input id="fh" type="number" class="form-control form-control-sm" />
        </div>
        <div class="col">
          <label for="fm">@lang('DBasic::widgets.minutes')</label>
          <input id="fm" type="number" class="form-control form-control-sm" min="0" max="59" step="1"/>
        </div>
      </div>
      <div class="row row-cols-2 mb-1">
        <div class="col">
          <label for="fmt">@lang('DBasic::widgets.multiplier')</label>
          <input id="fmt" type="number" class="form-control form-control-sm" step="0.1"/>
        </div>
        <div class="col">
          <label for="fr">@lang('DBasic::widgets.result')</label>
          <input id="fr" type="text" class="form-control form-control-sm" maxlength="8" disabled/>
        </div>
      </div>
    </form>
  </div>
  <div class="card-footer p-1 text-end">
    <input type="button" onclick="TimeMultiplier()" class="btn btn-sm bg-success p-0 px-2" value="@lang('DBasic::widgets.calculate')">
  </div>
</div>
{{-- Flight Time Multiplier/Calculator Script --}}
<script type="text/javascript">
  function TimeMultiplier() {
    // Get form entries
    var h = document.getElementById('fh').value;
    var m = document.getElementById('fm').value;
    var factor = document.getElementById('fmt').value;
    // Do the calculation
    var seconds = (h * 60 * 60) + (m * 60);
    var newSeconds= factor * seconds;
    var date = new Date(newSeconds * 1000);
    var hh = date.getUTCHours();
    var mm = date.getUTCMinutes();
    // Style the result
    if (hh < 10) {hh = '0' + hh;}
    if (mm < 10) {mm = '0' + mm;}
    var result = hh + ':' + mm;
    // Return it back
    document.getElementById('fr').value = result;
  }
</script>
