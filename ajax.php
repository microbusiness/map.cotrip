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
foreach ($points as $point1)
{
    foreach ($points as $point2)
    {
        if ($point1->getId()!=$point2->getId())
        {
            if ($list->notExistRoute($point1,$point2))
            {
                $list->addItem($point1,$point2);
            }
        }
    }
}

$service=new CommService($handler);

$arrAjaxRoutes=explode('_',$_GET['path']);

$route=$service->calculateOptimalRoute($points[$arrAjaxRoutes[0]],$points[$arrAjaxRoutes[1]],$list);

$json=array('route'=>array(),'cost'=>0,'status'=>true,'message'=>'');
if (false===$route)
{
    $json['status']=false;
    $json['message']='Доступных маршрутов нет';
}
else
{
    list($routeList,$totalCost)=$service->getResult($route);

    foreach ($routeList as $point)
    {
        $json['route'][]=$point->getName();
    }
    $json['cost']=$totalCost;
}

echo json_encode($json);
?>