(function () {
    "use strict";

    var Locator = window.Locator = {};

    Locator.defaultSearchSettings = {
        //css selectors search uses to attach its components too
        selectors : {
            map : '.loc-srch-res-map',
            list : '.loc-srch-res-list',
            teaser : '.loc-teaser',
            form : '.loc-srch-form',
            loader : '.loc-loader',
            trigger : '.loc-trigger',
            results : '.loc-srch-res'
        },
        // if 1 map will be fixed to top of viewport when page is scrolled
        stickyMap : 1
    };

    Locator.Form = Class.create({

        initialize: function (el, search) {

            this.settings = {
                selectors : {
                    loader : '.loc-loader'
                }
            };

            this.el = el;
            this.search = search;
            var self = this;


            Event.observe(el, 'submit', function (event) {
                if ("" !== el.serialize().toQueryParams().s) {
                    self.startLoader();
                    self.search.findLocations(el.serialize(), function () {
                        self.stopLoader();
                    });
                }
                Event.stop(event);
            });
        },

        startLoader: function () {
            this.el.select(this.settings.selectors.loader).each(function (el) {
                el.addClassName('is-loading');
            });
        },

        stopLoader: function () {
            this.el.select(this.settings.selectors.loader).each(function (el) {
                el.removeClassName('is-loading');
            });
        }
    });

    Locator.Map = Class.create({
        initialize: function (el) {
            this.el = el;
            this.defaults = {
                zoom: 15,
                scrollwheel : false,
                mapTypeId : google.maps.MapTypeId.ROADMAP,
                mapTypeControl : false,
                mapTypeControlOptions : {
                    style : google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                    position : google.maps.ControlPosition.BOTTOM_CENTER,
                    mapTypeIds : [google.maps.MapTypeId.ROADMAP, 'locator']
                },
                streetViewControl : false,
                streetViewControlOptions : {
                    position : google.maps.ControlPosition.LEFT_TOP
                }
            };
            this.map = new google.maps.Map(el, this.defaults);
            this.markers = [];
            this.infowindows = [];

            this.stoppers = [];

            this.settings = {
                maxZoom : 15
            };
        },

        // render locations on the map and trigger actions
        renderLocations: function (locations) {

            var latlngbounds = new google.maps.LatLngBounds(),
                show = 0,
                self = this;

            this.clearOverlays();

//            if (this.settings.theme) {
//                var styledMap = new google.maps.StyledMapType(this.settings.theme, { name: "Locator" });
//                self.map.mapTypes.set('locator', styledMap);
//                self.map.setMapTypeId('locator');
//            }

            for (var key in locations) {
                if (locations.hasOwnProperty(key)) {

                    var l = locations[key];
                    var loc = new google.maps.LatLng(l.latitude, l.longitude);

                    self.infowindows[l.id] = new google.maps.InfoWindow({
                        content: '<div id="content"><span class="loader loc-infowindow-loader is-loading">loading</span></div>'
                    });
                    self.markers[l.id] = new google.maps.Marker({
                        position: loc,
                        map: self.map,
                        title: l.title,
                        icon: self.getMarkerImage(l),
                        // shadow: shadow,
                        // shape: shape,
                        animation: google.maps.Animation.DROP
                    });


                    self.markers[l.id].id = l.id;

                    google.maps.event.addListener(self.markers[l.id], 'click', function() {
                        self.showInfoWindow(this.id);
                    });

                    latlngbounds.extend( loc );
                    show = 1
                }
            }

            self.map.fitBounds( latlngbounds );

            if(self.settings.maxZoom){
                //when the map loads, make sure it hasn't zoomed in to far, if it has zoom out
                //@todo, configure the zoom level in admin
                var listener = google.maps.event.addListener(self.map, "idle", function() {
                    if (self.map.getZoom() > self.settings.maxZoom) self.map.setZoom(self.settings.maxZoom);
                    google.maps.event.removeListener(listener);
                });
            }

            google.maps.event.trigger(self.map, 'resize');
            self.loadInfoWindows();
        },

        clearOverlays: function(){
            for (var key in this.markers) {
                if (this.markers.hasOwnProperty(key)) {
                    this.markers[key].setMap(null);
                }
            }
        },

        //hide all currently visible info windows
        hideInfoWindows: function(){
            for (var key in this.infowindows) {
                if (this.infowindows.hasOwnProperty(key)) {
                    this.infowindows[key].close();
                }
            }
        },

        //show an info window based on an id
        showInfoWindow: function(id){
            var self = this;

            self.hideInfoWindows();
            self.infowindows[id].open(self.map,self.markers[id]);
            if(!self.infowindows[id].isSet){
                new Ajax.Request('/locator/search/infowindow/id/'+id, {
                    method : 'get',
                    onFailure: function () {
                        alert('failed');
                    },
                    onSuccess: function (t) {
                        self.setInfoWindowContent(id, t.responseText);
                    }
                });
            }
        },

        //set the content of an info window based on an id
        setInfoWindowContent: function(id, content){
            this.infowindows[id].setContent(content);
            this.infowindows[id].isSet = 1;
        },

        //load content for all infowindows
        loadInfoWindows: function(){
            var self = this,
            ids = [];

            for (var key in self.infowindows) {
                if (self.infowindows.hasOwnProperty(key)) {

                    if(!self.infowindows[key].isSet && self.markers[key]){
                        ids.push(key);
                    }
                }
            }

            new Ajax.Request('/locator/search/infowindows/?ids='+ids.join(), {
                method : 'get',
                onFailure: function () {
                    alert('failed');
                },
                onSuccess: function (t) {
                    var windows = JSON.parse(t.responseText);
                    var temp = Array();

                    for (var key in windows) {
                        if (windows.hasOwnProperty(key) && windows[key] != 'undefined') {
                            self.setInfoWindowContent(key,windows[key]);
                        }
                    }
                }
            });
        },

        //bounce a marker to highlight it
        highlightMarker: function(id){
            var self = this;
            if(self.markers[id].getAnimation() === null){
                self.markers[id].setAnimation(google.maps.Animation.BOUNCE);
                self.stoppers[id] = setTimeout(function(){
                    self.markers[id].setAnimation(null);
                }, 720);
            }
        },

        getMarkerImage: function(l){
            return new google.maps.MarkerImage(
                '/skin/frontend/base/default/locator/images/pin.png',
                new google.maps.Size(40, 50),
                new google.maps.Point(0, 0),
                new google.maps.Point(20, 50)
            );
        }
    });

    Locator.List = Class.create({

        initialize: function (el) {
            this.el = el;
        },

        update: function (text) {
            this.el.update(text);
        }
    });

    Locator.Search = Class.create({

        initialize: function(options) {

            if(options){
                //if override settings are
                if(options.settings){
                    this.settings = $H(Locator.defaultSearchSettings).merge(options.settings);
                }

                if(options.map){
                    this.map = options.map;
                }
            }

            //if options were not passed to the search class, use locator default
            if(!this.settings){
                this.settings = Locator.defaultSearchSettings;
            }

            //if map has not already been set from options set it now
            if(!this.map && $$(this.settings.selectors.map).first()){
                this.map = new Locator.Map($$(this.settings.selectors.map).first());
            }

            if($$(this.settings.selectors.list).first()){
                this.list = new Locator.List($$(this.settings.selectors.list).first());
            }

            this.forms = [];

            var self = this;



            this.initScroll();

            $$(this.settings.selectors.form).each(function (el) {
                self.forms.push(new Locator.Form(el, self));
            });

            // Attach onclick events to search triggers
            $$(this.settings.selectors.trigger).invoke('observe', 'click', function(event){
                var href = Event.element(event).readAttribute('href');

                self.forms[0].startLoader();
                self.findLocations( href.toQueryParams(), function () {
                    self.forms[0].stopLoader();
                });
                Event.stop(event);
            });


            //Bind map rendering to StateChange Event
            window.History.Adapter.bind(window, 'statechange', function () {

                var State = History.getState();

                if (State.data.locations.length) {
                    self.list.update(State.data.output);
                    self.map.renderLocations(State.data.locations);
                    self.initEvents();
                }
            });
        },

        // Set initial history state when locations are not loaded from search, this will trigger map render
        initState: function(locations){
            //inject a random parameter to query string so state always changes on first load
            var href = window.location.href+'&rand='+Math.random();
            var locations = this.parseLocationsJson(locations);

            if(!locations.length){
                this.toggleNoResults(1);
            }

            //reset the hash for old browsers to stop history.js errors
            if(History.getHash()){
                window.location.hash = '';
            }

            History.replaceState(
                {
                    locations: locations,
                    output: this.list.el.innerHTML,
                    search: href.toQueryParams()
                },
                this.getSearchTitle(locations),
                window.location.search
            );

        },

        //make an ajax request to the server to find locations based on given query params
        findLocations: function (query, callback) {

            var self = this;
            var href;

            if(typeof (query) === 'object') {
                query = $H(query).toQueryString();
            }

            href = window.location.pathname + '?' + query;

            new Ajax.Request(window.location.pathname + '?' + query +'&ajax=1', {

                method : 'get',
                onFailure: function () {
                    alert('search failed');
                },
                onSuccess: function (t) {
                    var result = self.parseSearchJson(t.responseText);
                    result.search = href.toQueryParams();

                    self.toggleNoResults(0);
                    if(result.error === true) {
                        if (result.error_type === 'noresults') {
                            self.toggleNoResults(1)
                        } else {
                            alert(result.message);
                        }
                    } else if (result.locations.length) {
                        History.pushState(result, self.getSearchTitle(result.locations), '?' + query);
                    }else{
                        alert('an error occured');
                    }

                    if (typeof callback === "function") {
                        callback.call();
                    }
                }
            });
        },

        //parse result from server
        parseSearchJson: function (string) {
            var search = JSON.parse(string);
            if (search.locations) {
                search.locations = this.parseLocationsJson(search.locations);
            }
            return search;
        },

        parseLocationsJson: function (string) {

            var locations = JSON.parse(string);
            var temp = Array();

            for (var key in locations) {
                if (locations.hasOwnProperty(key) && locations[key] != 'undefined') {
                    temp.push(locations[key]);
                }
            }
            return temp;
        },

        //show or hide no results page based on boolean parameter
        toggleNoResults: function (empty) {
            var els = $$(this.settings.selectors.results);
            if(empty){
                els.each(function(el){
                    el.addClassName('is-no-results');
                });
            }else{
                els.each(function(el){
                    el.removeClassName('is-no-results');
                });
            }
        },

        //attach events to search ui
        initEvents: function(){
            var self = this;
            $$(this.settings.selectors.teaser).invoke('observe', 'click', function(event){
                var id = this.readAttribute('data-id');
                self.map.showInfoWindow(id);
            });

            $$(this.settings.selectors.teaser).invoke('observe', 'mouseover', function(event) {
                var id = this.readAttribute('data-id');
                self.map.highlightMarker(id);
            });
        },

        getSearchTitle:function (locations){
            return "Search: " + locations.length + " Locations";
        },

        initScroll: function(){

            if(this.settings.stickyMap){
                var map = $$('.loc-srch-res-map-wrap').first();
                var results = $$(this.settings.selectors.results).first();
                var self = this;

                Event.observe(document, "scroll", function() {
                    if (results.viewportOffset().top < 1){
                        map.addClassName('is-fixed');
                    }else{
                        map.removeClassName('is-fixed');
                    }
                });
            }

        }
    });

})();