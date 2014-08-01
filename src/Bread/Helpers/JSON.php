<?php
namespace Bread\Helpers;

use Exception;

class JSON
{

    public static function encode($target, $properties = array(), $append = array())
    {
        if ($properties) {
            $obj = array();
            if (is_array($target)) {
                foreach ($target as $value) {
                    $entry = array();
                    foreach ($properties as $property) {
                        $entry[$property] = $value->$property;
                    }
                    $obj[] = $entry;
                }
            } else {
                foreach ($properties as $property) {
                    $obj[$property] = $target->$property;
                }
            }
            $json = json_encode($obj, JSON_PRETTY_PRINT);
        } else {
            $json = json_encode($target, JSON_PRETTY_PRINT);
            $properties = json_decode($json, true);
            foreach ($append as $key => $value) {
                $properties[$key] = $value;
            }
            $json = json_encode($properties, JSON_PRETTY_PRINT);
        }
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