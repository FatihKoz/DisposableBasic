@extends('admin.app')
@section('title', 'Disposable Airlines')

@section('content')
  <div class="card border-blue-bottom" style="margin-left:5px; margin-right:5px; margin-bottom:5px;">
    <div class="content">
      <p>This module is designed to provide some new pages/views for your phpVms v7; Airlines, Airline Details, Fleet, SubFleet, Aircraft Details and All Pireps.</p>
      <p>If you have DisposableTools Module installed, views will detect it and may use some of its widgets automatically</p>
      <p><b>Details about the module can be found in the README.md file</b></p>
      <p>&bull; <a href="https://github.com/FatihKoz/DisposableAirlines#readme" target="_blank">Online Readme</a></p>
      <hr>
      <p>Module Developed by <a href="https://github.com/FatihKoz" target="_blank">B.Fatih KOZ</a> &copy; 2021</p>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <div class="col-sm-12">
        <h5 style="margin:5px; padding:5px;"><b>Admin Functions</b></h5>
    </div>
  </div>

  <div class="row text-center" style="margin-left:5px; margin-right:5px;">
    <div class="col-sm-12">
      <div class="col-sm-4">
        <div class="card border-blue-bottom" style="padding:10px;">
          <b>Fix Aircraft State</b>
          <br><br>
          <form action="/admin/disposableairlines" id="fixacstate">
            <div class="row text-center">
              <div class="col-sm-12">
                <label for="parkac">Enter Aircraft Registration</label>
                <input class="form-control" type="text" id="parkac" name="parkac" placeholder="TC-DSP" maxlength="6">
              </div>
            </div>
            <input type="submit" value="Park Aircraft">
          </form>
          <br>
          <span class="text-danger"><b>If the aircraft has an active (in-progress) PIREP, it gets CANCELLED too !!!</b></span>
        </div>
        
        <div class="card border-blue-bottom" style="padding:10px;">
          <b>Enable / Disable Aircraft State Control</b>
          <br><br>
          <form action="/admin/disposableairlines" id="ac_state">
            <input type="hidden" name="action" value="acstate">
            <div class="row text-left">
              <div class="col-sm-8">
                <label for="sc">Control AC State</label>
                <input type="hidden" name="sc" value="false">
                <input type="checkbox" id="sc" name="sc" value="true" @if(Dispo_Settings('dairlines.acstate_control')) checked @endif>
              </div>
            </div>
            <input type="submit" value="Save Setting">
          </form>
          <br>
          <span class="text-danger"><b>When enabled Disposable Airlines module will CHANGE aircraft STATE's according to pirep events !</b></span>
        </div>
        
      </div>
      <div class="col-sm-8">
        <div class="card border-blue-bottom" style="padding:10px;">
          <b>Configure Discord Webhook (for Pirep Filed Messages)</b>
          <br><br>
          <form action="/admin/disposableairlines" id="discordmessages">
            <input type="hidden" name="action" value="pirepmsg">
            <div class="row text-left">
              <div class="col-sm-8">
                <label for="mainsettings">Enable Messages</label>
                <input type="hidden" name="mainsetting" value="false">
                <input type="checkbox" id="mainsetting" name="mainsetting" value="true" @if(Dispo_Settings('dairlines.discord_pirepmsg')) checked @endif>
              </div>
            </div>
            <div class="row text-left">
              <div class="col-sm-12">
                <label for="webhookurl">Webhook URL (copy&paste from your Discord Server settings)</label>
                <input class="form-control" type="text" id="webhookurl" name="webhookurl" value="{{ Dispo_Settings('dairlines.discord_pirep_webhook') }}" maxlength="500">
              </div>
            </div>
            <div class="row text-left">
              <div class="col-sm-4">
                <label for="webhookname">Message Poster's Name</label>
                <input class="form-control" type="text" id="webhookname" name="webhookname" value="{{ Dispo_Settings('dairlines.discord_pirep_msgposter') }}" maxlength="50">
              </div>
            </div>
            <input type="submit" value="Save Settings">
          </form>
          <br>
          <span class="text-info">Create your webhook before enabling it here, also check laravel logs if the messages do not appear in your Discord</span>
        </div>
    </div>
  </div>
@endsection
