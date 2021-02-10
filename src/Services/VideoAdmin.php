<?php
namespace App\Services;

class VideoAdmin
{
    public function addEmbed(string $var)
    {
        parse_str( parse_url( $var, PHP_URL_QUERY ), $my_array );
        return'https://www.youtube.com/embed/'.$my_array['v'];
    }
}
