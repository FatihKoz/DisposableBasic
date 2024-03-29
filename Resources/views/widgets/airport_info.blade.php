@if($is_visible)
  <form class="form">
    @csrf
    <div class="card mb-2">
      <div class="card-header p-1">
        <h5 class="m-1">
          @lang('DBasic::widgets.airport_info')
          <i class="fas fa-info-circle float-end"></i>
        </h5>
      </div>
      <div class="card-body p-1">
        <select style="width: 100%;" class="form-control airport_search {{ $hubs_only }}" name="airport_selector" id="airport_selector" onchange="Check_Airport_Selection()"></select>
      </div>
      <div class="card-footer p-1 text-end small">
        <span class="float-start pt-1">
          @if($config['type'] === 'hubs') @lang('DBasic::widgets.hubs_only') @endif
        </span>
        <a id="airport_link" style="visibility: hidden;" href="{{ route($apt_route, '') }}" class="btn btn-sm bg-success p-0 px-2">@lang('DBasic::widgets.go')</a>
      </div>
    </div>
  </form>
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
  @include('DBasic::scripts.airport_search')
@endif
