@if($is_visible === true)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        {{ $icao }} NOTAMS
        <i class="fas fa-tasks float-end"></i>
      </h5>
    </div>
    <div class="card-body text-start overflow-auto table-responsive p-1">
      @if($notams)
        @foreach($notams as $notam)
          {!! $notam['text'] !!}
          @if(!$loop->last)
            <hr class="m-0 my-1 p-0">
          @endif
        @endforeach
      @else
        No NOTAM(s) found
      @endif
    </div>
    <div class="card-footer small text-end p-0 pe-1">
      @if(count($notams) > 0)
        <span class="float-start px-1">{{ count($notams).' NOTAM(s) fetched' }}</span>
      @endif
      <b>Unofficial NOTAM feed for simulation use ONLY</b>
    </div>
  </div>
@endif
