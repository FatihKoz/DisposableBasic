<div class="row row-cols-md-2 row-cols-lg-4">
  <div class="col-md">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'count' => 3])
    @if ($multi_airline)
      @widget('DBasic::LeaderBoard', ['source' => 'airline', 'count' => 3])
    @endif
  </div>
  <div class="col-md">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'count' => 3, 'type' => 'time'])
    @if ($multi_airline)
      @widget('DBasic::LeaderBoard', ['source' => 'airline', 'count' => 3, 'type' => 'time'])
    @endif
  </div>
  <div class="col-md">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'count' => 3, 'type' => 'distance'])
    @if ($multi_airline)
      @widget('DBasic::LeaderBoard', ['source' => 'airline', 'count' => 3, 'type' => 'distance'])
    @endif
  </div>
  <div class="col-md">
    @widget('DBasic::LeaderBoard', ['source' => 'pilot', 'count' => 3, 'type' => 'lrate'])
    @if ($multi_airline)
      @widget('DBasic::LeaderBoard', ['source' => 'airline', 'count' => 3, 'type' => 'lrate'])
    @endif
  </div>
</div>
