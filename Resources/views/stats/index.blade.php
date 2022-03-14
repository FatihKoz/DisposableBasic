@extends('app')
@section('title', __('DBasic::common.stats'))

@section('content')
  <div class="row row-cols-lg-3">
    {{-- LEFT --}}
    <div class="col-lg">
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::widgets.stats_gen')
            <i class="fas fa-sitemap float-end"></i>
          </h5>
        </div>
        <div class="card-body p-0 table-responsive">
          <table class="table table-sm table-borderless table-striped align-middle mb-0">
            @foreach ($stats_basic as $key => $value)
              <tr>
                <th class="text-start">{{ $key }}</th>
                <td class="text-end">{{ $value }}</td>
              </tr>
            @endforeach
          </table>
        </div>
      </div>
      {{-- Leaderboard Navigation --}}
      @if($stats_pirep)
        <ul class="nav nav-pills nav-fill nav-justify mb-2" id="leaderboard-pills" role="tablist">
          <li class="nav-item px-1 mb-1" role="presentation">
            <button class="nav-link active p-1" id="overall-tab" data-bs-toggle="pill" data-bs-target="#overall" type="button" role="tab" aria-controls="overall" aria-selected="true">@lang('DBasic::widgets.lb_overall')</button>
          </li>
          <li class="nav-item px-1" role="presentation">
            <button class="nav-link p-1" id="pl-month-tab" data-bs-toggle="pill" data-bs-target="#pl-month" type="button" role="tab" aria-controls="pl-month" aria-selected="false">@lang('DBasic::widgets.lb_month_p')</button>
          </li>
          <li class="nav-item px-1" role="presentation">
            <button class="nav-link p-1" id="pl-year-tab" data-bs-toggle="pill" data-bs-target="#pl-year" type="button" role="tab" aria-controls="pl-year" aria-selected="false">@lang('DBasic::widgets.lb_year_p')</button>
          </li>
          @if($multi_airline)
            <li class="nav-item px-1" role="presentation">
              <button class="nav-link p-1" id="al-month-tab" data-bs-toggle="pill" data-bs-target="#al-month" type="button" role="tab" aria-controls="al-month" aria-selected="false">@lang('DBasic::widgets.lb_month_a')</button>
            </li>
            <li class="nav-item px-1" role="presentation">
              <button class="nav-link p-1" id="al-year-tab" data-bs-toggle="pill" data-bs-target="#al-year" type="button" role="tab" aria-controls="al-year" aria-selected="false">@lang('DBasic::widgets.lb_year_a')</button>
            </li>
          @endif
        </ul>
      @endif
    </div>
    {{-- MIDDLE --}}
    <div class="col-lg">
      <div class="row row-cols-md-2 row-cols-lg-1">
        <div class="col-md">
          @widget('DBasic::LeaderBoard', ['source' => 'dep', 'count' => 3])
        </div>
        <div class="col-md">
          @widget('DBasic::LeaderBoard', ['source' => 'arr', 'count' => 3])
        </div>
      </div>
    </div>
    {{-- RIGHT --}}
    <div class="col-lg">
      @if($stats_pirep)
        <div class="card mb-2">
          <div class="card-header p-1">
            <h5 class="m-1">
              @lang('DBasic::widgets.stats_rep')
              <i class="fas fa-file-upload float-end"></i>
            </h5>
          </div>
          <div class="card-body p-0 table-responsive">
            <table class="table table-sm table-borderless table-striped align-middle mb-0">
              @foreach($stats_pirep as $key => $value)
                <tr>
                  <th class="text-start">{{ $key }}</th>
                  <td class="text-end">{{ $value }}</td>
                </tr>
              @endforeach
            </table>
          </div>
        </div>
      @endif
    </div>
  </div>
  {{-- LEADERBOARD RESULTS --}}
  @if($stats_pirep)
    <div class="tab-content" id="leaderboard-Content">
      <div class="tab-pane fade show active" id="overall" role="tabpanel" aria-labelledby="overall-tab">
        @include('DBasic::stats.lb_alltime')
      </div>
      <div class="tab-pane fade" id="pl-month" role="tabpanel" aria-labelledby="pl-month-tab">
        @include('DBasic::stats.lb_pilot_month')
      </div>
      <div class="tab-pane fade" id="pl-year" role="tabpanel" aria-labelledby="pl-year-tab">
        @include('DBasic::stats.lb_pilot_year')
      </div>
      @if ($multi_airline)
        <div class="tab-pane fade" id="al-month" role="tabpanel" aria-labelledby="al-month-tab">
          @include('DBasic::stats.lb_airline_month')
        </div>
        <div class="tab-pane fade" id="al-year" role="tabpanel" aria-labelledby="al-year-tab">
          @include('DBasic::stats.lb_airline_year')
        </div>
      @endif
    </div>
  @endif
@endsection
