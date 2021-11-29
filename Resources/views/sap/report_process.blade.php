@php
  if ($stable) {
    $requirements = is_array($stable->requirementResultsGroups) ? collect($stable->requirementResultsGroups) : null;
    $touchdowns = is_array(optional($stable->analysis)->touchdowns) ? collect($stable->analysis->touchdowns) : null;
  } else {
    $requirements = null;
    $touchdowns = null;
  }

  $analysis = isset($stable) ? $stable->analysis : null;

  $airport = isset($analysis) ? $analysis->apt : null;
  $approach = isset($analysis) ? $analysis->app : null;
  $rollout = isset($analysis) ? $analysis->rollout : null;
  $runway = isset($analysis) ? $analysis->rwy : null;
  $touchdown_combined = isset($analysis) ? $analysis->touchdown_combined : null;

  $is_stable = (isset($requirements) && $requirements->where('type', '2')->count()) ? false : true;
@endphp