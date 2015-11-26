<?php

class HeuristicCostHandler
{
    /**
     * @param $point1
     * @param $point2
     * @return float
     */
    public function getEstimateCost($point1, $point2)
    {
        return $this->getHeuristic(array($point1,$point2));
    }

    /**
     * @param $point1
     * @param $point2
     * @return float
     */
    public function getGoalCost($point1, $point2)
    {
        return $this->getHeuristic(array($point1,$point2));
    }


    /**
     * @param $arrayPoints
     * @return float
     */
    public function getHeuristic($arrayPoints)
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

        return $dist = ((round($dist/10))/100);
    }
}