@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::widgets.airport_info')
        <i class="fas fa-info-circle float-end"></i>
      </h5>
    </div>
    <div class="card-body p-1">
      <select id="airport_selector" class="form-control select2" style="width: 100%;" onchange="Check_Airport_Selection()">
        <option value="ZZZZ">@lang('DBasic::widgets.select_apt')</option>
        @foreach($airports as $airport)
          <option value="{{ $airport->id }}">
            {{ $airport->id.' : '.$airport->name }} @if(filled($airport->location)) {{ '('.$airport->location.')' }} @endif
          </option>
        @endforeach
      </select>
    </div>
    <div class="card-footer p-1 text-end small">
      <span class="float-start pt-1">
        @if($config['type'] === 'hubs') @lang('DBasic::widgets.hubs_only') @endif
        @if($config['type'] === 'nohubs') @lang('DBasic::widgets.nonhubs_only') @endif
      </span>
      <a id="airport_link" style="visibility: hidden;" href="{{ route($apt_route, '') }}" class="btn btn-sm bg-success p-0 px-2">@lang('DBasic::widgets.go')</a>
    </div>
  </div>

  <script type="text/javascript">
    // Simple Selection With Dropdown Change
    const oldlink = document.getElementById('airport_link').href;

    function Check_Airport_Selection() {
      if (document.getElementById('airport_selector').value === 'ZZZZ') {
        document.getElementById('airport_link').style.visibility = 'hidden';
      } else {
        document.getElementById('airport_link').style.visibility = 'visible';
      }
      const selected_ap = document.getElementById('airport_selector').value;
      const newlink = '/'.concat(selected_ap);
      document.getElementById('airport_link').href = oldlink.concat(newlink);
    }
  </script>
@endif
