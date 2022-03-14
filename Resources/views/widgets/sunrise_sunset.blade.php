@if(filled($details) && $card_view === true)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::widgets.sundetails')
        <i class="fas {{ $icon }} float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-borderless table-sm table-striped text-start text-nowrap align-middle mb-0">
        <tr>
          <td>@lang('DBasic::widgets.twilight_begin')</td>
          <td class="text-end">{{ $details['twilight_begin'] ?? '' }}</td>
        </tr>
        <tr>
          <td>@lang('DBasic::widgets.sunrise')</td>
          <td class="text-end">{{ $details['sunrise'] ?? '' }}</td>
        </tr>
        <tr>
          <td>@lang('DBasic::widgets.sunset')</td>
          <td class="text-end">{{ $details['sunset'] ?? '' }}</td>
        </tr>
        <tr>
          <td>@lang('DBasic::widgets.twilight_end')</td>
          <td class="text-end">{{ $details['twilight_end'] ?? '' }}</td>
        </tr>
      </table>
    </div>
    @if($footer_note)
      <div class="card-footer p-0 px-1 text-end small fw-bold">
        <span class="float-start">{{ $location }}</span>
        {{ $footer_note }}
      </div>
    @endif
  </div>
@elseif(filled($details) && $card_view === false)
  <div class="p-1 fw-bold small text-center">
    @if(isset($details['twilight_begin']))
      <i class="fas fa-cloud-sun mx-2" title="@lang('DBasic::widgets.twilight_begin')"></i>{{ $details['twilight_begin'] }}
    @endif
    @if(isset($details['sunrise']))
      <i class="fas fa-sun mx-2" title="@lang('DBasic::widgets.sunrise')"></i>{{ $details['sunrise'] }}
    @endif
    @if(isset($details['sunset']))
      <i class="fas fa-moon mx-2" title="@lang('DBasic::widgets.sunset')"></i>{{ $details['sunset'] }}
    @endif
    @if(isset($details['twilight_end']))
      <i class="fas fa-cloud-moon mx-2" title="@lang('DBasic::widgets.twilight_end')"></i>{{ $details['twilight_end'] }}
    @endif
  </div>
@endif