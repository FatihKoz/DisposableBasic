<div style="margin-bottom: 5px;">
  {{ Form::open(['route' => 'DBasic.manual_payment', 'method' => 'post']) }}
    <table class="table table-striped text-left" style="margin-bottom: 2px;">
      <tr>
        <td style="width: 30%; max-width: 30%;">User</td>
        <td class="text-right">
          <select class="form-control select2" style="width: 98%;" name="mp_user">
            <option value="ZZZ">Select user...</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->pilot_id.' - '.$user->name }}</option>
            @endforeach
          </select>
        </td>
      </tr>
      <tr>
        <td style="width: 30%; max-width: 30%;">Amount ({{ setting('units.currency') }})</td>
        <td class="text-right input-group-sm">
          <input type="number" class="form-control" name="mp_amount" step="1" min="1" max="100000" placeholder="0"/>
        </td>
      </tr>
    </table>
    <input class="button" type="submit" value="Transfer Money to User">
  {{ Form::close() }}
</div>