@if(isset($flights) && $flights > 0 || !isset($flights) && count($mapAirports) > 0 || !isset($flights) && count($mapHubs) > 0 || isset($sceneries))
  {{-- Map Modal Button --}}
  <div class="row">
    <div class="col d-grid">
      <button type="button" class="btn btn-sm btn-danger p-0 px-1" data-toggle="modal" data-target="{{ '#modal'.$mapsource }}" onclick="{{ $mapsource }}ExpandMap()">
        @if($mapsource === 'user')
          @lang('DBasic::widgets.personal_map')
        @elseif($mapsource === 'fleet')
          @lang('DBasic::widgets.fleet_map')
        @elseif($mapsource === 'airline')
          @lang('DBasic::widgets.airline_map')
        @elseif($mapsource === 'assignment')
          @lang('DBasic::widgets.assignm_map')
        @elseif($mapsource === 'aerodromes')
          @lang('DBasic::widgets.aerodr_map')
        @elseif($mapsource === 'scenery')
          My Sceneries Map
        @else
          @lang('DBasic::widgets.flights_map')
        @endif
      </button>
    </div>
  </div>

  {{-- Map Modal --}}
  <div class="modal" id="modal{{ $mapsource }}" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="{{ $mapsource.'Title' }}" aria-hidden="true">
    <div class="modal-dialog mx-auto" style="max-width: 80%;">
      <div class="modal-content shadow-none p-0">
        <div class="modal-header border-0 p-1">
          <h5 class="card-title m-0" id="{{ $mapsource.'Title' }}">
            @if ($mapsource === 'user')
              @lang('DBasic::widgets.personal_map')
            @elseif ($mapsource === 'fleet')
              @lang('DBasic::widgets.fleet_map')
            @elseif($mapsource === 'airline')
              @lang('DBasic::widgets.airline_map')
            @elseif($mapsource === 'assignment')
              @lang('DBasic::widgets.assignm_map')
            @elseif($mapsource === 'aerodromes')
              @lang('DBasic::widgets.aerodr_map')
            @elseif($mapsource === 'scenery')
              My Sceneries Map
            @else
              @lang('DBasic::widgets.flights_map')
            @endif
          </h5>
          <span class="close">
            <i class="fas fa-times-circle" data-dismiss="modal" aria-label="Close" aria-hidden="true"></i>
          </span>
        </div>
        <div class="modal-body border-0 p-0">
          <div id="{{ $mapsource }}" style="width: 100%; height: 80vh;"></div>
        </div>
        <div class="modal-footer border-0 p-0 small text-end">
          <span>
            @if(isset($sceneries))
              Sceneries: {{ $sceneries }}
            @endif
            @if(count($mapCityPairs) > 0)
              @lang('DBasic::widgets.citypairs'): {{ count($mapCityPairs) }} |
            @endif
            @if(count($mapHubs) > 0)
              @lang('DBasic::widgets.hubs'): {{ count($mapHubs) }} |
            @endif
            @if(count($mapAirports) > 0)
              @lang('DBasic::widgets.airports'): {{ count($mapAirports) }}
            @endif
            @if(isset($flights))
              | {{ trans_choice('common.flight', 2) }}: {{ $flights }}
            @endif
            @if(isset($aircraft))
              | @lang('common.aircraft'): {{ $aircraft }}
            @endif
          </span>
        </div>
      </div>
    </div>
  </div>

  @section('scripts')
    @parent
    {{-- Map Leaflet Script --}}
    <script type="text/javascript">
      function {{ $mapsource }}ExpandMap() {
        // Icons
        var BlueIcon = new L.Icon({!! $mapIcons['BlueIcon'] !!});
        var GoldIcon = new L.Icon({!! $mapIcons['GoldIcon'] !!});
        var GreenIcon = new L.Icon({!! $mapIcons['GreenIcon'] !!});
        var GreyIcon = new L.Icon({!! $mapIcons['GreyIcon'] !!});
        var OrangeIcon = new L.Icon({!! $mapIcons['OrangeIcon'] !!});
        var RedIcon = new L.Icon({!! $mapIcons['RedIcon'] !!});
        var VioletIcon = new L.Icon({!! $mapIcons['VioletIcon'] !!});
        var YellowIcon = new L.Icon({!! $mapIcons['YellowIcon'] !!});
        // Map Boundary
        var mBoundary = L.featureGroup();
        // Hubs
        @if(count($mapHubs) > 0)
          var mHubs = L.layerGroup();
          @foreach ($mapHubs as $hub)
            var HUB_{{ $hub['id'] }} = L.marker([{{ $hub['loc'] }}], {icon: GreenIcon , opacity: 0.8}).bindPopup({!! "'".$hub['pop']."'" !!}).addTo(mHubs).addTo(mBoundary);
          @endforeach
        @endif
        // Airports
        @if(count($mapAirports) > 0)
          var mAirports = L.layerGroup();
          @foreach ($mapAirports as $airport)
            var APT_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: BlueIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mAirports).addTo(mBoundary);
          @endforeach
        @endif
        // FS9 Sceneries
        @if(count($mapFS9) > 0)
          var mFS9 = L.layerGroup();
          @foreach ($mapFS9 as $airport)
            var FS9_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: RedIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mFS9).addTo(mBoundary);
          @endforeach
        @endif
        // FSX Sceneries
        @if(count($mapFSX) > 0)
          var mFSX = L.layerGroup();
          @foreach ($mapFSX as $airport)
            var FSX_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: BlueIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mFSX).addTo(mBoundary);
          @endforeach
        @endif
        // P3D Sceneries
        @if(count($mapP3D) > 0)
          var mP3D = L.layerGroup();
          @foreach ($mapP3D as $airport)
            var P3D_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: GoldIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mP3D).addTo(mBoundary);
          @endforeach
        @endif
        // XP Sceneries
        @if(count($mapXP) > 0)
          var mXP = L.layerGroup();
          @foreach ($mapXP as $airport)
            var XP_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: VioletIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mXP).addTo(mBoundary);
          @endforeach
        @endif
        // MSFS Sceneries
        @if(count($mapMSFS) > 0)
          var mMSFS = L.layerGroup();
          @foreach ($mapMSFS as $airport)
            var MSFS_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: OrangeIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mMSFS).addTo(mBoundary);
          @endforeach
        @endif
        // OTHER Sceneries
        @if(count($mapOTHER) > 0)
          var mOTHER = L.layerGroup();
          @foreach ($mapOTHER as $airport)
            var OTHER_{{ $airport['id'] }} = L.marker([{{ $airport['loc'] }}], {icon: GreyIcon , opacity: 0.8}).bindPopup({!! "'".$airport['pop']."'" !!}).addTo(mOTHER).addTo(mBoundary);
          @endforeach
        @endif
        // City Pairs / Flights Layer Group
        @if(count($mapCityPairs) > 0)
          var mFlights = L.layerGroup();
          @foreach ($mapCityPairs as $citypair)
            @if($citypair['pop'])
              var FLT_{{ $citypair['name'] }} = L.geodesic([{{ $citypair['geod'] }}], {weight: 2, opacity: 0.8, steps: 5, color: '{{$citypair['geoc']}}'}).bindPopup({!! "'".$citypair['pop']."'" !!}).addTo(mFlights);
            @else
              var FLT_{{ $citypair['name'] }} = L.geodesic([{{ $citypair['geod'] }}], {weight: 2, opacity: 0.8, steps: 5, color: '{{$citypair['geoc']}}'}).addTo(mFlights);
            @endif
          @endforeach
        @endif
        // Define Base Layers For Control Box
        var DarkMatter = L.tileLayer.provider('CartoDB.DarkMatter');
        var NatGeo = L.tileLayer.provider('Esri.NatGeoWorldMap');
        var OpenSM = L.tileLayer.provider('OpenStreetMap.Mapnik');
        var WorldTopo = L.tileLayer.provider('Esri.WorldTopoMap');
        // Define Additional Overlay Layers
        var OpenAIP = L.
          tileLayer('http://{s}.tile.maps.openaip.net/geowebcache/service/tms/1.0.0/openaip_basemap@EPSG%3A900913@png/{z}/{x}/{y}.{ext}', {
          attribution: '<a href="https://www.openaip.net/">openAIP Data</a> (<a href="https://creativecommons.org/licenses/by-sa/3.0/">CC-BY-NC-SA</a>)',
          ext: 'png',
          minZoom: 4,
          maxZoom: 14,
          tms: true,
          detectRetina: true,
          subdomains: '12'
        });
        // Define Control Groups
        var BaseLayers = {'Dark Matter': DarkMatter, 'OpenSM Mapnik': OpenSM, 'NatGEO World': NatGeo, 'World Topo': WorldTopo};
        var Overlays = {!! $mapOverlays !!};
        // Define Map and Add Control Box
        var {{ $mapsource }} = L.map('{{ $mapsource }}', {center: [{{ $mapcenter }}], layers: [DarkMatter, {!! $mapLayers !!}], scrollWheelZoom: false}).fitBounds(mBoundary.getBounds().pad(0.2));;
        L.control.layers(BaseLayers, Overlays).addTo({{ $mapsource }});
        // TimeOut to ReDraw The Map in Modal
        setTimeout(function(){ {{ $mapsource }}.invalidateSize().fitBounds(mBoundary.getBounds().pad(0.2))}, 300);
      }
    </script>
  @endsection
@endif
