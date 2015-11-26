<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Задача коммивояжера</title>
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

$points=array();
$points['MOW']=new Point(1,'MOW',55.7557,37.6176);
$points['LED']=new Point(2,'LED',59.800292,30.262503);
$points['AER']=new Point(3,'AER',43.449928,39.956589);
$points['ROV']=new Point(4,'ROV',47.258208,39.818089);
$points['OVB']=new Point(5,'OVB',55.012622,82.650656);
$points['DXB']=new Point(6,'DXB',25.252778,55.364444);
$points['TSE']=new Point(7,'TSE',51.022222,71.466944);
$points['VVO']=new Point(8,'VVO',43.398953,132.148017);
$points['BKK']=new Point(9,'BKK',13.681108,100.747283);
$points['PAR']=new Point(10,'PAR',48.856389,2.352222);
$points['PRG']=new Point(11,'PRG',50.100833,14.26);
$points['NRT']=new Point(12,'NRT',35.764722,140.386389);
$points['KEF']=new Point(13,'KEF',63.985,-22.605556);
$points['JNB']=new Point(14,'JNB',-26.139166,28.246,5558);
$points['RAK']=new Point(15,'RAK',31.606886,-8.0363);
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



