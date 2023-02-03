@if($settings)
  <div style="margin-bottom: 5px;">
    {{ Form::open(['route' => 'DBasic.settings_update', 'method' => 'post']) }}
      <table class="table table-striped text-left" style="margin-bottom: 2px;">
        @foreach($settings->where('group', $group)->sortBy('order') as $st)
          <tr>
            <td style="width:30%; max-width: 30%;">{{ $st->name }}</td>
            <td>
              @if($st->field_type === 'check')
                <input type="hidden" name="{{ $st->id }}" value="false">
                <input class="form-control" type="checkbox" name="{{ $st->id }}" value="true" @if($st->value === 'true') checked @endif>
              @elseif($st->field_type === 'select')
                @php $values = explode(',', $st->options); @endphp
                <select class="form-control" name="{{ $st->id }}">
                  @foreach($values as $value)
                    <option value="{{ $value }}" @if($st->value === $value || !filled($st->value) && $st->default === $value) selected @endif>{{ $value }}</option>
                  @endforeach
                </select>
              @else
                <input
                  class="form-control"
                  @if($st->field_type === 'decimal')
                    type="number" step="0.0001" min="0" max="9999"
                  @elseif($st->field_type === 'numeric')
                    type="number" step="1" @if($st->key === 'dbasic.ar_marginlrate') min="-9999" max="0" @else min="0" max="9999" @endif
                  @else
                    type="text" maxlength="500"
                  @endif
                  name="{{ $st->id }}" placeholder="{{ $st->default }}" value="{{ $st->value }}">
              @endif
            </td>
          </tr>
        @endforeach
      </table>
      <input type="hidden" name="group" value="{{ $group }}">
      <input class="button" type="submit" value="Save Section Settings">
    {{ Form::close() }}
  </div>

  {{-- Custom placeholder coloring --}}
  <style>
    ::placeholder { color: indianred !important; opacity: 0.6 !important; }
    :-ms-input-placeholder { color: indianred !important; }
    ::-ms-input-placeholder { color: indianred !important; }
  </style>
@endif