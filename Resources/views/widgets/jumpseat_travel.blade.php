@if($is_visible)
  {{ Form::open(array('route' => $form_route, 'method' => 'post')) }}
    @if(empty($fixed_dest) && filled($js_airports))
      <div class="card mb-2">
        <div class="card-header p-1">
          <h5 class="m-1">
            @lang('DBasic::widgets.js_travel')
            <i class="fas fa-ticket-alt float-end"></i>
          </h5>
        </div>
        <div class="card-body p-1">
          <select name="newloc" id="destination" style="width: 100%" class="form-group input-group select2 my-1">
            <option value="">@lang('DBasic::widgets.js_selectap')</option>
            @foreach($js_airports as $destination)
              <option value="{{ $destination->id }}">{{ $destination->id.' : '.$destination->name.' ('.$destination->location.')' }}</option>
            @endforeach
          </select>
        </div>
        <div class="card-footer p-1 text-end">
          <i class="fas fa-money-bill-wave text-{{ $icon_color }} float-start m-1" title="{{ $icon_title }}"></i>
          @if($price === 'auto')
            <button class="btn btn-sm bg-info p-0 px-1" type="submit" name="interim_price" value="1">@lang('DBasic::widgets.js_check')</button>
          @endif
          <button class="btn btn-sm bg-success p-0 px-1" type="submit">@lang('DBasic::widgets.js_button')</button>
        </div>
      </div>
    @elseif($fixed_dest && $is_possible)
      <button class="btn btn-sm btn-success mx-1" type="submit" title="{{ $icon_title }}">@lang('DBasic::widgets.js_buttonf')</button>
      <input type="hidden" name="newloc" value="{{ $fixed_dest }}">
    @endif
    <input type="hidden" name="price" value="{{ $price }}">
    <input type="hidden" name="basep" value="{{ $base_price }}">
    <input type="hidden" name="croute" value="{{ url()->current() }}">
  {{ Form::close() }}
@endif
