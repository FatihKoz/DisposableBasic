@extends('app', ['plain' => true, 'disable_nav' => true])
@section('title', __('DBasic::common.stats'))

@section('content')
  <div class="row">
    <div class="col mt-1">

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
@endsection
