@foreach($subfleets as $subfleet)
  <div class="row">
    <div class="col table-responsive">
      <table class="table table-sm table-striped text-start align-middle mb-0">
        <tr>
          <th>{{ $subfleet->name }}</th>
          <th class="text-end">
            <i class="fas fa-scroll m-1" title="Show/Hide Members" type="button" data-bs-toggle="collapse" data-bs-target="#sf_{{ $subfleet->id }}" aria-expanded="false" aria-controls="sf_{{ $subfleet->id }}"></i>
          </th>
        </tr>
        @include('DBasic::fleet.table', ['fleet' => $subfleet->aircraft, 'coll' => 'collapse', 'coll_id' => 'sf_'.$subfleet->id])
      </table>
    </div>
  </div>
@endforeach