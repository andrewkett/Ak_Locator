var Locator = Locator || { 'settings': {}, 'map': {'zoom_level':15, 'markers':[]}};
var gmaps;

function initLocator(){

    gmaps = google.maps;

    Locator.settings.defaultMapOptions = {
        zoom: 15,
        scrollwheel: false,
        mapTypeId: gmaps.MapTypeId.ROADMAP,
        mapTypeControl: false,
        mapTypeControlOptions: {
            style: gmaps.MapTypeControlStyle.HORIZONTAL_BAR,
            position: gmaps.ControlPosition.BOTTOM_CENTER,
            mapTypeIds: [gmaps.MapTypeId.ROADMAP, 'locator']
        },
        streetViewControl: false,
        streetViewControlOptions: {
            position: gmaps.ControlPosition.LEFT_TOP
        }
    };

    Locator.settings.markers = [];

    Locator.settings.markers['default'] = new gmaps.MarkerImage(
        '/skin/frontend/base/default/locator/images/pin.png',
        new gmaps.Size(40, 50),
        new gmaps.Point(0, 0),
        new gmaps.Point(20, 50)
    );

    Locator.firstLoad = true;
}

function clearOverlays() {
    for (var key in Locator.map.markers) {
        if (Locator.map.markers.hasOwnProperty(key)) {
            Locator.map.markers[key].setMap(null);
        }
    }
}

function renderMarker(l){
  return '<div id="content">'+
    '<div id="siteNotice">'+
    '</div>'+
    '<h2 id="firstHeading" class="firstHeading">'+l.title+'</h2>'+
    '<div id="bodyContent">'+
      '<p>Approx '+l.distance+'km from you</p>'+
    '</div>'+
    '<p><a href="'+l.directions+'" target="_blank">Get Directions</a></p>'+
    '</div>';
}

function renderMap(map, locations){

  var latlngbounds = new gmaps.LatLngBounds();

  infowindows = Array();
  clearOverlays();
  markers = Locator.map.markers;

  if(Locator.settings.theme){
    var styledMap = new gmaps.StyledMapType(Locator.settings.theme,{name: "Locator"});
    map.mapTypes.set('locator', styledMap);
    map.setMapTypeId('locator');
  }




  // var shadow = new google.maps.MarkerImage(
  //   '/skin/frontend/base/default/locator/images/shadow.png',
  //   new google.maps.Size(62,35),
  //   new google.maps.Point(0, 0),
  //   new google.maps.Point(0,35)
  // );

  // var shape = {
  //   coord: [27,0,30,1,32,2,34,3,35,4,36,5,38,6,39,7,39,8,39,9,39,10,38,11,37,12,33,13,34,14,34,15,33,16,32,17,31,18,27,19,28,20,28,21,27,22,26,23,22,25,23,26,24,27,24,28,24,29,24,30,24,31,24,32,23,33,22,34,17,34,16,33,15,32,15,31,14,30,14,29,15,28,15,27,16,26,17,25,13,23,12,22,11,21,11,20,12,19,8,18,7,17,6,16,5,15,5,14,6,13,2,12,1,11,0,10,0,9,0,8,0,7,1,6,3,5,4,4,5,3,7,2,9,1,12,0,27,0],
  //   type: 'poly'
  // };

  var show = 0;

  for (var key in locations) {
    if (locations.hasOwnProperty(key)) {

      var l = locations[key];

        loc = new gmaps.LatLng(l.latitude, l.longitude);

        infowindows[l.id] = new gmaps.InfoWindow({
            content: renderMarker(l)
        });

        markers[l.id] = new gmaps.Marker({
          position: loc,
          map: map,
          title: l.title,
          icon: Locator.settings.markers['default'],
          // shadow: shadow,
          // shape: shape,
          animation: gmaps.Animation.DROP
        });

         markers[l.id].id = l.id;

        gmaps.event.addListener(markers[l.id], 'click', function() {
          hideInfoWindows();
          infowindows[this.id].open(map,markers[this.id]);
        });

        latlngbounds.extend( loc );
        show = 1;
    }
  }

    if(!show){
        $('locator-results').addClassName('is-no-results');
    }

    map.fitBounds( latlngbounds );

    if(Locator.settings.maxZoom){
      //when the map loads, make sure it hasn't zoomed in to far, if it has zoom out
      //@todo, configure the zoom level in admin
      var listener = gmaps.event.addListener(map, "idle", function() {
          if (map.getZoom() > Locator.settings.maxZoom) map.setZoom(Locator.settings.maxZoom);
          gmaps.event.removeListener(listener);
      });
    }


    gmaps.event.trigger(map, 'resize');

}

function hideInfoWindows(){
  for (var key in infowindows) {
    if (infowindows.hasOwnProperty(key)) {
      infowindows[key].close();
    }
  }
}


function parseSearchJson(string){

  search = JSON.parse(string);

  if(search.locations){
    search.locations = parseLocationsJson(search.locations);
  }

  return search;

}

function parseLocationsJson(string){

  locations = JSON.parse(string);
  temp = Array();

  for (var key in locations) {
    if (locations.hasOwnProperty(key) && locations[key] != 'undefined') {
      temp.push(locations[key]);
    }
  }

  return temp;
}


function initRollovers(){

  var stoppers = Array();

  $$('.loc-teaser').invoke('observe', 'mouseover', function(event) {
      var id = this.readAttribute('data-id');
      if(markers[id].getAnimation() === null){
        markers[id].setAnimation(google.maps.Animation.BOUNCE);
        stoppers[id] = setTimeout(function(){
          markers[id].setAnimation(null);
        }, 720);
      }
  });

  $$('.loc-teaser').invoke('observe', 'click', function(event) {
      id = this.readAttribute('data-id');
      hideInfoWindows();
      infowindows[id].open(map,markers[id]);

  });
}

function initStorePage(){
  map = new gmaps.Map(document.getElementById("location-map"), Locator.settings.defaultMapOptions);
  renderMap(map, Locator.locations);
}

function initSearchPage(){

  searchForm = $('locator-search');

  //if(!window.location.href.toQueryParams().s && !window.location.href.toQueryParams().a){
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(function(position){

        //if user hasn't already entered search params and geolocation is supported move map to users location
        //findLocations('lat='+position.coords.latitude+'&long='+position.coords.longitude);

        query = 'lat='+position.coords.latitude+'&long='+position.coords.longitude;

        $$('span.search-areas').each(function(el){
          el.insert({bottom:'<a class="near-me" href="?'+query+'">Near You</a>'} );
        });

        Event.observe($$('.near-me').first(),'click',function(event){
            findLocations(query);
            Event.stop(event);
        });

      }, function(){
        alert('error geocoding');
      });
    }
  //}

  //@todo for some reason the stat object is not being passed to the state change event on initial load when there is only one location
  History.replaceState({locations: Locator.locations, output: $('search-info').innerHTML, search: searchForm.serialize().toQueryParams()}, "Search", window.location.search);

  map = new gmaps.Map(document.getElementById("location-map"), Locator.settings.defaultMapOptions);
  renderMap(map, Locator.locations);
  initRollovers();



  Event.observe(searchForm, 'submit', function(event) {

    if("" !== searchForm.serialize().toQueryParams().s){
      findLocations(searchForm.serialize());
    }

    Event.stop(event);
  });

  $$('.search-areas a').each(function(narrowByElement){
      Event.observe($(narrowByElement),'click',function(event){
          var href = Event.element(event).readAttribute('href');

          findLocations(href.toQueryParams());
          Event.stop(event);
      });
  });


  //Bind to StateChange Event
  History.Adapter.bind(window,'statechange',function(){

      var State = History.getState();
      //History.log(State.data, State.title, State.url);

      if(State.data.locations.length){
        $('locator-results').removeClassName('is-no-results');
        renderMap(map, State.data.locations);
        $('search-info').update(State.data.output);
      }else{
        //renderMap(map, {});
        //$('search-info').update(State.data.output);
        //location.reload();
      }
      initRollovers();

  });

}

function findLocations(query){
  loader = $('locator-loader');
  loader.addClassName('is-loading');

  if(typeof(query) === 'object'){
    query = $H(query).toQueryString();
  }

  href=window.location.pathname+'?'+query;

  new Ajax.Request(window.location.pathname+'?'+query, {

    method:'get',
    onFailure: function() {
      alert('search failed');
      loader.removeClassName('is-loading');
    },
    onSuccess: function(t) {
      results = $('locator-results');
      searchForm = $('locator-search');

      result = parseSearchJson(t.responseText);
      result.search = href.toQueryParams();
      results.removeClassName('is-no-results');
      if(result.error === true){
        if(result.error_type === 'noresults'){
          results.addClassName('is-no-results');
        }else{
          alert(result.message);
        }
      }else if(result.locations.length){
        History.pushState(result, "Search: "+result.locations.length+" Locations", '?'+query);
      }else{
        alert('an error occured');
      }
      loader.removeClassName('is-loading');
    }

  });

}


// Location autocomplete

function initializeAutocomplete() {
    autocomplete = new gmaps.places.Autocomplete(document.getElementById('locator-autocomplete'), { types: [ 'geocode' ],componentRestrictions: {country: Locator.settings.country_code} });
    // google.maps.event.addListener(autocomplete, 'place_changed', function() {
    //   alert('handle response');
    //   //fillInAddress();
    // });
  }


