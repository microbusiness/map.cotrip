<?php

/**
 * Created by PhpStorm.
 * User: ivan
 * Date: 13.03.2016
 * Time: 17:58
 */
class Geo
{
    private $city;

    public function getCities()
    {
        $f = fopen('cities15000.txt',  'r');
        while (!feof($f))
        {
            $line=fgets($f);
            $lineArr=explode("\t",$line);
            if (isset($lineArr[14]))
            {
                $population=(integer)$lineArr[14];
                if ($population>2000000)
                {
                    $geoObject=array();
                    $geoObject['id']=$lineArr[0];
                    $geoObject['name']=$lineArr[1];
                    $geoObject['lat']=(float)$lineArr[4];
                    $geoObject['lon']=(float)$lineArr[5];
                    $geoObject['country_code']=$lineArr[8];
                    $geoObject['population']=$population;
                    $geoObject['timezone']=$lineArr[17];

                    $this->city[]=new Point($geoObject['id'],$geoObject['name'],$geoObject['lat'],$geoObject['lon'],$geoObject['timezone']);
                }
            }

        }
        fclose($f);
    }

    public function getCityExist($point,$maxDist)
    {
        $result=false;
        foreach ($this->city as $item)
        {
            $realDist=$this->getDist($point,$item);
            if ($realDist<=$maxDist)
            {
                $result=$realDist;
                break;
            }
        }
        return $result;
    }

    private function getDist($begin,$end)
    {
        $marpos1 = $begin;
        $marpos2 = $end;
        $R = 6371; // km (коэффициент для определения расстояния между двумя точками в километрах)
        $dLat = ($marpos2->getLat() - $marpos1->getLat()) * pi() / 180;
        $dLon = ($marpos2->getLon() - $marpos1->getLon()) * pi() / 180;
        $a = sin($dLat/2) * sin($dLat/2) + cos($marpos1->getLat() * pi() / 180 ) * cos($marpos2->getLat() * pi() / 180 ) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $d = $R * $c;


        return $d;
    }

}