<!-- resources/views/map.blade.php -->
<!DOCTYPE html>
<html>
<head>
    <title>Google Map</title>
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>
    <h3>Google Map Tích Hợp Trong Laravel</h3>
    <div id="map"></div>

    <script>
        function initMap() {
           const location = { lat: 10.7769, lng: 106.7009 }; // Hồ Chí Minh, Việt Nam
            const map = new google.maps.Map(document.getElementById("map"), {
                zoom: 12,
                center: location,
            });
            const marker = new google.maps.Marker({
                position: location,
                map: map,
            });
        }
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD4krw8lVLviWoq1rDZsZe0lznaoP4-oAo&callback=initMap"
    async defer></script>
</body>
</html>