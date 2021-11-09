@extends('app')
@section('title', __('DBasic::common.fleet'))

@section('content')
  <div class="row">
    <div class="col">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @isset($subfleet) {{ $subfleet->airline->name }} | {{ $subfleet->name }} @else {{ config('app.name') }} @endisset @lang('DBasic::common.fleet')
            <i class="fas fa-plane float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 overflow-auto table-responsive" style="max-height: 77vh;">
          @include('DBasic::fleet.table')
        </div>
        <div class="card-footer p-1 small">
          <div class="row row-cols-3">
            <div class="col text-start">
              @if(isset($subfleet) && $subfleet->fares_count > 0)
                <b>@lang('DBasic::common.config'):</b>
                @foreach($subfleet->fares as $fare)
                  @if(!$loop->first) &bull; @endif
                  {{ $fare->name }}
                  {{ number_format($fare->pivot->capacity) }}
                  @if($fare->type === 1) {{ setting('units.weight') }} @else Pax @endif
                @endforeach
              @endif
            </div>
            <div class="col text-center">
              @if(isset($subfleet) && $subfleet->flights_count > 0)
                <b>{{ trans_choice('common.flight',2) }}:</b> {{ $subfleet->flights_count }}
              @endif
            </div>
            <div class="col text-end">
              <b>@lang('DBasic::common.paginate', ['first' => $aircraft->firstItem(), 'last' => $aircraft->lastItem(), 'total' => $aircraft->total()])</b>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{ $aircraft->links('pagination.auto') }}
@endsection
