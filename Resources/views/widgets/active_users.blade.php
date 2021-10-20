@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::widgets.activeu')
        <i class="fas fa-users float-end m-1"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-borderless table-sm table-striped mb-0 text-start align-middle">
        @foreach($active_users as $active)
          <tr>
            <td>
              <a href="{{ route('frontend.profile.show', [$active->user_id]) }}">{{ $active->user->name_private ?? '' }}</a>
            </td>
            <td class="text-end">{{ $active->last_activity->diffForHumans() }}</td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>
@endif
