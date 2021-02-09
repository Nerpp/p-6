<?php
namespace App\Services;


class Pagination
{
    const maximal = 15;

    public function pagination(int $iLenDis, int $iBdd)
    {
        $lenght = $iBdd - $iLenDis;

        if ($lenght >= self::maximal) {
            return self::maximal + $iLenDis;
        }elseif ($lenght >> 0 && $lenght << self::maximal) {
            return $iLenDis + $lenght;
        }elseif($lenght <= 0){
            return $iBdd;
        }
    }
    
}
