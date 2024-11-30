@if($is_visible)
  <div class="card mb-2">
    <div class="card-header p-1">
      <h5 class="m-1">
        @lang('DBasic::common.stats') {{ $header_note }}
        <i class="fas {{ $icon }} float-end"></i>
      </h5>
    </div>
    <div class="card-body p-0 table-responsive">
      <table class="table table-sm table-borderless table-striped text-start text-nowrap align-middle mb-0">
        @if(isset($stats['airlines_desc']))
          <tr>
            <th class="text-start">{{ $stats['airlines_desc'] }}</th>
            <td class="text-end">{{ $stats['airlines_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['pilots_desc']))
          <tr>
            <th class="text-start">{{ $stats['pilots_desc'] }}</th>
            <td class="text-end">{{ $stats['pilots_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['subfleets_desc']))
          <tr>
            <th class="text-start">{{ $stats['subfleets_desc'] }}</th>
            <td class="text-end">{{ $stats['subfleets_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['aircraft_desc']))
          <tr>
            <th class="text-start">{{ $stats['aircraft_desc'] }}</th>
            <td class="text-end">{{ $stats['aircraft_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['flights_desc']))
          <tr>
            <th class="text-start">{{ $stats['flights_desc'] }}</th>
            <td class="text-end">{{ $stats['flights_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['airports_desc']))
          <tr>
            <th class="text-start">{{ $stats['airports_desc'] }}</th>
            <td class="text-end">{{ $stats['airports_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['hubs_desc']))
          <tr>
            <th class="text-start">{{ $stats['hubs_desc'] }}</th>
            <td class="text-end">{{ $stats['hubs_value'] }}</td>
          </tr>
        @endif
        @if(isset($stats['pireps_desc']))
          <tr>
            <th class="text-start">{{ $stats['pireps_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['pireps_value']) }}</td>
          </tr>
        @endif
        @if(isset($stats['time_desc']))        
          <tr>
            <th class="text-start">{{ $stats['time_desc'] }}</th>
            <td class="text-end">{{ DB_ConvertMinutes($stats['time_value'], '%02d h %02d m') }}</td>
          </tr>
        @endif
        @if(isset($stats['dist_desc']))
          <tr>
            <th class="text-start">{{ $stats['dist_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['dist_value']->local(2)).' '.$units['distance'] }}</td>
          </tr>
        @endif
        @if(isset($stats['fuel_desc']))
          <tr>
            <th class="text-start">{{ $stats['fuel_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['fuel_value']->local(2)).' '.$units['fuel'] }}</td>
          </tr>
        @endif
        @if(isset($stats['pax_desc']))
          <tr>
            <th class="text-start">{{ $stats['pax_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['pax_value']) }}</td>
          </tr>
        @endif
        @if(isset($stats['cgo_desc']))
          <tr>
            <th class="text-start">{{ $stats['cgo_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['cgo_value']->local(2)).' '.$units['weight'] }}</td>
          </tr>
        @endif
        @if(isset($stats['time_avg_desc']))
          <tr>
            <th class="text-start">{{ $stats['time_avg_desc'] }}</th>
            <td class="text-end">{{ DB_ConvertMinutes($stats['time_avg_value'], '%02d h %02d m') }}</td>
          </tr>
        @endif
        @if(isset($stats['dist_avg_desc']))
          <tr>
            <th class="text-start">{{ $stats['dist_avg_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['dist_avg_value']->local(2)).' '.$units['distance'] }}</td>
          </tr>
        @endif
        @if(isset($stats['fuel_avg_desc']))
          <tr>
            <th class="text-start">{{ $stats['fuel_avg_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['fuel_avg_value']->local(2)).' '.$units['fuel'] }}</td>
          </tr>
        @endif
        @if(isset($stats['fuel_perhour_desc']))
          <tr>
            <th class="text-start">{{ $stats['fuel_perhour_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['fuel_perhour_value']->local(2)).' '.$units['fuel'] }}</td>
          </tr>
        @endif
        @if(isset($stats['pax_avg_desc']))
          <tr>
            <th class="text-start">{{ $stats['pax_avg_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['pax_avg_value']) }}</td>
          </tr>
        @endif
        @if(isset($stats['cgo_avg_desc']))
          <tr>
            <th class="text-start">{{ $stats['cgo_avg_desc'] }}</th>
            <td class="text-end">{{ number_format($stats['cgo_avg_value']->local(2)).' '.$units['weight'] }}</td>
          </tr>
        @endif
      </table>
    </div>
    @if($footer_note)
      <div class="card-footer p-0 px-1 small text-end fw-bold">
        {{ $footer_note }}
      </div>
    @endif
  </div>
@endif