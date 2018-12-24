<?php
namespace NACOSS\Helpers;

class CasingHelper {
  /**
   * Translates a string with underscores
   * into camel case (e.g. first_name -> firstName)
   *
   * @param string $str String in underscore format
   * @param bool $capitalise_first_char If true, capitalise the first char in $str
   * @return string $str translated into camel caps
   */
  public static function getCamelCase($str, $capitalise_first_char = false) {
    if ($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }

    return preg_replace_callback('/_([a-z])/', function ($c)
    {
      return strtoupper($c[1]);
    }, $str);
  }

  public static function getSentenceCase($str, $capitalise_first_char = false) {
    $str = strtolower($str);

    if ($capitalise_first_char) {
      $str[0] = strtoupper($str[0]);
    }
    
    $replaced = str_replace('_', ' ', $str);

    if (!$replaced) {
      return $str;
    }

    return $replaced;
  }
}
