@extends('app')
@section('title', __('DBasic::common.airlines'))

@section('content')
  <div class="row row-cols-md-2 row-cols-lg-4">
    @foreach($airlines as $airline)
      <div class="col-md">
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              <a href="{{ route('DBasic.airline', [$airline->icao]) }}">{{ $airline->name }}</a>
              <img class="float-end img-h20 me-1" src="{{ public_asset('/image/flags_new/'.strtolower($airline->country).'.png') }}" alt="">
              {{-- <span class="float-end flag-icon flag-icon-{{ strtolower($airline->country) }}" style="font-size: 1.1rem;"></span> --}}
            </h5>
          </div>
          <div class="card-body table-responsive p-0">
            <table class="table table-sm table-borderless table-striped text-start text-nowrap align-middle mb-0">
              <tr>
                <th>@lang('DBasic::common.icao')</th>
                <td class="text-end">{{ $airline->icao }}</td>
              </tr>
              <tr>
                <th>@lang('DBasic::common.iata')</th>
                <td class="text-end">{{ $airline->iata ?? '--' }}</td>
              </tr>
              <tr>
                <th>@lang('common.country')</th>
                <td class="text-end">
                  @if(strlen($airline->country) === 2)
                    {{ $country->alpha2($airline->country)['name'] }} ({{ strtoupper($airline->country) }})
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>
      </div>
    @endforeach
  </div>
@endsection
