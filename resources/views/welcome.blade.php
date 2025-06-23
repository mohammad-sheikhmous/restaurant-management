<!DOCTYPE html>
<html>
<head>
    <title>Polygon Drawing Example</title>
    <meta charset="utf-8">
    <style>
        #map {
            height: 500px;
            width: 100%;
        }
    </style>
</head>
<body>

<h3>ارسم منطقة على الخريطة</h3>
<div id="map" style="height: 500px;"></div>
<form action="{{route('test2')}}" method="POST">
    @csrf
    <input type="text" name="name[en]" placeholder="Zone English Name">
    <input type="text" name="name[ar]" placeholder="Zone Arabic Name">
    <input type="hidden" name="coordinates" id="zone_coordinates">
    <button type="submit">Save Zone</button>
</form>
<script>
    let map;
    let drawingManager;
    let selectedShape;

    function initMap() {
        map = new google.maps.Map(document.getElementById("map"), {
            center: { lat: 33.497514, lng: 36.3195355 },
            zoom: 12,
        });

        drawingManager = new google.maps.drawing.DrawingManager({
            drawingMode: google.maps.drawing.OverlayType.POLYGON,
            drawingControl: true,
            drawingControlOptions: {
                position: google.maps.ControlPosition.TOP_CENTER,
                drawingModes: [google.maps.drawing.OverlayType.POLYGON],
            },
            polygonOptions: {
                fillColor: '#28a745',
                fillOpacity: 0.3,
                strokeWeight: 2,
                clickable: true,
                editable: true,
                zIndex: 1
            }
        });

        drawingManager.setMap(map);

        google.maps.event.addListener(drawingManager, 'overlaycomplete', function (event) {
            if (event.type === 'polygon') {
                if (selectedShape) selectedShape.setMap(null);
                selectedShape = event.overlay;

                const coordinates = selectedShape.getPath().getArray().map(coord => ({
                    lat: coord.lat(),
                    lng: coord.lng()
                }));

                document.getElementById('zone_coordinates').value = JSON.stringify(coordinates);
            }
        });
    }

    window.initMap = initMap;
</script>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyD9zQQNoowad3i_Fycd6YrfbR2mfysHtnQ&libraries=drawing&callback=initMap" async defer></script>

</body>
</html>

