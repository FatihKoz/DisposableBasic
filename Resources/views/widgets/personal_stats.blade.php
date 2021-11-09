@if($config['disp'] === 'full')
  <div class="card mb-2 text-center">
    <div class="card-body p-2">
      @if(is_numeric($pstat) && ($config['type'] === 'avgtime' || $config['type'] === 'tottime'))
        {{ DB_ConvertMinutes($pstat, '%2dh %2dm') }}
      @else
        {{ $pstat }}
      @endif
    </div>
    <div class="card-footer p-0 small fw-bold">
      {{ $sname }} {{ $speriod }}
    </div>
  </div>
@else
  @if(is_numeric($pstat) && ($config['type'] === 'avgtime' || $config['type'] === 'tottime'))
    {{ DB_ConvertMinutes($pstat, '%2dh %2dm') }}
  @else
    {{ $pstat }}
  @endif
@endif