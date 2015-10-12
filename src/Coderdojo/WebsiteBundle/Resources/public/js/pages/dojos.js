(function(){

  var map;
  function initialize() {
    var mapCanvas = document.getElementById('all-dojos-map');
    var mapOptions = {
      center: new google.maps.LatLng(52.132633, 5.291266),
      zoom: 8,
      mapTypeId: google.maps.MapTypeId.ROADMAP,
      scrollwheel: false,
      streetViewControl: false,
      mapTypeControl: false
    }
    map = new google.maps.Map(mapCanvas, mapOptions)

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

  $(function(){
    // grab reference to the tabs
    var allDojosTab           = $('[data-js-ref=all-dojos]')
        upcomingDojosTab      = $('[data-js-ref=upcoming-dojos]'),
        upcomingDojosContent  = $('[data-js-ref=list-upcoming-dojos]'),
        allDojosContent       = $('[data-js-ref=list-all-dojos]');

    // when all tab is clicked, switch to all content
    allDojosTab.on('click', function(e){
      e.preventDefault();

      // toggle active state of tab
      allDojosTab.addClass('active');
      upcomingDojosTab.removeClass('active');

      // toggle visibility of content
      allDojosContent.removeClass('hidden');
      upcomingDojosContent.addClass('hidden');
     
    });

    upcomingDojosTab.on('click', function(e){
      e.preventDefault();

      allDojosTab.removeClass('active');
      upcomingDojosTab.addClass('active')

      allDojosContent.addClass('hidden');
      upcomingDojosContent.removeClass('hidden');
    });


  });

})();