@extends('app')
@section('title', 'Stable Approach Reports')

@section('content')
  @if(!$reports->count())
    <div class="alert alert-info mb-1 p-1 px-2 fw-bold">No Stable Approach Reports!</div>
  @else
    <div class="row row-cols-4">
      @foreach($reports as $report)
        <div class="col">
          @include('DBasic::sap.report', ['stable' => json_decode($report->raw_report)])
        </div>
      @endforeach
    </div>
    {{ $reports->links('pagination.default') }}
  @endif
@endsection
