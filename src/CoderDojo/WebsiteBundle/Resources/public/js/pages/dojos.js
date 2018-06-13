(function () {
    "use strict";
    // class to wrap the map background in
    mapboxgl.accessToken = 'pk.eyJ1IjoiY29kZXJkb2pvbmwiLCJhIjoiY2o4N2QxaTc3MHp3ZTMybXAzMjRoMGM0ZyJ9.AxmqSifIHD8776sMIDpZcg';

    var DojosMapBackground = function(){};

    DojosMapBackground.prototype.initializeMap = function (dojos) {
        var mapOptions = {
            container: 'all-dojos-map',
            style: 'mapbox://styles/mapbox/streets-v10',
            minZoom: 5,
            maxZoom: 15,
            center: [5.291266, 52.132633],
            zoom: 2,
            pitchWithRotate: false
        };

        this.dojos = dojos;

        // setup the map
        this.map = new mapboxgl.Map(mapOptions);

        // if the window resizes, the map should know
        // $(window).resize(function () {
        //     google.maps.event.trigger(this.map, "resize");
        // }.bind(this));

        // focus the map in the middle of the Netherlands
        this.map.on('load', function(){
            this.map.flyTo({
                center: [5.291266, 52.132633],
                zoom: 7
            });


            // Place dojo markers
            this.placeMarkers();
        }.bind(this));
    };

    // place a single marker and save the reference to it
    DojosMapBackground.prototype.placeMarkerForDojo = function (dojo) {
        var el = document.createElement('div');
        el.className = 'marker';
        el.id = 'marker';

        var windowContent = '<h5>' + dojo.name + '</h5>' +
                            '<p>' + dojo.city + '</p>';

        if(dojo.nextUrl) {
            windowContent += '<p><strong>Volgende dojo</strong>: ' + dojo.nextDate + '</p>' +
                '<p><a href="'+dojo.nextUrl+'" class="btn-u btn-u-green btn-u-small">Registreren</a></p>';
        }

        var popup = new mapboxgl.Popup({ offset: 25 })
            .setHTML(windowContent);

        dojo.geo.marker = new mapboxgl.Marker(el)
            .setLngLat([dojo.geo.long, dojo.geo.lat])
            .setPopup(popup);

        dojo.geo.marker.addTo(this.map);
    };

    // place the markers
    DojosMapBackground.prototype.placeMarkers = function () {
        $.map(this.dojos, function (dojo) {
            this.placeMarkerForDojo(dojo);
        }.bind(this));
    };

    // start bouncing the dojo marker for the given id
    DojosMapBackground.prototype.activateMarkerForDojoId = function (dojoId) {
        var dojo = this.dojos[dojoId];
        dojo.geo.marker._element.classList.add('active');
    };

    // stop bouncing the dojo marker for the given id
    DojosMapBackground.prototype.deActivateMarkerFordojoId = function (dojoId) {
        var dojo = this.dojos[dojoId];
        dojo.geo.marker._element.classList.remove('active');
    };

    // pan and zoom to the location of the dojo
    DojosMapBackground.prototype.focusOnDojoWithId = function (dojoId) {
        // do calculations to center the dojo correctly
        var dojo   = this.dojos[dojoId];

        // pan to it
        this.map.flyTo({
            center: [dojo.geo.long, dojo.geo.lat],
            zoom: 13
        });

        if(this.lastPopup) {
            this.lastPopup.togglePopup();
        }

        dojo.geo.marker.togglePopup();

        this.lastPopup = dojo.geo.marker;
    };

    // reset the focus and show all dojos in the Netherlands
    DojosMapBackground.prototype.resetFocus = function () {
        this.map.flyTo({
            center: [5.291266, 52.132633],
            zoom: 7
        });
    };

    DojosMapBackground.prototype.closePopups = function () {
        $.map(this.dojos, function (dojo) {
            dojo.geo.marker.getPopup().open();
        }.bind(this));
    };

    // UI logic
    $(function () {
        // logic to control the map background
        var mapBackground = new DojosMapBackground();
        mapBackground.initializeMap(window.dojos);

        $('.dojo-row').mouseenter(function () {
            var dojoId = $(this).data('dojo-id');
            mapBackground.activateMarkerForDojoId(dojoId);
        });

        $('.dojo-row').mouseleave(function () {
            var dojoId = $(this).data('dojo-id');
            mapBackground.deActivateMarkerFordojoId(dojoId);
        });

        $('.dojo-row').click(function (e) {
            e.stopPropagation();
            var dojoId = $(this).data('dojo-id');
            //mapBackground.closePopups();
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

}());
