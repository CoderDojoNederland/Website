(function(){

  // class to wrap the map background in
  var DojosMapBackground = function(mapCanvas, dojos){
    this.map       = null;
    this.mapCanvas = mapCanvas;
    this.dojos     = dojos;

    google.maps.event.addDomListener(window, 'load', this.initializeMap.bind(this));
  };

  DojosMapBackground.prototype.initializeMap = function(){
    var mapOptions = {
      mapTypeId:          google.maps.MapTypeId.ROADMAP,
      scrollwheel:        false,
      streetViewControl:  false,
      mapTypeControl:     false
    };

    // setup the map
    this.map = new google.maps.Map(this.mapCanvas, mapOptions);

    // focus the map in the middle of the Netherlands
    this.resetFocus();

    // place the markers needed
    this.placeMarkers();
  };

  // place a single marker and save the reference to it
  DojosMapBackground.prototype.placeMarkerForDojo = function(dojo){
    var marker = new google.maps.Marker({
      position: {lat: dojo.geo.lat, lng: dojo.geo.long},
      map:      this.map,
      title:    dojo.name
    });

    dojo.geo.marker = marker;
  };

  // place the markers
  DojosMapBackground.prototype.placeMarkers = function(){
    $.each(this.dojos, function(_, dojo){
      this.placeMarkerForDojo(dojo);
    }.bind(this));
  };

  // start bouncing the dojo marker for the given id
  DojosMapBackground.prototype.startBouncingMarkerForDojoId = function(dojoId){
    dojo = this.dojos[dojoId];
    dojo.geo.marker.setAnimation(google.maps.Animation.BOUNCE);
  };

  // stop bouncing the dojo marker for the given id
  DojosMapBackground.prototype.stopBouncingMarkerFordojoId = function(dojoId){
    dojo = this.dojos[dojoId];
    dojo.geo.marker.setAnimation(null);
  };

  // pan and zoom to the location of the dojo
  // TODO: Make sure it is slightly off-center since the UI overlays here..
  DojosMapBackground.prototype.focusOnDojoWithId = function(dojoId){
    dojo = this.dojos[dojoId];
    this.map.panTo({lat: dojo.geo.lat, lng: dojo.geo.long});
    this.map.setZoom(15);
    dojo.geo.marker.setAnimation(null);
  };

  // reset the focus and show all dojos in the Netherlands
  DojosMapBackground.prototype.resetFocus = function(){
    this.map.setCenter(new google.maps.LatLng(52.132633, 5.291266));
    this.map.setZoom(8);
  };

  // UI logic
  $(function(){

    // logic to control the map background
    var mapBackground = new DojosMapBackground(
      $('#all-dojos-map')[0],
      _dojos
    );

    $('.dojo-row').hover(function(){
      dojoId = $(this).data('dojo-id');
      mapBackground.startBouncingMarkerForDojoId(dojoId);
    });

    $('.dojo-row').mouseout(function(){
      dojoId = $(this).data('dojo-id');
      mapBackground.stopBouncingMarkerFordojoId(dojoId);
    });

    $('.dojo-row').click(function(e){
      e.preventDefault();
      dojoId = $(this).data('dojo-id');
      mapBackground.focusOnDojoWithId(dojoId);
    });

    // logic to control the tabs in the list
    $('[data-tab-ref]').click(function(){
      switch($(this).data('tab-ref')){
        case 'upcoming-dojos':
          $('[data-tab-ref=upcoming-dojos]').addClass('active');
          $('[data-tab-ref=all-dojos]').removeClass('active');
          $('[data-js-ref=list-upcoming-dojos]').removeClass('hidden');
          $('[data-js-ref=list-all-dojos]').addClass('hidden');
        break;

        case 'all-dojos':
          $('[data-tab-ref=all-dojos]').addClass('active');
          $('[data-tab-ref=upcoming-dojos]').removeClass('active');
          $('[data-js-ref=list-upcoming-dojos]').addClass('hidden');
          $('[data-js-ref=list-all-dojos]').removeClass('hidden');
          mapBackground.resetFocus();
        break;
      }
    });

  });

})();
