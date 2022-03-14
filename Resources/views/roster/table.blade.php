<table class="table table-sm table-borderless table-striped text-start text-nowrap mb-0 align-middle">
  <tr>
    <th>@lang('common.name')</th>
    <th>@lang('DBasic::common.rank')</th>
    @if(empty($airline_view))
      <th>@lang('DBasic::common.airline')</th>
    @endif
    @if(!isset($type) || isset($type) && $type != 'hub')
      <th>@lang('DBasic::common.base')</th>
    @endif
    @if(!isset($type) || isset($type) && $type != 'visitor')
      <th>@lang('DBasic::common.location')</th>
    @endif
    @if(!isset($type))
      <th class="text-center">@lang('DBasic::common.awards')</th>
    @endif
    <th class="text-center">@lang('DBasic::common.flights')</th>
    <th class="text-center">@lang('DBasic::common.ftime')</th>
    @if(isset($state_badge))
      <th class="text-center">@lang('DBasic::common.state')</th>
    @endif
    @if(!isset($type))
      <th class="text-end">@lang('DBasic::common.last_flt')</td>
    @endif
  </tr>
  @foreach($users as $user)
    <tr @if(empty($state_badge) && $user->state != 1) {!! DB_UserState($user, 'row') !!} @endif>
      <td>
        <a href="{{ route('frontend.users.show.public', [$user->id]) }}">{{ $user->name_private }}</a>
      </td>
      <td>
        {{ optional($user->rank)->name }}
      </td>
      @if(empty($airline_view))
        <td>
          <a href="{{ route('DBasic.airline', [$user->airline->icao ?? '']) }}">{{ optional($user->airline)->name }}</a>
        </td>
      @endif
      @if(!isset($type) || isset($type) && $type != 'hub')
        <td>
          <a href="{{ route('DBasic.hub', [$user->home_airport_id ?? '']) }}">{{ $user->home_airport->full_name ?? $user->home_airport_id }}</a>
        </td>
      @endif
      @if(!isset($type) || isset($type) && $type != 'visitor')
        <td>
          <a href="{{ route('frontend.airports.show', [$user->curr_airport_id ?? '']) }}">{{ $user->current_airport->full_name ?? $user->curr_airport_id }}</a>
        </td>
      @endif
      @if(!isset($type))
        <td class="text-center">
          @if($user->awards_count > 0)
            <i class="fas fa-trophy text-success" title="{{ $user->awards_count }}"></i>
          @endif
        </td>
      @endif
      <td class="text-center">
        @if($user->flights > 0) {{ number_format($user->flights) }} @endif
      </td>
      <td class="text-center">
        @if(Theme::getSetting('roster_combinetimes'))
          {{ DB_ConvertMinutes(($user->flight_time + $user->transfer_time), '%2dh %2dm') }}
        @else 
          {{ DB_ConvertMinutes($user->flight_time, '%2dh %2dm') }}
        @endif
      </td>
      @if(isset($state_badge))
        <td class="text-center">
          {!! DB_UserState($user) !!}
        </td>
      @endif
      @if(!isset($type))
        <td class="text-end">
          @if($user->last_pirep)
            {{ $user->last_pirep->submitted_at->diffForHumans() }}
          @endif
        </td>
      @endif
    </tr>
  @endforeach
</table>