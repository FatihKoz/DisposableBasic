<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      @lang('DBasic::widgets.sundetails') {{ $location }}
      <i class="fas {{ $icon }} float-end m-1"></i>
    </h5>
  </div>
  <div class="card-body p-0 table-responsive">
    @if(isset($details))
      <table class="table table-borderless table-sm table-striped text-start align-middle mb-0">
        <tr>
          <td>@lang('DBasic::widgets.twilight_begin')</td>
          <td class="text-end">{{ $twilight_begin }}</td>
        </tr>
        <tr>
          <td>@lang('DBasic::widgets.sunrise')</td>
          <td class="text-end">{{ $sunrise }}</td>
        </tr>
        <tr>
          <td>@lang('DBasic::widgets.sunset')</td>
          <td class="text-end">{{ $sunset }}</td>
        </tr>
        <tr>
          <td>@lang('DBasic::widgets.twilight_end')</td>
          <td class="text-end">{{ $twilight_end }}</td>
        </tr>
      </table>
    @elseif(isset($error))
      <span class="text-danger m-1">{{ $error }}</span>
    @endif
  </div>
  @if($footer_note)
    <div class="card-footer p-0 px-1 text-end small">
      <b>{{ $footer_note }}</b>
    </div>
  @endif
</div>