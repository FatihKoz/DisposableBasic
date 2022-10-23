<table class="table table-sm table-striped table-borderless text-center text-nowrap align-middle mb-0">
  <tr>
    <th class="text-start">@lang('DBasic::common.flightno')</th>
    <th>@lang('DBasic::common.orig_abr')</th>
    <th>@lang('DBasic::common.dest_abr')</th>
    <th>@lang('DBasic::common.aircraft')</th>
    <th>@lang('DBasic::common.btime')</th>
    <th class="text-end">@lang('DBasic::common.pilot')</th>
    <th class="text-end">@lang('DBasic::common.submitted')</th>
  </tr>
  @foreach($assets as $pirep)
    <tr>
      <td class="text-start">
        {{ optional($pirep->airline)->code.' '.$pirep->flight_number }}
      </td>
      <td>
        @if($location != $pirep->dpt_airport_id)
          <a href="{{ route('frontend.airports.show', [$pirep->dpt_airport_id]) }}" title="{{ optional($pirep->dpt_airport)->name }}">{{ $pirep->dpt_airport_id }}</a>
        @else
          {{ $pirep->dpt_airport_id }}
        @endif
      </td>
      <td>
        @if($location != $pirep->arr_airport_id)
          <a href="{{ route('frontend.airports.show', [$pirep->arr_airport_id]) }}" title="{{ optional($pirep->arr_airport)->name }}">{{ $pirep->arr_airport_id }}</a>
        @else
          {{ $pirep->arr_airport_id }}
        @endif
      </td>
      <td>
        @if($pirep->aircraft)
          <a href="{{ route('DBasic.aircraft', [optional($pirep->aircraft)->registration ?? '']) }}">{{ optional($pirep->aircraft)->ident }}</a>
        @endif
      </td>
      <td>
        {{ DB_ConvertMinutes($pirep->flight_time) }}
      </td>
      <td class="text-end">
        <a href="{{ route('frontend.profile.show', [$pirep->user_id]) }}">
          @if(Theme::getSetting('roster_ident'))
            {{ optional($pirep->user)->ident.' - ' }}
          @endif
          {{ optional($pirep->user)->name_private }}
        </a>
      </td>
      <td class="text-end">
        {{ $pirep->submitted_at->diffForHumans() }}
      </td>
    </tr>
  @endforeach
</table>
