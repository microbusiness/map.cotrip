<?php


class CalcRouteItem
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var Point
     */
    private $point;
    /**
     * @var Point
     */
    private $parentPoint;
    /**
     * @var CalcRouteItem
     */
    private $parentRouteItem;
    /**
     * @var float
     */
    private $goalCost;
    /**
     * @var float
     */
    private $estimateCost;
    /**
     * @var float
     */
    private $currentCost;

    /**
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return Point
     */
    public function getPoint()
    {
        return $this->point;
    }

    /**
     * @param Point $point
     */
    public function setPoint($point)
    {
        $this->point = $point;
    }

    /**
     * @return float
     */
    public function getCurrentCost()
    {
        return $this->currentCost;
    }

    /**
     * @param float $currentCost
     */
    public function setCurrentCost($currentCost)
    {
        $this->currentCost = $currentCost;
    }

    /**
     * @return float
     */
    public function getEstimateCost()
    {
        return $this->estimateCost;
    }

    /**
     * @param float $estimateCost
     */
    public function setEstimateCost($estimateCost)
    {
        $this->estimateCost = $estimateCost;
    }

    /**
     * @return float
     */
    public function getGoalCost()
    {
        return $this->goalCost;
    }

    /**
     * @param float $goalCost
     */
    public function setGoalCost($goalCost)
    {
        $this->goalCost = $goalCost;
    }

    /**
     * @return CalcRouteItem
     */
    public function getParentRouteItem()
    {
        return $this->parentRouteItem;
    }

    /**
     * @param CalcRouteItem $parentRouteItem
     */
    public function setParentRouteItem($parentRouteItem)
    {
        $this->parentRouteItem = $parentRouteItem;
    }

    /**
     * @return Point
     */
    public function getParentPoint()
    {
        return $this->parentPoint;
    }

    /**
     * @param Point $parentPoint
     */
    public function setParentPoint($parentPoint)
    {
        $this->parentPoint = $parentPoint;
    }
}