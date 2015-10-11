var map;
function initialize() {
  var mapCanvas = document.getElementById('all-dojos-map');
  var mapOptions = {
    center: new google.maps.LatLng(44.5403, -78.5463),
    zoom: 8,
    mapTypeId: google.maps.MapTypeId.ROADMAP
  }
  map = new google.maps.Map(mapCanvas, mapOptions)

  console.log('setting up map');
}
google.maps.event.addDomListener(window, 'load', initialize);


// on hover, fetch dojo details
$('.dojo-row').hover(function(e){
  dojoId = $(this).data('dojo-id');

  // grab dojo object
  dojo = _dojos[dojoId]

  console.log(dojo);

  // focus map on dojo address




});