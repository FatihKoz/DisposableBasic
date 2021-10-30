<div class="card mb-2">
  <div class="card-header p-1">
    <h5 class="m-1">
      @lang('DBasic::common.specs')
      <i class="fas fa-cogs float-end"></i>
    </h5>
  </div>
  <div class="card-body p-0 table-responsive">
    <ul class="nav nav-tabs border-0" id="specsTab" role="tablist">
      @foreach($specs as $sp)
        <li class="nav-item m-0 p-0" role="presentation">
          <button 
            class="nav-link border-0 m-1 mx-1 p-0 px-1 @if($loop->first) active @endif" 
            id="{{ $sp->id.'-tab' }}" data-bs-toggle="tab" data-bs-target="#{{ 'spec'.$sp->id }}" 
            type="button" role="tab" aria-controls="{{ 'spec'.$sp->id }}" aria-selected="true">
            <b>{{ $sp->saircraft }}</b>
          </button>
        </li>
      @endforeach
    </ul>
    <div class="tab-content" id="specsTabContent">
      @foreach($specs as $sp)
        <div class="tab-pane fade @if($loop->first) show active @endif" id="{{ 'spec'.$sp->id }}" role="tabpanel" aria-labelledby="{{ $sp->id.'-tab' }}">
          @include('DBasic::specs.table', ['hide_title' => true])
        </div>
      @endforeach
    </div>
  </div>
</div>