<?php
namespace App\Services;

class VideoAdmin
{
    public function addEmbed(string $var)
    {
       
        if (preg_match("/\bembed\b/i",$var)) {
            return $var;
        } else { 
            
            parse_str( parse_url( $var, PHP_URL_QUERY ), $my_array );
            
            return'https://www.youtube.com/embed/'.$my_array['v'];
        }
        
    }
}
