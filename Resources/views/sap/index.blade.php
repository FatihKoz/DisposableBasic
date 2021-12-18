@extends('app')
@section('title', 'Stable Approach Reports')

@section('content')
  @if(!$sap_reports->count())
    <div class="alert alert-info mb-1 p-1 px-2 fw-bold">No Stable Approach Reports!</div>
  @else
    <div class="row row-cols-4">
      @foreach($sap_reports as $sap)
        <div class="col">
          @include('DBasic::sap.report')
        </div>
      @endforeach
    </div>
    {{ $sap_reports->links('pagination.default') }}
  @endif
@endsection
