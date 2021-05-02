<?php
namespace App\Services;

class Pagination
{
    const MAXIMALTRICK = 15;
    const MAXIMALCOM = 10;

    public function tricksPagination(int $iLenDis, int $iBdd)
    {
        $lenght = $iBdd - $iLenDis;

        if ($lenght >= self::MAXIMALTRICK) {
            return self::MAXIMALTRICK + $iLenDis;
        }elseif ($lenght >> 0 && $lenght << self::MAXIMALTRICK) {
            return $iLenDis + $lenght;
        }elseif($lenght <= 0) {
            return $iBdd;
        }
    }

    public function commentsPagination(int $iLenDis, int $iBdd)
    {
        $lenght = $iBdd - $iLenDis;

        if ($lenght >= self::MAXIMALCOM) {
            return self::MAXIMALCOM + $iLenDis;
        }elseif ($lenght >> 0 && $lenght << self::MAXIMALCOM) {
            return $iLenDis + $lenght;
        }elseif($lenght <= 0) {
            return $iBdd;
        }
    }
}