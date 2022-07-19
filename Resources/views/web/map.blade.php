@extends('app', ['plain' => true, 'disable_nav' => true])
@section('title', config('app.name'))

@section('content')
  <div class="row" style="height: 2vh;">
    <div class="col">
      <span class="float-end small fw-bold" id="utc_clock"></span>
    </div>
  </div>
  <div class="row">
    <div class="col">
      <div class="card">
        <div id="LeafletMap" style="width: 100%; height: 94vh;"></div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
  <script type="text/javascript">
    // Define Icons
    var GreenIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-green.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [12, 20],
      popupAnchor: [1, -34],
      shadowSize: [20, 20]
    });
    var RedIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-red.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [12, 20],
      popupAnchor: [1, -34],
      shadowSize: [20, 20]
    });
    var VioletIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-violet.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [12, 20],
      popupAnchor: [1, -34],
      shadowSize: [20, 20]
    });
    var BlueIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-blue.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [12, 20],
      popupAnchor: [1, -34],
      shadowSize: [20, 20]
    });
    var YellowIcon = new L.Icon({
      iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-yellow.png',
      shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
      iconSize: [12, 20],
      popupAnchor: [1, -34],
      shadowSize: [20, 20]
    });

    // Define Map Base Layers
    var DarkMatter = L.tileLayer.provider('CartoDB.DarkMatter');
    var OpenSM = L.tileLayer.provider('OpenStreetMap.Mapnik');
    var WorldTopo = L.tileLayer.provider('Esri.WorldTopoMap');
    var NatGeo = L.tileLayer.provider('Esri.NatGeoWorldMap');

    // Define Groups
    var mBoundary = L.featureGroup();
    var mHubs = L.layerGroup();
    var mDestinations = L.layerGroup();
    var mA321 = L.layerGroup();
    var mA330 = L.layerGroup();
    var mRoutes = L.layerGroup();
    
    // Define Overlays
    var Overlays = {"Destinations": mDestinations, "Hubs": mHubs, "Routes": mRoutes, "A330": mA330, "A321": mA321};
    
    // Define Control Groups
    var BaseLayers = {"Dark Matter": DarkMatter, "OpenSM Mapnik": OpenSM, "World Topo": WorldTopo, "NatGeo World": NatGeo};
    
    // Airport Coordinates
    var cAYT = [36.9043, 30.8019];
    var cSVO = [55.9736, 37.4125];
    var cLED = [59.8029, 30.2678];
    var cSVX = [56.7448, 60.8029];
    var cPEE = [57.9166, 56.0295];
    var cCEK = [55.3032, 61.5030];
    var cOVB = [55.0114, 82.6522];
    var cKJA = [56.1729, 92.4933];
    var cKUF = [53.5132, 50.1572];
    var cKZN = [55.6083, 49.2804];
    var cUFA = [54.5558, 55.8779];
    var cTLV = [32.0055, 34.8854];

    // Fixed Point Coordinates
    var cGASBI = [41.41080075570664, 50.48606266627703];
    var cKZPASS1 = [45.73970418738745, 54.7047230067963];
    var cKZPASS2 = [51.38902479010291, 79.49295126732865];
    var cRUENT1 = [51.47596816060141, 52.996185439519145];
    var cRUENT2 = [50.85535460800352, 56.89062313033866];
    var cBULGARIA = [42.96718566510318, 23.31669401561882];
    var cSLOVAKIA = [48.80441881541685, 21.25200905542288];
    var cPOLAND = [53.794528221115726, 22.332032297860245];
    var cLITHUANIA = [54.59967446550916, 25.15763125735905];

    // Hubs
    var dAYT = L.marker(cAYT, {icon: VioletIcon , opacity: 0.8}).bindPopup('AYT / LTAI').addTo(mHubs).addTo(mBoundary);

    // Destinations
    var dSVO = L.marker(cSVO, {icon: RedIcon , opacity: 0.8}).bindPopup('SVO / UUEE').addTo(mDestinations).addTo(mBoundary);
    var dLED = L.marker(cLED, {icon: RedIcon , opacity: 0.8}).bindPopup('LED / ULLI').addTo(mDestinations).addTo(mBoundary);
    var dSVX = L.marker(cSVX, {icon: RedIcon , opacity: 0.8}).bindPopup('SVX / USSS').addTo(mDestinations).addTo(mBoundary);
    var dPEE = L.marker(cPEE, {icon: RedIcon , opacity: 0.8}).bindPopup('PEE / USPP').addTo(mDestinations).addTo(mBoundary);
    var dCEK = L.marker(cCEK, {icon: RedIcon , opacity: 0.8}).bindPopup('CEK / USCC').addTo(mDestinations).addTo(mBoundary);
    var dOVB = L.marker(cOVB, {icon: RedIcon , opacity: 0.8}).bindPopup('OVB / UNNT').addTo(mDestinations).addTo(mBoundary);
    var dKJA = L.marker(cKJA, {icon: RedIcon , opacity: 0.8}).bindPopup('KJA / UNKL').addTo(mDestinations).addTo(mBoundary);
    var dKUF = L.marker(cKUF, {icon: RedIcon , opacity: 0.8}).bindPopup('KUF / UWWW').addTo(mDestinations).addTo(mBoundary);
    var dKZN = L.marker(cKZN, {icon: RedIcon , opacity: 0.8}).bindPopup('KZN / UWKD').addTo(mDestinations).addTo(mBoundary);
    var dUFA = L.marker(cUFA, {icon: RedIcon , opacity: 0.8}).bindPopup('UFA / UWUU').addTo(mDestinations).addTo(mBoundary);
    var dTLV = L.marker(cTLV, {icon: RedIcon , opacity: 0.8}).bindPopup('TLV / LLBG').addTo(mDestinations).addTo(mBoundary);
    
    // Routes
    var AYTSVO = L.polyline([cAYT, cBULGARIA, cSLOVAKIA, cPOLAND, cLITHUANIA, cSVO], {weight: 1, opacity: 0.8, color: 'red'}).bindPopup('AYT-SVO').addTo(mRoutes).addTo(mA330);
    var AYTLED = L.polyline([cAYT, cBULGARIA, cSLOVAKIA, cPOLAND, cLITHUANIA, cLED], {weight: 1, opacity: 0.8, color: 'red'}).bindPopup('AYT-LED').addTo(mRoutes).addTo(mA330);

    var AYTKUF = L.polyline([cAYT, cGASBI, cRUENT1, cKUF], {weight: 1, opacity: 0.8, color: 'blue'}).bindPopup('AYT-SVX').addTo(mRoutes).addTo(mA321);
    var AYTKZN = L.polyline([cAYT, cGASBI, cRUENT1, cKZN], {weight: 1, opacity: 0.8, color: 'blue'}).bindPopup('AYT-KZN').addTo(mRoutes).addTo(mA321);
    var AYTUFA = L.polyline([cAYT, cGASBI, cRUENT1, cUFA], {weight: 1, opacity: 0.8, color: 'blue'}).bindPopup('AYT-UFA').addTo(mRoutes).addTo(mA321);

    var AYTSVX = L.polyline([cAYT, cGASBI, cRUENT2, cSVX], {weight: 1, opacity: 0.8, color: 'blue'}).bindPopup('AYT-SVX').addTo(mRoutes).addTo(mA321);
    var AYTPEE = L.polyline([cAYT, cGASBI, cRUENT2, cPEE], {weight: 1, opacity: 0.8, color: 'blue'}).bindPopup('AYT-PEE').addTo(mRoutes).addTo(mA321);
    var AYTCEK = L.polyline([cAYT, cGASBI, cRUENT2, cCEK], {weight: 1, opacity: 0.8, color: 'blue'}).bindPopup('AYT-CEK').addTo(mRoutes).addTo(mA321);

    var AYTOVB = L.polyline([cAYT, cGASBI, cKZPASS1, cKZPASS2, cOVB], {weight: 1, opacity: 0.8, color: 'red'}).bindPopup('AYT-OVB').addTo(mRoutes).addTo(mA330);
    var AYTKJA = L.polyline([cAYT, cGASBI, cKZPASS1, cKZPASS2, cKJA], {weight: 1, opacity: 0.8, color: 'red'}).bindPopup('AYT-KJA').addTo(mRoutes).addTo(mA330);

    var AYTTLV = L.geodesic([cAYT, cTLV], {weight: 1, opacity: 0.8, steps: 5, color: 'crimson'}).bindPopup('AYT-TLV').addTo(mRoutes).addTo(mA330).addTo(mA321);

    // Define Map and Add Control Box
    var map = L.map('LeafletMap', {center: cAYT, layers: [WorldTopo, mDestinations, mHubs]}).fitBounds(mBoundary.getBounds().pad(0.2));
    L.control.layers(BaseLayers, Overlays).addTo(map);
</script>
@endsection