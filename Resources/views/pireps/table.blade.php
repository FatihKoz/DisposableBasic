<table class="table table-sm table-borderless table-striped text-center text-nowrap align-middle mb-0">
  <thead>
    <tr>
      <th class="text-start">@sortablelink('flight_number', __('DBasic::common.flightno'))</th>
      <th class="text-start">@sortablelink('dpt_airport_id', __('DBasic::common.orig'))</th>
      <th class="text-start">@sortablelink('arr_airport_id', __('DBasic::common.dest'))</th>
      @if(!isset($ac_page))
        <th>@sortablelink('aircraft.registration', __('DBasic::common.aircraft'))</th>
      @endif
      <th>@sortablelink('flight_time', __('DBasic::common.btime'))</th>
      <th>@sortablelink('fuel_used', __('DBasic::common.fuelused'))</th>
      @ability('admin', 'admin-access')
        <th>@sortablelink('score', __('DBasic::common.score'))</th>
        <th>@sortablelink('landing_rate', __('DBasic::common.lrate'))</th>
        @if(Theme::getSetting('gen_stable_approach'))
          <th>FDM Result</th>
        @endif
      @endability
      @if(DB_Setting('dbasic.networkcheck', false))
        <th>Network</th>
      @endif
      <th class="text-end">@sortablelink('user.name', __('DBasic::common.pilot'))</th>
      <th class="text-end">@sortablelink('submitted_at', __('DBasic::common.submitted'))</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($pireps as $pirep)
      <tr @if ($pirep->state != 2) {!! DB_PirepState($pirep, 'row') !!} @endif>
        <th class="text-start">
          @ability('admin', 'admin-access')
            <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}"><i class="fas fa-info-circle me-1"></i></a>
          @endability
          {{ optional($pirep->airline)->code.' '.$pirep->flight_number }}
          {{--}}
          @ability('admin', 'admin-user')
            @if($DSpecial && filled($pirep->route_code) && filled($pirep->route_leg))
              <a href="{{ route('DSpecial.tour_remove', [$pirep->id]) }}">
                <i class="fas fa-exclamation-circle text-danger mx-1"
                  onclick="return confirm('Are you really sure ?\nRemoving tour details from the pirep is irreversible !!!')"
                  title="Remove Tour details from Pirep !">
                </i>
              </a>
            @endif
          @endability
          {{--}}
        </th>
        <td class="text-start">
          <a href="{{ route('frontend.airports.show', [$pirep->dpt_airport_id]) }}" title="{{ optional($pirep->dpt_airport)->name }}">
            @if(empty($compact_view))
              {{ optional($pirep->dpt_airport)->full_name ?? $pirep->dpt_airport_id }}</a>
            @else 
              {{ $pirep->dpt_airport_id }}
            @endif
        </td>
        <td class="text-start">
          <a href="{{ route('frontend.airports.show', [$pirep->arr_airport_id]) }}" title="{{ optional($pirep->arr_airport)->name }}">
            @if(empty($compact_view))
              {{ optional($pirep->arr_airport)->full_name ?? $pirep->arr_airport_id }}</a>
            @else 
              {{ $pirep->arr_airport_id }}
            @endif
        </td>
        @if(!isset($ac_page))
          <td>
            <a href="{{ route('DBasic.aircraft', [$pirep->aircraft->registration ?? '']) }}">{{ optional($pirep->aircraft)->ident }}</a>
          </td>
        @endif
        <td>
          {{ DB_ConvertMinutes($pirep->flight_time) }}
          @ability('admin', 'admin-access')
            @if(($pirep->flight_time - $pirep->planned_flight_time) > 20)
              <i class="fas fa-clock text-danger mx-1" title="Check Flight Time"></i>
            @endif
          @endability
        </td>
        <td>
          {{ DB_ConvertWeight($pirep->fuel_used, $units['fuel']) }}
          @ability('admin', 'admin-access')
            @if(filled($pirep->simbrief) && ($pirep->fuel_used->local() - ($pirep->simbrief->xml->fuel->enroute_burn + ($pirep->simbrief->xml->fuel->contingency * 1.15) + ($pirep->simbrief->xml->fuel->taxi * 2)) > 100))
              <i class="fas fa-gas-pump text-danger mx-1" title="Check Fuel Used"></i>
            @endif
          @endability
        </td>
        @ability('admin', 'admin-access')
          <td>{{ $pirep->score }}</td>
          <td>@if($pirep->landing_rate) {{ $pirep->landing_rate.' ft/min' }} @endif</td>
          @if(Theme::getSetting('gen_stable_approach'))
            <td>@widget('DBasic::StableApproach', ['pirep' => $pirep])</td>
          @endif
        @endability
        @if(DB_Setting('dbasic.networkcheck', false))
          <td>{!! DB_NetworkPresence($pirep, 'badge') !!}</td>
        @endif
        <td class="text-end">
          <a href="{{ route('frontend.users.show.public', [$pirep->user_id]) }}">
            @if(Theme::getSetting('roster_ident'))
              {{ optional($pirep->user)->ident.' - ' }}
            @endif
            {{ optional($pirep->user)->name_private }}
          </a>
        </td>
        <td class="text-end">
          @ability('admin', 'admin-access')
            @if($pirep->comments_count > 0 || filled($pirep->notes))
              <i class="fas fa-file-alt text-secondary mx-1" title="Has Comments / Notes"></i>
            @endif
          @endability
          {{ $pirep->submitted_at->diffForHumans().' | '.$pirep->submitted_at->format('d.M') }}
        </td>
      </tr>
    @endforeach
  </tbody>
</table>