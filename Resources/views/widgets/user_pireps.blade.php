@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::common.reports')
        <i class="fas fa-upload float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 overflow-auto table-responsive">
      <table class="table table-sm table-borderless table-striped text-center text-nowrap align-middle mb-0">
        <thead>
          <tr>
            <th class="text-start">@lang('DBasic::common.flightno')</th>
            <th class="text-start">@lang('DBasic::common.orig')</th>
            <th class="text-start">@lang('DBasic::common.dest')</th>
            <th>@lang('DBasic::common.aircraft')</th>
            <th>@lang('DBasic::common.btime')</th>
            <th>@lang('DBasic::common.fuelused')</th>
            @ability('admin', 'admin-access')
              <th>@lang('DBasic::common.score')</th>
              <th>@lang('DBasic::common.lrate')</th>
            @endability
            @if(DB_Setting('dbasic.networkcheck', false))
              <th>Network</th>
            @endif
            <th class="text-end">@lang('DBasic::common.submitted')</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pireps as $pirep)
            <tr>
              <th class="text-start">
                @ability('admin', 'admin-access')
                  <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}"><i class="fas fa-info-circle me-1"></i></a>
                @endability
                {{ optional($pirep->airline)->code.' '.$pirep->flight_number }}
              </th>
              <td class="text-start">
                <a href="{{ route('frontend.airports.show', [$pirep->dpt_airport_id]) }}">{{ $pirep->dpt_airport_id }}</a>
              </td>
              <td class="text-start">
                <a href="{{ route('frontend.airports.show', [$pirep->arr_airport_id]) }}">{{ $pirep->arr_airport_id }}</a>
              </td>
              <td>
                <a href="{{ route('DBasic.aircraft', [$pirep->aircraft->registration ?? '']) }}">{{ optional($pirep->aircraft)->ident }}</a>
              </td>
              <td>{{ DB_ConvertMinutes($pirep->flight_time) }}</td>
              <td>{{ DB_ConvertWeight($pirep->fuel_used, $units['fuel']) }}</td>
              @ability('admin', 'admin-access')
                <td>{{ $pirep->score }}</td>
                <td>@if($pirep->landing_rate) {{ $pirep->landing_rate.' ft/min' }} @endif</td>
              @endability
              @if(DB_Setting('dbasic.networkcheck', false))
                <td>{!! DB_NetworkPresence($pirep, 'badge') !!}</td>
              @endif              
              <td class="text-end">
                {{ $pirep->submitted_at->diffForHumans().' | '.$pirep->submitted_at->format('d.M') }}
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    <div class="card-footer p-0 px-1 small text-end fw-bold">
      Latest {{ $limit }} Reports
    </div>
  </div>
@endif