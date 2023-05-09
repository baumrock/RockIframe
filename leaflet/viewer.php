<!DOCTYPE html>
<html>

<head>
  <title><?= $page->title ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin="" />
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  <style>
    html,
    body {
      height: 100%;
      margin: 0;
    }

    #map {
      width: 100%;
      height: 100%;
    }
  </style>
</head>

<body>
  <div id='map'></div>
  <script>
    var map = L.map('map', {
      crs: L.CRS.Simple,
      minZoom: <?= $minZoom ?>,
      maxZoom: <?= $maxZoom ?>
    });
    var bounds = [
      [0, 0],
      [<?= $y ?>, <?= $x ?>]
    ];
    var image = L.imageOverlay('<?= $img ?>', bounds).addTo(map);
    map.fitBounds(bounds);
    let flyto = localStorage.getItem('map-flyto');
    if (flyto) {
      flyto = JSON.parse(flyto);
      // map.flyTo([flyto.lat, flyto.lng], flyto.zoom);
      map.setView(new L.LatLng(flyto.lat, flyto.lng), flyto.zoom);
    }
    <?php if (isset($zoom)) echo "if(!flyto) map.setZoom($zoom);"; ?>
    map.on('moveend', (e) => {
      let center = map.getCenter();
      let json = JSON.stringify({
        lat: center.lat,
        lng: center.lng,
        zoom: map.getZoom(),
      });
      console.log(json);
      localStorage.setItem('map-flyto', json);
    });
  </script>
</body>

</html>