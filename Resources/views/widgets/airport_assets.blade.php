@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ $title }}
        <i class="fas {{ $icon }} m-1 float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 overflow-auto table-responsive">
      @include('DBasic::widgets.airport_assets_'.$type)
    </div>
    <div class="card-footer p-0 px-1 small text-end">
      <b>@lang('DBasic::common.count'): {{ $count }}</b>
    </div>
  </div>
@endif