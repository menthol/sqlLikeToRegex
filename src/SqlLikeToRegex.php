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

    public static function convert($pattern, $delimiter = '/', $escape = null)
    {
        $endDelimiter = self::getEndDelimiter($delimiter);
        $pattern = self::convertLikeCharsToRegex($pattern, $escape, $delimiter);
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
     * @param $escape
     * @param $delimiter
     * @return null|string
     */
    protected static function convertLikeCharsToRegex($pattern, $escape, $delimiter)
    {
        if (! isset($escape)) {
            $quotedPattern = preg_quote($pattern, $delimiter);
            return str_replace(['%', '_'], ['.*', '.'], $quotedPattern);
        }

        $splitPattern = str_split($pattern);

        $pattern = '';
        while ($char = array_shift($splitPattern)) {
            if ($char == $escape) {
                $char = array_shift($splitPattern);
                $pattern .= preg_quote($char, $delimiter);
            }
            elseif ($char == '%') {
                $pattern .= '.*';
            }
            elseif ($char == '_') {
                $pattern .= '.';
            }
            else {
                $pattern .= preg_quote($char, $delimiter);
            }
        }

        return $pattern;
    }
}

