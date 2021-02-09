<?php
namespace App\Services;


class Pagination
{
    const maximal = 15;
    const maximalCom = 5;

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

    public function commentsPagination(int $iLenDis, int $iBdd)
    {
        $lenght = $iBdd - $iLenDis;

        if ($lenght >= self::maximalCom) {
            return self::maximalCom + $iLenDis;
        }elseif ($lenght >> 0 && $lenght << self::maximalCom) {
            return $iLenDis + $lenght;
        }elseif($lenght <= 0){
            return $iBdd;
        }
    }
    
}
