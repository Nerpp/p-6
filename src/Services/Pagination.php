<?php
namespace App\Services;


class Pagination
{
    const maximalTrick = 15;
    const maximalCom = 10;

    public function tricksPagination(int $iLenDis, int $iBdd)
    {
        $lenght = $iBdd - $iLenDis;

        if ($lenght >= self::maximalTrick) {
            return self::maximalTrick + $iLenDis;
        }elseif ($lenght >> 0 && $lenght << self::maximalTrick) {
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
