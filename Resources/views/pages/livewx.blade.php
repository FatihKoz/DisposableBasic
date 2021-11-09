@extends('app')
@section('title', 'Live WX Map')
@php
  // This map uses your phpvms settings to center on a given location,
  // if you want it to show a different location provide your lat and lon values here
  // Just un-comment below two lines and edit values.
  // $lat = 35.18;
  // $lon = 32.08;
@endphp
@section('content')
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        Live Weather
        <i class="fas fa-cloud-sun-rain float-end"></i>
      </h5>
    </div>
    <div class="card-body text-center p-0">
      @include('DBasic::pages.livewx_map', ['lat' => $lat, 'lon' => $lon, 'style' => 'border-bottom-left-radius: 5px; border-bottom-right-radius: 5px;'])
    </div>
  </div>
@endsection
