@php 
  $style = isset($style) ? $style : null;
  $zoom = isset($zoom) ? $zoom : 5;
  $marker = isset($marker) ? $marker : false;
  $height = isset($height) ? $height : '800px';
@endphp
<iframe 
  id="windyframe" height="{{ $height }}" width="100%" style="{{ $style }} display:block"
  src="https://embed.windy.com/embed2.html?lat={{ $lat }}&lon={{ $lon }}&detailLat={{ $lat }}&detailLon={{ $lon }}&zoom={{ $zoom }}&marker={{ $marker }}&level=surface&overlay=thunder&product=ecmwf&calendar=now&message=true&pressure=true&type=map&location=coordinates&metricWind=kt&metricTemp=%C2%B0C&radarRange=-1"
  frameborder="0"
  >
</iframe>