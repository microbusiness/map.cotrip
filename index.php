<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Поиск кратчайших аэромаршрутов</title>
    <meta name="viewport" content="initial-scale=1.0">
    <meta charset="utf-8">
    <style>
        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }
        #map {
            height: 100%;
        }
    </style>
</head>
<body>

<?php

spl_autoload_register(function ($class) {
    include $class.'.php';
});

$list=new RouteItemList();
$handler=new HeuristicCostHandler();

if (!file_exists('realdata.dat'))
{
    $geo=new Geo();
    $city=$geo->getCities();

    if (!file_exists('airport.dat'))
    {
        $data=file_get_contents('https://raw.githubusercontent.com/jpatokal/openflights/master/data/airports.dat');
        file_put_contents('airport.dat',$data);
    }

    $dataAirport=file('airport.dat');
    $points=array();
    $i=1;
    foreach ($dataAirport as $airport)
    {
        $airport=str_replace('"','',$airport);
        $ai=explode(',',$airport);

        $newPoint=new Point($ai[0],$ai[4],(float)$ai[6],(float)$ai[7],$ai[1].' '.$ai[2].' '.$ai[3]);

        if ($geo->getCityExist($newPoint,30)!==false)
        {
            if ($ai[4]!='')
            {
                $points[$ai[4]]=$newPoint;
            }
        }

    }

    $pointsSerial=serialize($points);
    file_put_contents('realdata.dat',$pointsSerial);
}
else
{
    $pointsSerial=file_get_contents('realdata.dat');
    $points=unserialize($pointsSerial);
}

?>

<div id="map"></div>
<script>

    var map;
    function initMap() {
        map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 49.546453, lng: 49.567768},
            zoom: 3
        });

        <?php
            echo 'coords=[];';
            foreach ($points as $p)
            {
                echo 'coords["'.$p->getName().'"]={lat: '.$p->getLat().', lng: '.$p->getLon().'};';
            }
        ?>
        markers=[];
        currMarkers=[];
        image='airport_red.png';
        for(var i in coords) {
            markers[i]=new google.maps.Marker({
                position: coords[i],
                map: map,
                title: i,
                icon: image
            });
            markers[i].addListener('click', function() {
                if (currMarkers.length==2)
                {
                    for (var i in currMarkers){
                        currMarkers[i].setIcon('airport_red.png');
                    }
                    currMarkers=[];
                    if (typeof flightPath !== "undefined")
                    {
                        flightPath.setMap(null);
                    }
                }
                this.setIcon('airport.png');
                currMarkers.push(this);
                if (currMarkers.length==2)
                {

                    var path_='';
                    for (var i in currMarkers){
                        path_=path_+currMarkers[i].getTitle()+'_';
                    }
                    var xhr = new XMLHttpRequest();
                    xhr.open('GET', 'ajax.php?path='+path_, false);
                    xhr.send();
                    if (xhr.status != 200) {
                        alert( xhr.status + ': ' + xhr.statusText );
                    } else {

                        obj = JSON.parse(xhr.response);
                        var flightPlanCoordinates=[];
                        for (var i in obj.route){
                            var newMarker=markers[obj.route[i]];
                            flightPlanCoordinates.push(newMarker.getPosition());
                        }

                        flightPath = new google.maps.Polyline({
                            path: flightPlanCoordinates,
                            geodesic: true,
                            strokeColor: '#FF0000',
                            strokeOpacity: 1.0,
                            strokeWeight: 2
                        });

                        flightPath.setMap(map);

                    }
                }
            });
        };




    }

</script>
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDrJQVjROibCO9TbJF_MN-rHB2z25li3OU&callback=initMap"  async defer></script>


</body>
</html>



