<?php

class CommService
{
    /**
     * @var HeuristicCostHandler
     */
    private $handler;

    public function __construct(HeuristicCostHandler $handler)
    {
        $this->handler=$handler;
    }

    /**
     * @param Point $x
     * @param Point $goalPoint
     * @param RouteItemList $list
     * @return bool|mixed
     */
    public function calculateOptimalRoute(Point $x, Point $goalPoint, RouteItemList $list)
    {
        $firstItem=new CalcRouteItem();
        $firstItem->setId($x->getId().'-'.$x->getId());
        $firstItem->setPoint($x);
        $firstItem->setParentPoint(null);
        $firstItem->setParentRouteItem(null);
        $firstItem->setGoalCost(0);
        $firstItem->setEstimateCost($this->handler->getEstimateCost($x,$goalPoint));
        $firstItem->setCurrentCost($firstItem->getGoalCost()+$firstItem->getEstimateCost());

        $openList=array($x->getId().'-'.$x->getId()=>$firstItem);
        $closeList=array();

        while (count($openList)!=0)
        {
            $openList=$this->sortList($openList);
            $currentItem=current($openList);
            $currKey=explode('-',$currentItem->getId());
            if (in_array($goalPoint->getId(),$currKey))
            {
                return $currentItem;
            }
            unset($openList[$currentItem->getId()]);
            $closeList[$currentItem->getId()]=$currentItem;

            foreach ($list->getNeighborList($currentItem) as $key=>$neighborItem)
            {
                if (!array_key_exists($key,$closeList))
                {
                    $neighborGoalCost=$currentItem->getGoalCost()+$this->handler->getHeuristic(array($currentItem->getPoint(),$neighborItem->getPoint()));

                    if (!array_key_exists($key,$openList))
                    {
                        $neighborItem->setParentRouteItem($currentItem);
                        $openList[$key]=$neighborItem;
                        $goodNeighbor=true;
                    }
                    else
                    {
                        if ($neighborGoalCost<$neighborItem->getGoalCost())
                        {
                            $goodNeighbor=true;
                        }
                        else
                        {
                            $goodNeighbor=false;
                        }
                    }
                    if ($goodNeighbor)
                    {
                        $neighborItem->setGoalCost($this->handler->getGoalCost($neighborItem->getPoint(),$currentItem->getPoint()));
                        $neighborItem->setEstimateCost($this->handler->getEstimateCost($neighborItem->getPoint(),$goalPoint));
                        $neighborItem->setCurrentCost($neighborItem->getGoalCost()+$neighborItem->getEstimateCost());
                    }
                }
            }
        }
        return false;
    }


    /**
     * @param CalcRouteItem $calcRouteItem
     */
    public function getResult($calcRouteItem)
    {
        $result=array();
        $totalCost=0;
        $currentRoute=$calcRouteItem;
        $endRoute=false;
        while (false===$endRoute)
        {
            if ($currentRoute->getParentRouteItem()===null)
            {
                $endRoute=true;
            }
            $result[]=$currentRoute->getPoint();
            $totalCost=$totalCost+$currentRoute->getGoalCost();
            $currentRoute=$currentRoute->getParentRouteItem();
        }
        $result=array_reverse($result);
        return array($result,$totalCost);
    }

    /**
     * @param array $list
     * @return mixed
     */
    private function sortList($list)
    {
        uasort($list,function($a,$b) {
            if ($a->getCurrentCost()==$b->getCurrentCost())
            {
                $res=0;
            }
            else if ($a->getCurrentCost()<$b->getCurrentCost())
            {
                $res=-1;
            }
            else
            {
                $res=1;
            }
            return $res;
        });
        return $list;
    }

}