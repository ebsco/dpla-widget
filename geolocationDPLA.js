function showPosition(position) {
    var lat = encodeURIComponent(position.coords.latitude);
    var lon = encodeURIComponent(position.coords.longitude);
    var url = "http://api.dp.la/v2/items?sourceResource.spatial.coordinates="+lat+":"+lon+"&api_key=4085d1b6bab130d8aa14e4366a765cb1";
    
    $.get( url, function( data ) {
        $( "#dplaresult" ).html( data );
        alert( "Load was performed." );
    });
}

function showError(error) {
    switch(error.code) {
        case error.PERMISSION_DENIED:
            alert("User denied the request for Geolocation.");
            break;
        case error.POSITION_UNAVAILABLE:
            alert("Location information is unavailable.");
            break;
        case error.TIMEOUT:
            alert("The request to get user location timed out.");
            break;
        case error.UNKNOWN_ERROR:
            alert("An unknown error occurred.");
            break;
    }
}