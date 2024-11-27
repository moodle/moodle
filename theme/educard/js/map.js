(function() {
    'use strict';
    var LeafIcon = L.Icon.extend({
        options: {
            iconSize: [59, 75],
            iconAnchor: [15, 100]
        }
    });
  
    var geolocation_1 = $("#map-geolocation-1").text();
    var geolocation_2 = $("#map-geolocation-2").text();
    var map = L.map('map').setView([geolocation_1,geolocation_2], 13);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    var address = $("#map-address").text();
    L.marker([geolocation_1,geolocation_2]).addTo(map)
        .bindPopup(address)
        .openPopup();
})(jQuery);
