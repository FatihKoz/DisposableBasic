<div class="card mx-1 mb-2">
    <div class="row">
      <div class="col table-responsive">
        @foreach($subfleets as $subfleet)
          <table class="table table-sm border-dark align-middle text-start text-nowrap mb-0">
            <tr>
              <th>
                <a href="{{ route('DBasic.subfleet', [$subfleet->type ?? '']) }}">{{ $subfleet->name }}</a>
              </th>
              <td class="text-end">
                {{ $subfleet->type }} | @lang('DBasic::common.aircraft'): {{ $subfleet->aircraft->count() }}
                <i class="fas fa-scroll" title="@lang('DBasic::common.show_hide')" type="button" data-bs-toggle="collapse" data-bs-target="#sf_{{ $subfleet->id }}" aria-expanded="false" aria-controls="sf_{{ $subfleet->id }}"></i>
              </td>
            </tr>
          </table>
          <div class="collapse table-responsive" id="sf_{{ $subfleet->id }}">
            @include('DBasic::fleet.table', ['aircraft' => $subfleet->aircraft, 'compact_view' => true])
          </div>
        @endforeach
      </div>
    </div>
</div>