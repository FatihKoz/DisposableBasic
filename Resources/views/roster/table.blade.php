<table class="table table-sm table-borderless table-striped text-start mb-0 align-middle">
  <tr>
    <th>@lang('common.name')</th>
    <th>@lang('DBasic::ranks.rtitle')</th>
    <th>@lang('DBasic::common.airline')</th>
    <th class="text-center">@lang('DBasic::common.base')</th>
    <th class="text-center">@lang('DBasic::common.location')</th>
    <th class="text-center">@lang('DBasic::common.ftime')</th>
  </tr>
  @foreach($users as $user)
    <tr>
      <td><a href="{{ route('frontend.users.show.public', [$user->id]) }}">{{ $user->name_private }}</a></td>
      <td>{{ $user->rank->name }}</td>
      <td><a href="{{ route('DBasic.airline', [$user->airline->icao]) }}">{{ $user->airline->name }}</a></td>
      <td class="text-center">{{ $user->home_airport_id }}</td>
      <td class="text-center">{{ $user->curr_airport_id }}</td>
      <td class="text-center">@minutestotime($user->flight_time)</td>
    </tr>
  @endforeach
</table>