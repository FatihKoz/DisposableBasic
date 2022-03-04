@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::common.stats') {{ $header_note }}
        <i class="fas {{ $icon }} float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-sm table-borderless table-striped text-start text-nowrap align-middle mb-0">
        @foreach($stats as $key => $value)
          <tr>
            <th>{{ $key }}</th>
            <td class="text-end">{{ $value }}</td>
          </tr>
        @endforeach
      </table>
    </div>
    @if($footer_note)
      <div class="card-footer p-0 px-1 small text-end fw-bold">
        {{ $footer_note }}
      </div>
    @endif
  </div>
@endif