@if($visible === true)
  @if($config['disp'] === 'full')
    <div class="card mb-2 text-center">
      <div class="card-body p-2">
        {{ $pstat }}
      </div>
      <div class="card-footer p-0 small fw-bold">
        {{ $sname.' '.$speriod }}
      </div>
    </div>
  @else
    {{ $pstat }}
  @endif
@endif