@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::widgets.fleetoverview')
        <i class="fas fa-plane float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 overflow-auto table-responsive">
      <table class="table table-sm table-borderless table-striped text-start text-nowrap align-middle mb-0">
        <tr>
          <th>{{ $col_header }}</th>
          <th class="text-end pe-1">@lang('DBasic::common.count')</th>
        </tr>
        @foreach ($fleet as $ac)
          <tr>
            <td>
              @if($type === 'icao')
                {{ $ac->icao }}
              @elseif($type === 'subfleet')
                <a href="{{ route('DBasic.subfleet', [$ac->subfleet->type ?? '']) }}">{{ optional($ac->subfleet)->name.' | '.optional(optional($ac->subfleet)->airline)->icao  }}</a>
              @else
                <a href="{{route('frontend.airports.show', [$ac->airport_id])}}">{{ $ac->airport->name ?? $ac->airport_id }}</a>
                @if($hubs && optional($ac->airport)->hub) <i class="fas fa-home pt-1 float-end"></i> @endif
              @endif
            </td>
            <td class="text-end pe-1">{{ $ac->totals }}</td>
          </tr>
        @endforeach
      </table>
    </div>
    <div class="card-footer p-0 px-1 text-end small">
      <b>{{ $footer_note }}</b>
    </div>
  </div>
@endif
