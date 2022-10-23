@if(filled($leader_board))
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ $header_title }}
        <i class="fas {{ $header_icon }} float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-sm table-striped table-borderless text-start text-nowrap align-middle mb-0">
        @if($count > 1)
          <tr>
            <th>@lang('DBasic::common.name')</th>
            <th class="text-end">{{ $column_title }}</th>
          </tr>
        @endif
        @foreach($leader_board as $board)
          <tr>
            <td>
              <a href="{{ route($board['route'], $board['id']) }}">
                @if(Theme::getSetting('roster_ident'))
                  {{ $board['pilot_ident'] }}
                @endif
                {{ $board['name_private'] }}
              </a>
            </td>
            <td class="text-end">{{ $board['totals'] }}</td>
          </tr>
        @endforeach
      </table>
    </div>
    <div class="card-footer p-0 px-1 text-end small fw-bold">
      {{ $footer_note.' '.$footer_type }}
    </div>
  </div>
@endif