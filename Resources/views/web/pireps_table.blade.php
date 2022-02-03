<table class="table table-sm table-borderless table-striped text-center align-middle mb-0">
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
      <th>@lang('DBasic::common.pilot')</th>
      <th class="text-end">@lang('DBasic::common.submitted')</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($pireps as $pirep)
      <tr @if ($pirep->state != 2) {!! DB_PirepState($pirep, 'row') !!} @endif>
        <th class="text-start">
          {{ optional($pirep->airline)->code.' '.$pirep->flight_number }}
        </th>
        <td class="text-start">
          {{ optional($pirep->dpt_airport)->full_name ?? $pirep->dpt_airport_id }}
        </td>
        <td class="text-start">
          {{ optional($pirep->arr_airport)->full_name ?? $pirep->arr_airport_id }}
        </td>
        <td>
          {{ optional($pirep->aircraft)->ident }}
        </td>
        <td>{{ DB_ConvertMinutes($pirep->flight_time) }}</td>
        <td>{{ DB_ConvertWeight($pirep->fuel_used, $units['fuel']) }}</td>
        @ability('admin', 'admin-access')
          <td>{{ $pirep->score }}</td>
          <td>@if($pirep->landing_rate) {{ $pirep->landing_rate.' ft/min' }} @endif</td>
        @endability
        <td>
          {{ optional($pirep->user)->name_private }}
        </td>
        <td class="text-end">
          {{ $pirep->submitted_at->diffForHumans().' | '.$pirep->submitted_at->format('d.M') }}
        </td>
      </tr>
    @endforeach
  </tbody>
</table>