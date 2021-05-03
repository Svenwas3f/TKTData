<?php
/**
 * Array key last
 */
if (! function_exists("array_key_last")) {
    function array_key_last($array) {
        if (!is_array($array) || empty($array)) {
            return NULL;
        }

        return array_keys($array)[count($array)-1];
    }
}

/**
 * Array key first
 */
 if (!function_exists('array_key_first')) {
     function array_key_first(array $arr) {
         foreach($arr as $key => $unused) {
             return $key;
         }
         return NULL;
     }
 }

/**
 * Function to convert some characters into html code
 *
 * $html = html code that should be converted
 */
function utf8Html( $html ) {
  //Replace some characters loooked up in this list
  //https://www.whatsmyip.org/html-characters/
  $characters = array(
    " \" " => "&quot;",
    " / " => "&sol", //Special, check that before and after is an space otherwise it could be an endocded info
    " ‚ " => "&sbquo;",
    " „ " => "&bdquo;",
    " & " => "&amp;", //Special, check that before and after is an space otherwise it could be an endocded info

    "†" => "&dagger;",
    "‡" => "&Dagger;",
    "‰" => "&permil;",
    "‘" => "&lsquo;",
    "’" => "&rsquo;",
    "“" => "&ldquo;",
    "”" => "&rdquo;",
    "¡" => "&iexcl;",

    "¢" => "&cent;",
    "£" => "&pound;",
    "¥" => "&yen;",
    "§" => "&sect;",

    "©" => "&copy;",

    "←" => "&larr;",
    "↑" => "&uarr;",
    "→" => "&rarr;",
    "↓" => "&darr;",

    "Ä" => "&Auml;",
    "Ö" => "&Ouml;",
    "Ü" => "&Uuml;",
    "ä" => "&auml;",
    "ö" => "&ouml;",
    "ü" => "&uuml;",
  );

  //Replace elements
  return str_replace( array_flip( $characters ) , $characters , $html );
}
 ?>
