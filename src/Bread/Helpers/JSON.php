<?php

namespace Bread\Helpers;

use Exception;

class JSON
{
    public static function encode($target)
    {
        $json = json_encode($target);
        switch (json_last_error()) {
          case JSON_ERROR_NONE:
              return $json;
          case JSON_ERROR_DEPTH:
          case JSON_ERROR_STATE_MISMATCH:
          case JSON_ERROR_CTRL_CHAR:
          case JSON_ERROR_SYNTAX:
          case JSON_ERROR_UTF8:
              throw new Exception(json_last_error_msg());
        }
    }
    
    public static function decode($json, $assoc = true)
    {
        $array = json_decode($json, $assoc);
        switch (json_last_error()) {
          case JSON_ERROR_NONE:
              return $array;
          case JSON_ERROR_DEPTH:
          case JSON_ERROR_STATE_MISMATCH:
          case JSON_ERROR_CTRL_CHAR:
          case JSON_ERROR_SYNTAX:
          case JSON_ERROR_UTF8:
              throw new Exception(json_last_error_msg());
        }
    }
}