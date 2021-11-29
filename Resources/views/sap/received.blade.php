@extends('app')
@section('title', 'Stable Approach Report')

@section('content')
  @if($status != 'OK')
    <div class="alert alert-danger mb-1 p-1 px-2 fw-bold">{{ $status }}</div>
  @else
    <div class="alert alert-success mb-1 p-1 px-2 fw-bold">Report successfully received and saved</div>
    <div class="row row-cols-4 mt-2">
      <div class="col">
        @include('DBasic::sap.report', ['stable' => json_decode($report->raw_report)])
      </div>
    </div>
  @endif
@endsection
