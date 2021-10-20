<table class="table table-sm table-borderless table-striped text-start mb-0 align-middle">
  <tr>
    <th>@lang('DBasic::common.flightid')</th>
    <th>@lang('DBasic::common.orig')</th>
    <th>@lang('DBasic::common.dest')</th>
    <th>@lang('DBasic::common.aircraft')</th>
    <th class="text-center">@lang('DBasic::common.btime')</th>
    {{-- <th class="text-center">@lang('DisposableBasic::common.score')</th> --}}
    {{-- <th class="text-center">@lang('DisposableBasic::common.lndrate')</th> --}}
    <th>@lang('DBasic::common.pic')</th>
    <th class="text-center">@lang('DBasic::common.submitted')</th>
    <th class="text-center">@lang('common.status')</th>
  </tr>
  @foreach($pireps as $pirep)
    <tr>
      <td>
        @ability('admin', 'admin-access')
          <a href="{{ route('frontend.pireps.show', [$pirep->id]) }}"><i class="fas fa-info-circle me-3"></i></a>
        @endability
        <b>{{ optional($pirep->airline)->code }} {{ $pirep->flight_number }}</b>
      </td>
      <td>
        <a href="{{ route('frontend.airports.show', [$pirep->dpt_airport_id]) }}">{{ $pirep->dpt_airport_id }} {{ optional($pirep->dpt_airport)->name }}</a>
      </td>
      <td>
        <a href="{{ route('frontend.airports.show', [$pirep->arr_airport_id]) }}">{{ $pirep->arr_airport_id }} {{ optional($pirep->arr_airport)->name }}</a>
      </td>
      <td>
        @if($pirep->aircraft)
          {{ $pirep->aircraft->registration }} ({{ $pirep->aircraft->icao }})
        @endif
      </td>
      <td class="text-center">
        @minutestotime($pirep->flight_time)
      </td>
      {{-- <td class="text-center">{{ $pirep->score }}</td> --}}
      {{-- <td class="text-center">@if($pirep->landing_rate) {{ $pirep->landing_rate }} ft/min @endif</td> --}}
      <td>
        <a href="{{ route('frontend.users.show.public', [$pirep->user_id]) }}">{{ optional($pirep->user)->name_private }}</a>
      </td>
      <td class="text-center">
        @if(filled($pirep->submitted_at))
          {{ $pirep->submitted_at->diffForHumans() }}
        @endif
      </td>
      <td class="text-center">{{ $pirep->state }}</td>
    </tr>
  @endforeach
</table>