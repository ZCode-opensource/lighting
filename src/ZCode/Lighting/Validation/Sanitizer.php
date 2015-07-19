<?php

/*
 * This file is part of the ZCode Lighting Web Framework.
 *
 * (c) Álvaro Somoza <asomoza@zcode.cl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ZCode\Lighting\Validation;

class Sanitizer
{
    public static function sanitizeBooleanValue($value)
    {
        if (is_bool($value) && $value === true) {
            return true;
        }

        return false;
    }

    public static function sanitizeStringValue($value)
    {
        if (is_string($value) && strlen($value) > 0) {
            $value = trim($value);
            return $value;
        }

        return null;
    }

    public static function sanitizeIntegerValue($value)
    {
        if (is_int($value)) {
            return $value;
        }

        return null;
    }

    public static function sanitizeArrayValue($value)
    {
        if (is_array($value)) {
            return $value;
        }

        return null;
    }

    public static function sanitizeNumericValue($value)
    {
        if (is_numeric($value)) {
            return $value;
        }

        return null;
    }

    public static function sanitizeFloatValue($value)
    {
        if (is_float($value)) {
            return $value;
        }

        return null;
    }
}