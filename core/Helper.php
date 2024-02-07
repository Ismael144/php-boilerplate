<?php

namespace App\core;

class Helper
{
  /**
   * Will filter strings from unwanted characters
   *
   * @param mixed $value
   * @return string
   */
  final public function filter(string $value)
  {
    return trim(mb_convert_encoding(htmlspecialchars(html_entity_decode($value)), "utf-8"));
  }
}
