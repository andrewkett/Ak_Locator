/*jshint browser:true, devel:true, prototypejs:true */
(function () {
    "use strict";

    /**
     * @name Locator
     * @namespace
     */
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
        stickyMap : 0,
        baseUrl : '/'
    };

    /**
     * @class Form
     */
    Locator.Form = Class.create({

        /**
         * @constructor
         * @param {HTMLElement} el
         * @param {Locator.Search} search
         */
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

        /**
         * Make loader visible to user
         */
        startLoader: function () {
            this.el.select(this.settings.selectors.loader).each(function (el) {
                el.addClassName('is-loading');
            });
        },

        /**
         * Hide loader from user
         */
        stopLoader: function () {
            this.el.select(this.settings.selectors.loader).each(function (el) {
                el.removeClassName('is-loading');
            });
        }
    });

    /**
     * @class Map
     */
    Locator.Map = Class.create({

        /**
         * @constructor
         * @param {HTMLElement} el
         */
        initialize: function (el) {
            var theme = this.getTheme();

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
                maxZoom : 15,
                baseUrl : Locator.defaultSearchSettings.baseUrl
            };



            if (theme) {
                var styledMap = new google.maps.StyledMapType(theme, { name: "Locator" });
                this.map.mapTypes.set('locator', styledMap);
                this.map.setMapTypeId('locator');
            }
        },

        /**
         * Render locations on the map and trigger actions
         *
         * @param locations
         */
        renderLocations: function (locations, coords) {

            var latlngbounds = new google.maps.LatLngBounds(),
                show = 0,
                self = this;

            this.clearOverlays();

            // If point coords have been passed render the point marker
            if (coords) {
                var lat = coords[1], long = coords[0];

                var loc = new google.maps.LatLng(lat, long);

                self.markers.point = new google.maps.Marker({
                    position: loc,
                    map: self.map
                });

                latlngbounds.extend( loc );
            }

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
                    show = 1;
                }
            }

            self.map.fitBounds( latlngbounds );
            self.checkMaxZoom();

            google.maps.event.trigger(self.map, 'resize');
            self.loadInfoWindows();
        },

        /**
         * Check that the map is not zoomed in to far
         */
        checkMaxZoom: function(){
            var self = this;
            if (self.settings.maxZoom) {
                //when the map loads, make sure it hasn't zoomed in to far, if it has zoom out
                //@todo, configure the zoom level in admin
                var listener = google.maps.event.addListener(self.map, "idle", function() {
                    if (self.map.getZoom() > self.settings.maxZoom) self.map.setZoom(self.settings.maxZoom);
                    google.maps.event.removeListener(listener);
                });
            }
        },

        /**
         * Clear current markers from map
         */
        clearOverlays: function(){
            for (var key in this.markers) {
                if (this.markers.hasOwnProperty(key)) {
                    this.markers[key].setMap(null);
                }
            }
        },

        /**
         * Hide all currently visible info windows
         */
        hideInfoWindows: function(){
            for (var key in this.infowindows) {
                if (this.infowindows.hasOwnProperty(key)) {
                    this.infowindows[key].close();
                }
            }
        },

        /**
         * Show an info window based on an id
         *
         * @param {int} id
         */
        showInfoWindow: function(id){
            var self = this;

            self.hideInfoWindows();
            self.infowindows[id].open(self.map,self.markers[id]);

            if (!self.infowindows[id].isSet) {
                new Ajax.Request(this.settings.baseUrl+'locator/search/infowindow/id/'+id, {
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

        /**
         * Set the content of an info window for a given location
         *
         * @param {int} id
         * @param {string} content
         */
        setInfoWindowContent: function(id, content){
            this.infowindows[id].setContent(content);
            this.infowindows[id].isSet = 1;
        },

        /**
         * Load content for all infowindows
         */
        loadInfoWindows: function(){
            var self = this,
            ids = [];

            for (var key in self.infowindows) {
                if (self.infowindows.hasOwnProperty(key)) {
                    if (!self.infowindows[key].isSet && self.markers[key]) {
                        ids.push(key);
                    }
                }
            }

            new Ajax.Request(this.settings.baseUrl+'locator/search/infowindows/?ids='+ids.join(), {
                method : 'get',
                onFailure: function () {
                    alert('failed');
                },
                onSuccess: function (t) {
                    var windows = JSON.parse(t.responseText);

                    for (var key in windows) {
                        if (windows.hasOwnProperty(key) && windows[key] != 'undefined') {
                            self.setInfoWindowContent(key,windows[key]);
                        }
                    }
                }
            });
        },

        /**
         * Bounce a marker to highlight it
         *
         * @param {int} id
         */
        highlightMarker: function(id){
            var self = this;
            if (self.markers[id].getAnimation() === null) {
                self.markers[id].setAnimation(google.maps.Animation.BOUNCE);
                self.stoppers[id] = setTimeout(function(){
                    self.markers[id].setAnimation(null);
                }, 720);
            }
        },

        /**
         * Get a google maps marker
         *
         * @param {Object} l object containing location data
         * @returns {google.maps.MarkerImage}
         */
        getMarkerImage: function(){
            return new google.maps.MarkerImage(
                '/skin/frontend/base/default/locator/images/pin.png',
                new google.maps.Size(40, 50),
                new google.maps.Point(0, 0),
                new google.maps.Point(20, 50)
            );
        },

        /**
         * Return theme settings, returns false by default but can be overridden to theme map
         *
         * @returns {boolean | Object}
         */
        getTheme : function() {
            return false;
        }
    });

    /**
     * @class List
     */
    Locator.List = Class.create({

        /**
         * @constructor
         * @param {HTMLElement} el
         */
        initialize: function (el) {
            this.el = el;
        },

        /**
         * Update list content
         *
         * @param {string} text
         */
        update: function (text) {
            this.el.update(text);
        }
    });


    /**
     * @class Search
     */
    Locator.Search = Class.create({

        /**
         * Construct the search class
         *
         * @constructor
         * @param {Object} options
         */
        initialize: function(options) {

            if (options) {
                //if override settings are
                if (options.settings) {
                    this.settings = $H(Locator.defaultSearchSettings).merge(options.settings);
                }

                if (options.map) {
                    this.map = options.map;
                }
            }

            //if options were not passed to the search class, use locator default
            if (!this.settings) {
                this.settings = Locator.defaultSearchSettings;
            }

            //if map has not already been set from options set it now
            if (!this.map && $$(this.settings.selectors.map).first()) {
                this.map = new Locator.Map($$(this.settings.selectors.map).first());
            }

            if ($$(this.settings.selectors.list).first()) {
                this.list = new Locator.List($$(this.settings.selectors.list).first());
            }

            this.forms = [];

            var self = this;

            this.initScroll();

            $$(this.settings.selectors.form).each(function (el) {
                self.forms.push(new Locator.Form(el, self));
            });

            //Bind map rendering to StateChange Event
            window.History.Adapter.bind(window, 'statechange', function () {

                var State = History.getState();

                if (State.data.locations && State.data.locations.length) {

                    if (self.list) {
                        self.list.update(State.data.output);
                    }

                    if (self.map) {
                        if (State.data.search_point) {
                            self.map.renderLocations(State.data.locations, State.data.search_point.coords);
                        } else {
                            self.map.renderLocations(State.data.locations);
                        }
                    }
                }
                self.initEvents();
            });
        },

        /**
         * Set initial history state when locations are not loaded from search, this will trigger map render
         *
         * @param {Object} data
         * @returns {Object}
         */
        initState: function(data){
            //inject a random parameter to query string so state always changes on first load
            var href = window.location.href+'&rand='+Math.random();
            var state = {};

            state.output = this.list.el.innerHTML;
            state.href = href.toQueryParams();

            if (data.locations !== '') {
                state.locations = this.parseLocationsJson(data.locations);
            }

            if (data.search_point) {
                state.search_point = data.search_point;
            }

            if (!state.locations.length) {
                this.toggleNoResults(true);
            }

            //reset the hash for old browsers to stop history.js errors
            if (History.getHash()) {
                window.location.hash = '';
            }

            History.replaceState(state,
                this.getSearchTitle(state.locations),
                window.location.search
            );

            return this;
        },

        /**
         * Make an ajax request to the server to find locations based on given query params
         *
         * @param {(string|Object)} query
         * @param callback
         * @returns {Object}
         */
        findLocations: function (query, callback) {

            var self = this;
            var href;

            if (typeof (query) === 'object') {
                query = $H(query).toQueryString();
            }

            href = this.settings.baseUrl+"locator/search/?" + query;

            new Ajax.Request(href +'&xhr=1', {

                method : 'get',
                onFailure: function () {
                    alert('search failed');
                },
                onSuccess: function (t) {
                    var result = self.parseSearchJson(t.responseText);
                    result.search = href.toQueryParams();

                    self.toggleNoResults(false);
                    if (result.error === true) {
                        if (result.error_type === 'noresults') {
                            self.toggleNoResults(true);
                        } else {
                            alert(result.message);
                        }
                    } else if (result.locations.length) {
                        History.pushState(result, self.getSearchTitle(result.locations), '?' + query);
                    } else {
                        alert('an error occured');
                    }

                    if (typeof callback === "function") {
                        callback.call();
                    }
                }
            });

            return this;
        },

        /**
         * Parse search result from server
         *
         * @param {string} string
         * @returns {Object}
         */
        parseSearchJson: function (string) {
            var search = JSON.parse(string);
            if (search.locations) {
                search.locations = this.parseLocationsJson(search.locations);
            }
            return search;
        },

        /**
         * Parse location json object
         *
         * @param {string} string
         * @returns {Array}
         */
        parseLocationsJson: function (string) {

            var locations = JSON.parse(string);
            var temp = [];

            for (var key in locations) {
                if (locations.hasOwnProperty(key) && locations[key] != 'undefined') {
                    temp.push(locations[key]);
                }
            }
            return temp;
        },

        /**
         * Show or hide no results page based on boolean parameter
         *
         * @param {boolean} empty
         * @returns {Object}
         */
        toggleNoResults: function (empty) {
            var els = $$(this.settings.selectors.results);
            if (empty) {
                els.each(function(el){
                    el.addClassName('is-no-results');
                });
            } else {
                els.each(function(el){
                    el.removeClassName('is-no-results');
                });
            }
            return this;
        },

        /**
         * Attach events to search UI
         *
         * @returns {Object}
         */
        initEvents: function() {
            var self = this;

            $$(self.settings.selectors.teaser).invoke('observe', 'click', function(){
                var id = this.readAttribute('data-id');
                self.map.showInfoWindow(id);
            });

            $$(self.settings.selectors.teaser).invoke('observe', 'mouseover', function() {
                var id = this.readAttribute('data-id');
                self.map.highlightMarker(id);
            });

            // Attach onclick events to search triggers
            $$(self.settings.selectors.trigger).invoke('observe', 'click', function(event){
                var el = Event.element(event);

                if (!el.readAttribute('href')) {
                    for (var i=0;i<10;i++) {
                        el = el.up();
                        if (el.readAttribute('href')) {
                            break;
                        }
                    }
                }

                var href = el.readAttribute('href');

                self.forms[0].startLoader();
                self.findLocations( href.toQueryParams(), function () {
                    self.forms[0].stopLoader();
                });
                Event.stop(event);
            });
            setTimeout(function(){
                google.maps.event.addListener(self.map.map, 'zoom_changed', function() {
                    self.hideNonVisible();
                });

                google.maps.event.addListener(self.map.map, 'dragend', function() {
                    self.hideNonVisible();
                });
            }, 1000);

            return this;
        },

        /**
         * Hide all markers not currently in view port and the matching item in list
         *
         * @returns {Object}
         */
        hideNonVisible: function(){
            var map = this.map;

            for (var key in map.markers) {
                if (map.markers.hasOwnProperty(key)) {
                    var marker = map.markers[key];
                    var teaser = $$(this.settings.selectors.list+' '+this.settings.selectors.teaser+'[data-id='+key+']').first();

                    if (!map.map.getBounds().contains(marker.getPosition())) {
                        if (teaser && !teaser.hasClassName('loc-closest')) {
                            teaser.addClassName('is-hidden');
                        }
                        marker.setVisible(false);
                    } else {
                        if (teaser) {
                            teaser.removeClassName('is-hidden');
                        }

                        marker.setVisible(true);
                    }
                }
            }

            return this;
        },

        /**
         * Create string to be displayed in the page title after a search has been performed
         *
         * @param {Array} locations
         * @returns {string}
         */
        getSearchTitle:function (locations){
            return "Search: " + locations.length + " Locations";
        },

        /**
         * Init scroll behaviour
         *
         * @returns {Object}
         */
        initScroll: function(){

            if (this.settings.stickyMap) {
                var map = $$('.loc-srch-res-map-wrap').first();
                var results = $$(this.settings.selectors.results).first();

                Event.observe(document, "scroll", function() {
                    if (results.viewportOffset().top < 1) {
                        map.addClassName('is-fixed');
                    } else {
                        map.removeClassName('is-fixed');
                    }
                });
            }
            return this;
        }
    });
})();