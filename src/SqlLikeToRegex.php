<?php

namespace Menthol\SqlLikeToRegex;

/**
 * SqlLikeToRegex
 *
 */
class SqlLikeToRegex
{
    private static $pairDelimiters = [
        '(' => ')',
        '{' => '}',
        '[' => ']',
        '<' => '>'
    ];

    public static function convert($pattern, $delimiter = '/')
    {
        $pattern = preg_quote($pattern, $delimiter);
        $endDelimiter = self::getEndDelimiter($delimiter);
        $pattern = self::convertLikeCharsToRegex($pattern);
        return "{$delimiter}^{$pattern}\${$endDelimiter}";
    }

    /**
     * @param $delimiter
     * @return mixed
     */
    protected static function getEndDelimiter($delimiter)
    {
        return isset(self::$pairDelimiters[$delimiter]) ? self::$pairDelimiters[$delimiter] : $delimiter;
    }

    /**
     * @param $pattern
     * @return null|string|string[]
     */
    protected static function convertLikeCharsToRegex($pattern)
    {
        $pattern = preg_replace('/(?<!\\\\)%/', '.*', $pattern);
        $pattern = preg_replace('/(?<!\\\\)_/', '.', $pattern);
        return $pattern;
    }
}

