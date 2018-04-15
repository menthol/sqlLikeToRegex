<?php

namespace Menthol\SqlLikeToRegex;

/**
 * SqlLikeToRegex
 *
 */
class SqlLikeToRegex
{
    /** @var array */
    private $pairDelimiters = [
        '(' => ')',
        '{' => '}',
        '[' => ']',
        '<' => '>'
    ];

    /** @var string */
    private $delimiter = '/';

    /** @var string */
    private $endDelimiter = '/';

    /** @var string|null */
    private $escape;

    /** @var bool */
    private $isCaseSensitive = false;

    /** @var string */
    private $pattern;

    /** @var string|null */
    private $regex;


    /**
     * @param $subject
     * @return bool
     */
    public function test($subject)
    {
        return !! preg_match($this->getRegex(), $subject);
    }

    /**
     * @param $pattern
     * @return string
     */
    private function convert($pattern)
    {
        $pattern = $this->convertLikeCharsToRegex($pattern);
        $options = ($this->isCaseSensitive ? '' : 'i');
        return "{$this->delimiter}^{$pattern}\${$this->endDelimiter}{$options}";
    }

    /**
     * @param $pattern
     * @return null|string
     */
    protected function convertLikeCharsToRegex($pattern)
    {
        if (!isset($this->escape)) {
            $quotedPattern = preg_quote($pattern, $this->delimiter);
            return str_replace(['%', '_'], ['.*', '.'], $quotedPattern);
        }

        $splitPattern = str_split($pattern);

        $pattern = '';
        while ($char = array_shift($splitPattern)) {
            if ($char == $this->escape) {
                $char = array_shift($splitPattern);
                $pattern .= preg_quote($char, $this->delimiter);
            } elseif ($char == '%') {
                $pattern .= '.*';
            } elseif ($char == '_') {
                $pattern .= '.';
            } else {
                $pattern .= preg_quote($char, $this->delimiter);
            }
        }

        return $pattern;
    }

    /**
     * @return string
     */
    public function getDelimiter()
    {
        return $this->delimiter;
    }

    /**
     * @param $delimiter
     * @return mixed
     */
    public function getEndDelimiter($delimiter)
    {
        return isset($this->pairDelimiters[$delimiter]) ? $this->pairDelimiters[$delimiter] : $delimiter;
    }

    /**
     * @param string $delimiter
     * @return SqlLikeToRegex
     */
    public function setDelimiter($delimiter)
    {
        $this->delimiter = $delimiter;
        $this->endDelimiter = $this->getEndDelimiter($delimiter);
        $this->regex = null;

        return $this;
    }

    /**
     * @return null|string
     */
    public function getEscape()
    {
        return $this->escape;
    }

    /**
     * @param null|string $escape
     * @return SqlLikeToRegex
     */
    public function setEscape($escape)
    {
        $this->escape = $escape;
        $this->regex = null;

        return $this;
    }

    /**
     * @return SqlLikeToRegex
     */
    public function setCaseSensitive()
    {
        $this->isCaseSensitive = true;
        $this->regex = null;

        return $this;
    }

    /**
     * @return SqlLikeToRegex
     */
    public function setCaseInsensitive()
    {
        $this->isCaseSensitive = false;
        $this->regex = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getPattern()
    {
        return $this->pattern;
    }

    /**
     * @param string $pattern
     * @return SqlLikeToRegex
     */
    public function setPattern($pattern)
    {
        $this->pattern = $pattern;
        $this->regex = null;

        return $this;
    }

    /**
     * @return string
     */
    public function getRegex()
    {
        if (!isset($this->regex)) {
            $this->regex = $this->convert($this->pattern);
        }

        return $this->regex;
    }
}

