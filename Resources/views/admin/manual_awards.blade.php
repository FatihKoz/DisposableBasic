<div style="margin-bottom: 5px;">
  {{ Form::open(['route' => 'DBasic.manual_award', 'method' => 'post']) }}
    <table class="table table-striped text-left" style="margin-bottom: 2px;">
      <tr>
        <td style="width: 30%; max-width: 30%;">User</td>
        <td class="text-right">
          <select class="form-control select2" style="width: 98%;" name="ma_user">
            <option value="ZZZ">Select user...</option>
            @foreach($users as $user)
              <option value="{{ $user->id }}">{{ $user->pilot_id.' - '.$user->name }}</option>
            @endforeach
          </select>
        </td>
      </tr>
      <tr>
        <td style="width: 30%; max-width: 30%;">Award</td>
        <td class="text-right">
          <select class="form-control select2" style="width: 98%" name="ma_award">
            <option value="ZZZ">Select award to assign...</option>
            @foreach($awards as $award)
              <option value="{{ $award->id }}">{{ $award->name }}</option>
            @endforeach
          </select>
        </td>
      </tr>
    </table>
    <input class="button" type="submit" value="Award User">
  {{ Form::close() }}
</div>