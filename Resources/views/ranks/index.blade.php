@extends('app')
@section('title', __('DBasic::ranks.ranks'))

@section('content')
  {{-- Ranks Table --}}
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ config('app.name') }} | @lang('DBasic::ranks.ranks')
        <i class="fas fa-tags float-end m-1"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-sm table-borderless table-striped text-center align-middle mb-0">
        <tr>
          <th>&nbsp;</th>
          <th class="text-start">@lang('DBasic::ranks.rtitle')</th>
          <th>@lang('DBasic::ranks.minhour')</th>
          <th>@lang('DBasic::ranks.payacars')</th>
          <th>@lang('DBasic::ranks.paymanual')</th>
          <th>@lang('DBasic::ranks.image')</th>
          <th>@lang('DBasic::ranks.restrict')</th>
          <th>&nbsp;</th>
        </tr>
        @foreach($ranks as $rank)
          <tr>
            <td>&nbsp;</td>
            <td class="text-start">{{ $rank->name }}</td>
            <td>{{ $rank->hours }}</td>
            <td>{{ number_format($rank->acars_base_pay_rate) }} {{ $currency }}</td>
            <td>{{ number_format($rank->manual_base_pay_rate) }} {{ $currency }}</td>
            <td>
              @if($rank->image_url)
                <img src="{{ $rank->image_url }}" title="{{ $rank->name }}" class="rounded img-mh30 mx-1">
              @endif
            </td>
            <td>
              @if($rank->subfleets->count() > 0)
                @lang('DBasic::ranks.allowedsf') {{ $rank->subfleets->count() }}
              @else
                @lang('DBasic::ranks.allowedno')
              @endif
            </td>
            <td>
              @if($rank->subfleets->count() > 0)
                <i class="fas fa-angle-double-down mx-2" type="button" data-toggle="collapse" aria-expanded="false"
                    data-target="#sfr_{{ $rank->id }}" aria-controls="sfr_{{ $rank->id }}"
                    title="@lang('DBasic::ranks.showhide')">
                </i>
              @endif
            </td>
          </tr>
        @endforeach
      </table>
    </div>
  </div>

  {{-- Rank Boxes --}}
  <div class="row row-cols-3">
    @foreach($ranks as $rank)
      <div id="sfr_{{ $rank->id }}" class="collapse">
        @if($rank->subfleets->count() > 0)
          <div class="col">
            <div class="card mb-2">
              <div class="card-header p-1">
                <h5 class="m-1">
                  {{ $rank->name }}
                  <i class="fas fa-tag m-1 float-end"></i>
                </h5>
              </div>
              <div class="card-body p-1">
                @foreach($rank->subfleets as $subfleet)
                  <a href="{{ route('DBasic.subfleet', [$subfleet->type]) }}">
                    &bull; {{ $subfleet->name }} | {{ $subfleet->airline->name }}
                  </a>
                  <br>
                @endforeach
              </div>
              <div class="card-footer text-right p-1 small">
                @lang('DBasic::ranks.minhour'): {{ $rank->hours }}
              </div>
            </div>
          </div>
        @endif
      </div>
    @endforeach
  </div>
@endsection
