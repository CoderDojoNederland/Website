(function () {
  "use strict";
    // class to wrap the map background in
  var DojosMapBackground = function (mapCanvas, dojos) {
    this.map        = null;
    this.infoWindow = null;
    this.mapCanvas  = mapCanvas;
    this.dojos      = dojos;

    google.maps.event.addDomListener(window, 'load', this.initializeMap.bind(this));
  };

  DojosMapBackground.prototype.initializeMap = function () {
    var mapOptions = {
      mapTypeId:          google.maps.MapTypeId.ROADMAP,
      scrollwheel:        false,
      streetViewControl:  false,
      mapTypeControl:     false,
      styles: [{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"visibility":"on"},{"color":"#e67e22"}]},{"featureType":"water","elementType":"all","stylers":[{"visibility":"on"},{"color":"#428bca"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#FFFFFF"},{"weight":4}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"on"},{"color":"#FFFFFF"},{"weight":1}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"geometry","stylers":[{"visibility":"simplified"},{"color":"#fcd3a1"}]},{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#000000"}]},{"featureType":"road","elementType":"all","stylers":[{"color":"#e67e22"}]},{"featureType":"administrative.locality","elementType":"all","stylers":[{"visibility":"off"}]}]
    };

    // setup the map
    this.map = new google.maps.Map(this.mapCanvas, mapOptions);

    // if the window resizes, the map should know
    $(window).resize(function () {
      google.maps.event.trigger(this.map, "resize");
    }.bind(this));

    // focus the map in the middle of the Netherlands
    this.resetFocus();

    // place the markers needed
    this.placeMarkers();
  };

  // place a single marker and save the reference to it
  DojosMapBackground.prototype.placeMarkerForDojo = function (dojo) {
    var marker = new google.maps.Marker({
      position: {lat: dojo.geo.lat, lng: dojo.geo.long},
      map:      this.map,
      title:    dojo.name,
      icon:     '/bundles/coderdojowebsite/img/markers/white.png'
    });

    // show info window on click
    marker.addListener('click', function () {
      this.showInfoWindowForDojoId(dojo.id);
    }.bind(this));

    dojo.geo.marker = marker;
  };

  // place the markers
  DojosMapBackground.prototype.placeMarkers = function () {
    $.map(this.dojos, function (dojo) {
      this.placeMarkerForDojo(dojo);
    }.bind(this));
  };

  // calculate an offsetted center
  DojosMapBackground.prototype.calculateOffsettedCenter = function (latlng, offsetx, offsety) {
    var scale = Math.pow(2, this.map.getZoom()),
      worldCoordinateCenter = this.map.getProjection().fromLatLngToPoint(latlng),
      pixelOffset = new google.maps.Point((offsetx / scale) || 0, (offsety / scale) || 0),

      worldCoordinateNewCenter = new google.maps.Point(
        worldCoordinateCenter.x - pixelOffset.x,
        worldCoordinateCenter.y + pixelOffset.y
      ),
      newCenter = this.map.getProjection().fromPointToLatLng(worldCoordinateNewCenter);

    return newCenter;
  };

  // center the map on the given dojo
  DojosMapBackground.prototype.centerDojoWithDojoId = function (dojoId) {
    var dojo   = this.dojos[dojoId],
      location = new google.maps.LatLng(dojo.geo.lat, dojo.geo.long),
      center   = this.calculateOffsettedCenter(location, 200, 0);

    this.map.panTo(center);
  };

  // start bouncing the dojo marker for the given id
  DojosMapBackground.prototype.startBouncingMarkerForDojoId = function (dojoId) {
    var dojo = this.dojos[dojoId];
    dojo.geo.marker.setAnimation(google.maps.Animation.BOUNCE);
  };

  // stop bouncing the dojo marker for the given id
  DojosMapBackground.prototype.stopBouncingMarkerFordojoId = function (dojoId) {
    var dojo = this.dojos[dojoId];
    dojo.geo.marker.setAnimation(null);
  };

  // pan and zoom to the location of the dojo
  DojosMapBackground.prototype.focusOnDojoWithId = function (dojoId) {
    // zoom in on the map
    this.map.setZoom(14);

    // do calculations to center the dojo correctly
    var dojo   = this.dojos[dojoId],
      location = new google.maps.LatLng(dojo.geo.lat, dojo.geo.long),
      center   = this.calculateOffsettedCenter(location, 200, 0);

    // pan to it
    this.map.panTo(center);

    // stop the animation
    dojo.geo.marker.setAnimation(null);

    // show the info window for this dojo
    this.showInfoWindowForDojoId(dojoId);
  };

  DojosMapBackground.prototype.showInfoWindowForDojoId = function (dojoId) {
    var dojo = this.dojos[dojoId],
      windowContent = "<strong>" + dojo.name + "</strong><br>" +
        dojo.location + "<br>" +
        dojo.street + " " + dojo.housenumber + "<br>" +
        dojo.postalcode + " " + dojo.city + "<br>";

    // close and nullify info window if already existing
    if (this.infoWindow) {
      this.infoWindow.close();
      this.infoWindow = null;
    }

    // create and show infoWindow
    this.infoWindow = new google.maps.InfoWindow({
      content: windowContent
    });

    this.infoWindow.open(this.map, dojo.geo.marker);
  };

  // reset the focus and show all dojos in the Netherlands
  DojosMapBackground.prototype.resetFocus = function () {
    this.map.setCenter(new google.maps.LatLng(52.132633, 5.291266));
    this.map.setZoom(8);
  };

  // UI logic
  $(function () {

    // logic to control the map background
    var mapBackground = new DojosMapBackground(
      $('#all-dojos-map')[0],
      window.dojos
    );

    $('.dojo-row').mouseenter(function () {
      var dojoId = $(this).data('dojo-id');
      if (mapBackground.infoWindow) {
        mapBackground.infoWindow.close();
        mapBackground.infoWindow = null;
      }

      if (mapBackground.map.getZoom() !== 8) {
        mapBackground.resetFocus();
      }

      mapBackground.startBouncingMarkerForDojoId(dojoId);
      mapBackground.centerDojoWithDojoId(dojoId);
    });

    $('.dojo-row').mouseleave(function () {
      var dojoId = $(this).data('dojo-id');
      mapBackground.stopBouncingMarkerFordojoId(dojoId);
    });

    $('.dojo-row').click(function (e) {
      e.stopPropagation();
      var dojoId = $(this).data('dojo-id');
      mapBackground.focusOnDojoWithId(dojoId);
    });

    // logic to control the tabs in the list
    $('[data-tab-ref]').click(function (e) {
      e.preventDefault();

      switch ($(this).data('tab-ref')) {
      case 'upcoming-dojos':
        $('[data-tab-ref=upcoming-dojos]').addClass('active');
        $('[data-tab-ref=all-dojos]').removeClass('active');
        $('[data-js-ref=list-upcoming-dojos]').removeClass('hidden');
        $('[data-js-ref=list-all-dojos]').addClass('hidden');
        mixpanel.track('View Upcoming Dojos');
        break;

      case 'all-dojos':
        $('[data-tab-ref=all-dojos]').addClass('active');
        $('[data-tab-ref=upcoming-dojos]').removeClass('active');
        $('[data-js-ref=list-upcoming-dojos]').addClass('hidden');
        $('[data-js-ref=list-all-dojos]').removeClass('hidden');
        mapBackground.resetFocus();
        mixpanel.track('View All Dojos');
        break;
      }
    });

  });
  
  $('#dojoeventlist a').on('click', function(e){
    var cb = generate_callback($(this));
    e.preventDefault();
    var el = $(e.target);

    mixpanel.track("Register for dojo", {
      "Dojo Name": el.data('dojo'),
      "Dojo Event Date": el.data('dojo-date'),
      "Registration Location": 'dojo page'
    });

    setTimeout(cb, 500);
  });

  function generate_callback(a) {
    return function() {
      window.location = a.attr("href");
    }
  }

  mixpanel.track('View All Dojos');
}());
