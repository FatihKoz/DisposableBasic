<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      {{ $subfleet->name }}
      <i class="fas fa-link float-end"></i>
    </h5>
  </div>
  <div class="card-body p-0 table-responsive">
    <table class="table table-sm table-borderless table-striped text-nowrap mb-0">
      <tr>
        <th style="width: 30%;" scope="row">@lang('DBasic::common.type')</th>
        <td>{{ $subfleet->type }}</td>
      </tr>
      @if($subfleet->fares_count > 0)
        <tr>
          <th scope="row">@lang('DBasic::common.config')</th>
          <td>
            @foreach($subfleet->fares as $fare)
              @if(!$loop->first) &bull; @endif
              {{ $fare->name }}
              {{ number_format($fare->pivot->capacity) }}
              @if($fare->type == 1) {{ $units['weight'] }} @else Pax @endif
            @endforeach
          </td>
        </tr>
      @endif
      @if(filled($subfleet->typeratings))
        <tr>
          <th scope="row">Type Rating(s)</th>
          <td>
            @foreach($subfleet->typeratings as $rating)
              @if(!$loop->first) &bull; @endif
              {{ $rating->name }}
            @endforeach
          </td>
        </tr>
      @endif
      <tr>
        <th scope="row">@lang('DBasic::common.airline')</th>
        <td>
          <a href="{{ route('DBasic.airline', [$subfleet->airline->icao ?? '']) }}">{{ $subfleet->airline->name ?? '' }}</a>
        </td>
      </tr>
      <tr>
        <th scope="row">@lang('DBasic::common.base')</th>
        <td>
          <a href="{{ route('DBasic.hub', [$subfleet->hub_id ?? '']) }}">{{ $subfleet->hub->full_name ?? '' }}</a>
        </td>
      </tr>
      @if($subfleet->flights_count > 0)
        <tr>
          <th scope="row">@lang('DBasic::common.flights')</th>
          <td>{{ number_format($subfleet->flights_count) }}</td>
        </tr>
      @endif
    </table>
  </div>
</div>
