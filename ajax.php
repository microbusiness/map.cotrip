<?php

spl_autoload_register(function ($class) {
    include $class.'.php';
});

$list=new RouteItemList();
$handler=new HeuristicCostHandler();

$pointsSerial=file_get_contents('realdata.dat');
$points=unserialize($pointsSerial);

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