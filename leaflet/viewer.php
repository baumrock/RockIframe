
<!DOCTYPE html>
<html>
<head>
  <title><?= $page->title ?></title>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>
  <style>
    html, body {
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
    var bounds = [[0,0], [<?= $y ?>,<?= $x ?>]];
    var image = L.imageOverlay('<?= $img ?>', bounds).addTo(map);
    map.fitBounds(bounds);
    <?php if(isset($zoom)) echo "map.setZoom($zoom);"; ?>
  </script>
</body>
</html>
