<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      Welcome {{ $curr_rank->name }}
      <i class="fas fa-star float-end"></i>
    </h5>
  </div>
  <div class="card-body text-start p-1">
    @if($notice)
      You have been manually promoted, progress not available!
    @elseif($last)
      Congratulations, you reached the last rank available. Progress not available...
    @else
      <div class="progress">
        <div class="progress-bar progress-bar-striped bg-success" role="progressbar" style="width: {{ $ratio }}%" aria-valuenow="{{ $ratio }}" aria-valuemin="0" aria-valuemax="100">{{ $ratio.'%'}}</div>
      </div>
    @endif
  </div>
  <div class="card-footer small text-end p-0 pe-1">
    @if($notice || $last)
      &nbsp;
    @else
      You need <b>{{ $missing }}</b> flight hours for <b>{{ $next_rank->name }}</b> promotion.
    @endif
  </div>
</div>