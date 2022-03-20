<?php

use Services\ArchivesEssence as ArchivesEssence;
use Services\Collection;
use Services\XmlToReader as xmlToReader;
use Services\Fix as Fix;
use Services\TwitterManager;

// Affichage des erreurs qu'en local
if ($_SERVER["HTTP_HOST"] === "local.essence" || $_SERVER["SERVER_NAME"] === "local.essence") {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

// Gestionnaire de l'archive des prix d'essences
$xml = new ArchivesEssence();
$collection = new Collection();
$fix = new Fix(); // Fix erreur humaine

// On télécharge l'archive du jour, et on récupère les infos
$doc = $xml->toDownload();
if (!$doc) {
    do {
        $doc = $xml->toDownload();
        sleep(60);
    } while ($doc === false);
}
$xml->toUnzip();
$xml->toDelete();

// Lecture du fichier XML récupéré
$xmlToReader = new xmlToReader();
$toRead      = $xmlToReader->read();
$liste_pdv  = $toRead->pdv;
$pdv_infos = []; // infos des points de vente

$today      = date('d/m/Y à H:i', time());
$pdv_infos  = $xmlToReader->infos($liste_pdv);
$count_pdv  = $pdv_infos["count"];

// echo "<pre>"; var_dump($pdv_infos); echo "</pre>"; die;

$Gazole = $SP95 = $SP98 = $E10 = $E85 = $GPLc = [];
$minGazole = $minSP95 = $minSP98 = $minE10 = $minE85 = $minGPLc = 0;
$essences = ["Gazole","SP95","SP98","E10","E85","GPLc"];
$infos = $prix = [];

// Je récupère les différentes variables, liées aux types d'essences ($Gazole, $SP95, $SP98, $E10, $E85, $GPLc)
foreach ($essences as $essence) {
    foreach ($pdv_infos["infos"] as $pdv_info) {
        ${$essence}[]   = (array_key_exists($essence, $pdv_info)) ? $pdv_info[$essence]: -1;
    }
    foreach (${$essence} as $key => &$value) {
        if($value === -1) unset(${$essence}[$key]);
    }

    $prix += [
        $essence => $fix->min_price(${$essence})
    ];
    foreach ($pdv_infos["infos"] as &$pdv_info) {
        if (isset($pdv_info["Gazole"]) && isset($prix["Gazole"]) && $pdv_info["Gazole"] == str_replace(",", '.',$prix["Gazole"])) {
            $pdv_info["actif"] = $pdv_info["Gazole-actif"] = 1;
        }

        if (isset($pdv_info["SP95"]) && isset($prix["SP95"]) && $pdv_info["SP95"] == str_replace(",", '.',$prix["SP95"])) {
            $pdv_info["actif"] = $pdv_info["SP95-actif"] = 1;
        }

        if (isset($pdv_info["SP98"]) && isset($prix["SP98"]) && $pdv_info["SP98"] == str_replace(",", '.',$prix["SP98"])) {
            $pdv_info["actif"] = $pdv_info['SP98-actif'] = 1;
        }

        if (isset($pdv_info["E10"]) && isset($prix["E10"]) && $pdv_info["E10"] == str_replace(",", '.',$prix["E10"])) {
            $pdv_info["actif"] = $pdv_info['E10-actif'] = 1;
        }

        if (isset($pdv_info["E85"]) && isset($prix["E85"]) && $pdv_info["E85"] == str_replace(",", '.',$prix["E85"])) {
            $pdv_info["actif"] = $pdv_info['E85-actif'] = 1;
        }

        if (isset($pdv_info["GPLc"]) && isset($prix["GPLc"]) && $pdv_info["GPLc"] == str_replace(",", '.',$prix["GPLc"])) {
            $pdv_info["actif"] = $pdv_info['GPLc-actif'] = 1;
        }
    }
}

$infos = json_encode($pdv_infos["infos"], true);
?>

<!DOCTYPE html>
<html lang="en">
<head>

    <title>Prix essence en Haute-Garonne</title>

    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="shortcut icon" type="image/x-icon" href="docs/images/favicon.ico" />

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" integrity="sha512-xodZBNTC5n17Xt2atTPuE1HxjVMSvLVW9ocqUKLsCC5CXdbqCmblAshOMAS6/keqq/sMZMZ19scR4PsZChSR7A==" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js" integrity="sha512-XQoYMqMTK8LvdxXYG3nZ448hOEQiglfqkJs1NOQV44cWnUrBc8PkAOcXy20w0vlaXaVUearIOBhiXZ5V3ynxwA==" crossorigin=""></script>

    <style>
        html, body {
            height: 100%;
            margin: 0;
        }
        .leaflet-container,
        #map {
            min-height: 100%;
            min-width: 100%;
            max-width: 100%;
            max-height: 100%;
        }
    </style>

</head>
<body>
    <div id="map"></div>
    <script src="assets/geojson/departements.geojson"></script>
    <script src="assets/js/markers.js"></script>
    <script src="assets/js/params.js"></script>
    <script>
        let map = L.map('map').setView([43.366669, 1.25], 9);

        let departement = L.geoJSON(departements, {
            filter: filter,
            pointToLayer: function (feature, latlng) {
                return L.circleMarker(latlng);
            },
            style: style,
        }).addTo(map);

        let tiles = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoibWFwYm94IiwiYSI6ImNpejY4NXVycTA2emYycXBndHRqcmZ3N3gifQ.rJcFIG214AriISLbB6B5aw', {
            maxZoom: 18,
            attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, ' +
                'Imagery © <a href="https://www.mapbox.com/">Mapbox</a>',
            id: 'mapbox/streets-v11',
            tileSize: 512,
            zoomOffset: -1
        }).addTo(map);

        let list = <?php echo $infos; ?>

        list.forEach((item, index) => {
            text = '<b>'+item['ville']+'</b><br />'+item['adresse']+'<br />';

            let actifGazole = (item['Gazole-actif'] ? ' (*le moins cher)' : '');
            let actifSP95 = (item['SP95-actif'] ? ' (*le moins cher)' : '');
            let actifSP98 = (item['SP98-actif'] ? ' (*le moins cher)' : '');
            let actifE10 = (item['E10-actif'] ? ' (*le moins cher)' : '');
            let actifE85 = (item['E85-actif'] ? ' (*le moins cher)' : '');
            let actifGPLc = (item['GPLc-actif'] ? ' (*le moins cher)' : '');

            if (item['Gazole']) {   text += 'Prix Diesel'+actifGazole+' : '+item['Gazole']+'<br />'; }
            if (item['SP95']) {     text += 'Prix SP95'+actifSP95+' : '+item['SP95']+'<br />'; }
            if (item['SP98']) {     text += 'Prix SP98'+actifSP98+' : '+item['SP98']+'<br />'; }
            if (item['E10']) {     text += 'Prix E10'+actifE10+' : '+item['E10']+'<br />'; }
            if (item['E85']) {     text += 'Prix E85'+actifE85+' : '+item['E85']+'<br />'; }
            if (item['GPLc']) {     text += 'Prix GPLc'+actifGPLc+' : '+item['GPLc']+'<br />'; }

            let icon = (item['actif'] ? greenIcon : blueIcon);
            let marker = L.marker([item['latitude'], item['longitude']], {icon: icon})
                .addTo(map)
                .bindPopup(text);
        })

    </script>

</body>
</html>