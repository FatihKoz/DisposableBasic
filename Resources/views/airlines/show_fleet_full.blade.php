
  <div class="row">
    <div class="col table-responsive">
      <table class="table table-sm table-striped text-start align-middle mb-0">
        @foreach($airline->aircraft as $ac)
        <tr>
          <th>{{ $ac->ident }}</th>
          <th class="text-end">
            {{ $ac->subfleet->name }}
          </th>
        </tr>
        @endforeach
      </table>
    </div>
  </div>
