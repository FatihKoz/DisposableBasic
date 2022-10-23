@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::widgets.flight_board')
        <i class="fas fa-paper-plane float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-sm table-borderless table-striped text-center text-nowrap align-middle mb-0">
        <tr>
          <th class="text-start" style="width: 50px;">@lang('DBasic::common.airline')</th>
          <th class="text-start">@lang('DBasic::common.flightno')</th>
          <th>@lang('DBasic::common.orig')</th>
          <th>@lang('DBasic::common.dest')</th>
          <th>@lang('DBasic::common.aircraft')</th>
          <th>@lang('DBasic::common.altitude')</th>
          <th>@lang('DBasic::common.speed')</th>
          <th>@lang('DBasic::common.status')</th>
          <th class="text-end">@lang('DBasic::common.pilot')</th>
        </tr>
        @foreach($flights as $flight)
          <tr>
            <td class="text-start" style="width: 50px;">
              @if(filled($flight->airline->logo))
                <img src="{{ $flight->airline->logo }}" style="max-height: 30px;">
              @else
                {{ $flight->airline->name }}
              @endif
            </td>
            <td class="text-start">
              @ability('admin', 'admin-access')
                <a href="{{ route('frontend.pireps.show', [$flight->id]) }}">
                  <i class="fas fa-info-circle me-1" title="@lang('DBasic::widgets.view_pirep')"></i>
                </a>
              @endability
              <span title="@if(filled($flight->route_code)){{ 'Code '.$flight->route_code }}@endif @if(filled($flight->route_leg)){{ ' Leg #'.$flight->route_leg }}@endif">
                {{ $flight->airline->code.' '.$flight->flight_number }}
              </span>
            </td>
            <td>
              <a href="{{ route('frontend.airports.show', [$flight->dpt_airport_id]) }}" title="{{ optional($flight->dpt_airport)->iata.' '.optional($flight->dpt_airport)->name }}">
                {{ $flight->dpt_airport_id }}
              </a>
            </td>
            <td>
              <a href="{{ route('frontend.airports.show', [$flight->arr_airport_id]) }}" title="{{ optional($flight->arr_airport)->iata.' '.optional($flight->arr_airport)->name }}">
                {{ $flight->arr_airport_id }}
              </a>
            </td>
            <td>
              <a href="{{ route('DBasic.aircraft', [$flight->aircraft->registration]) }}">
                {{ $flight->aircraft->registration.' ('.$flight->aircraft->icao.')' }}
              </a>
            </td>
            <td>{{ optional($flight->position)->altitude.' ft' }}</td>
            <td>{{ optional($flight->position)->gs.' kts' }}</td>
            <td>{{ PirepStatus::label($flight->status) }}</td>
            <td class="text-end">
              <a href="{{ route('frontend.profile.show', [$flight->user_id]) }}">
                @if(Theme::getSetting('roster_ident'))
                  {{ $flight->user->ident.' - ' }}
                @endif
                {{ $flight->user->name_private }}
              </a>
            </td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
@endif