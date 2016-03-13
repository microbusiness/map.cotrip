<?php

class RouteItemList
{
    /**
     * @var array
     */
    private $list = array();

    /**
     * @return array
     */
    public function getList()
    {
        return $this->list;
    }

    /**
     * @param array $list
     */
    public function setList($list)
    {
        $this->list = $list;
    }

    /**
     * @param $point1
     * @param $point2
     */
    public function addItem($point1, $point2)
    {
        $cost=$this->getDistance(array($point1,$point2));

        if ($cost<4000)
        {
            $routeItem=new CalcRouteItem();
            $routeItem->setId($point1->getId().'-'.$point2->getId());
            $routeItem->setPoint($point2);
            $routeItem->setParentPoint($point1);
            $this->list[$point1->getId().'-'.$point2->getId()]=$routeItem;
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function getItem($key)
    {
        if (isset($this->list[$key]) || array_key_exists($key, $this->list))
        {
            return $this->list[$key];
        }
        else
        {
            return false;
        }
    }

    /**
     * @param CalcRouteItem $routeItem
     * @return array
     */
    public function getNeighborList(CalcRouteItem $routeItem)
    {
        $result=array();
        foreach ($this->list as $key=>$item)
        {
            $keyArray=explode('-',$key);
            if ((in_array($routeItem->getPoint()->getId(),$keyArray))&&($key!=$routeItem->getId()))
            {
                if ($routeItem->getPoint()->getId()==$item->getPoint()->getId())
                {
                    $tempPoint=$item->getPoint();
                    $item->setPoint($item->getParentPoint());
                    $item->setParentPoint($tempPoint);
                }

                $result[$key]=$item;
            }
        }
        return $result;
    }

    /**
     * @param $point1
     * @param $point2
     * @return bool
     */
    public function notExistRoute($point1, $point2)
    {
        $result1=$this->getItem($point1->getId().'-'.$point2->getId());
        $result2=$this->getItem($point2->getId().'-'.$point1->getId());
        if ((false==$result1)&&(false==$result2))
        {
            return true;
        }
        else
        {
            return false;
        }

    }

    /**
     * @param $arrayPoints
     * @return string
     */
    private function getDistance($arrayPoints)
    {
        $dist=0;
        $lastMarker=null;
        $first=true;
        foreach ($arrayPoints as $point)
        {
            if ($first)
            {
                $lastMarker=$point;
                $first=false;
            }
            else
            {
                $marpos1 = $lastMarker;
                $marpos2 = $point;
                $R = 6371000; // km (коэффициент для определения расстояния между двумя точками в километрах)
                $dLat = ($marpos2->getLat() - $marpos1->getLat()) * pi() / 180;
                $dLon = ($marpos2->getLon() - $marpos1->getLon()) * pi() / 180;
                $a = sin($dLat/2) * sin($dLat/2) + cos($marpos1->getLat() * pi() / 180 ) * cos($marpos2->getLat() * pi() / 180 ) * sin($dLon/2) * sin($dLon/2);
                $c = 2 * atan2(sqrt($a), sqrt(1-$a));
                $d = $R * $c;
                $dist = $dist+$d;
                $lastMarker=$point;
            }
        }

        return $dist = (string)((round($dist/10))/100);
    }

}