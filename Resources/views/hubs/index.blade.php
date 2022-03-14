@extends('app')
@section('title', __('DBasic::common.hubs'))

@section('content')
  @if(!$hubs->count())
    <div class="alert alert-info mb-1 p-1 px-2 fw-bold">No Hubs!</div>
  @else
    <div class="row row-cols-md-2 row-cols-lg-3 row-cols-xxl-4">
      @foreach($hubs as $hub)
        <div class="col-md">
          <div class="card mb-2">
            <div class="card-header p-1">
              <h5 class="m-1">
                <a href="{{ route('DBasic.hub', [$hub->id]) }}">{{ $hub->name }}</a>
                <img class="img-h20 me-1 float-end" src="{{ public_asset('/image/flags_new/'.strtolower($hub->country).'.png') }}" alt="">
                {{-- <span class="float-end flag-icon flag-icon-{{ strtolower($hub->country) }}" style="font-size: 1.1rem;"></span> --}}
              </h5>
            </div>
            <div class="card-body p-0 table-responsive">
              <table class="table table-sm table-borderless table-striped text-start text-nowrap align-middle mb-0">
                <tr>
                  <th>@lang('DBasic::common.icao')</th>
                  <td class="text-end">{{ $hub->icao }}</td>
                </tr>
                <tr>
                  <th>@lang('DBasic::common.iata')</th>
                  <td class="text-end">{{ $hub->iata ?? '--' }}</td>
                </tr>
                <tr>
                  <th>@lang('common.country')</th>
                  <td class="text-end">
                    @if(strlen($hub->country) === 2)
                      {{ $country->alpha2($hub->country)['name'] }} ({{ strtoupper($hub->country) }})
                    @endif
                  </td>
                </tr>
                <tr>
                  <th>{{ trans_choice('common.pilot', 2) }}</th>
                  <td class="text-end">{{ $pilots[$hub->icao] ?? '-'}}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
