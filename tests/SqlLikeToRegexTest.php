<?php

namespace Menthol\SqlLikeToRegexTests;

use Menthol\SqlLikeToRegex\SqlLikeToRegex;
use PHPUnit\Framework\TestCase;

class SqlLikeToRegexTest extends TestCase
{
    /** @test */
    public function it_do_not_touch_pattern_without_special_chars()
    {
        $patterns = ['foo', 'bar', 123, '', true, null];
        foreach ($patterns as $pattern) {
            $this->assertEquals("/^{$pattern}$/", SqlLikeToRegex::convert($pattern));
        }
    }

    /** @test */
    public function it_should_protect_delimiter_char()
    {
        $patterns = [
            '/delimiters/' => '/^\/delimiters\/$/',
            '/delimiters with option/s' => '/^\/delimiters with option\/s$/',
        ];
        foreach ($patterns as $pattern => $expected) {
            $this->assertEquals($expected, SqlLikeToRegex::convert($pattern));
        }

        $patterns = [
            '+delimiters+' => '+^\+delimiters\+$+',
            '+delimiters with option+s' => '+^\+delimiters with option\+s$+',
        ];
        foreach ($patterns as $pattern => $expected) {
            $this->assertEquals($expected, SqlLikeToRegex::convert($pattern, '+'));
        }

        $patterns = [
            '(this [is] a (pattern))' => ['(', '(^\(this \[is\] a \(pattern\)\)$)'],
            '{this [is] a (pattern)}' => ['{', '{^\{this \[is\] a \(pattern\)\}$}'],
            '[this [is] a (pattern)]' => ['[', '[^\[this \[is\] a \(pattern\)\]$]'],
            '<this [is] a (pattern)>' => ['<', '<^\<this \[is\] a \(pattern\)\>$>'],
        ];
        foreach ($patterns as $pattern => $options) {
            list ($delimiter, $expected) = $options;
            $this->assertEquals($expected, SqlLikeToRegex::convert($pattern, $delimiter));
        }
    }

    /** @test */
    public function it_should_protect_meta_characters()
    {
        $converted = SqlLikeToRegex::convert('\^$.[]|()?*+{}');
        $this->assertEquals('/^\\\\\^\\$\\.\\[\\]\\|\\(\\)\\?\\*\\+\\{\\}$/', $converted);
    }

    /** @test */
    public function it_should_convert_percent_sign_and_underscore_to_regex()
    {
        $patterns = [
            '%' => '/^.*$/',
            '_' => '/^.$/',
        ];
        foreach ($patterns as $pattern => $expected) {
            $this->assertEquals($expected, SqlLikeToRegex::convert($pattern));
        }
    }

    /** @test */
    public function it_should_accept_escaping_percent_sign_and_underscore()
    {
        $patterns = [
            'xx\xx\\\\xx\\\\\\\\xx' => ['\\', '/^xxxx\\\\xx\\\\\\\\xx$/'],
            '\%' => ['\\', '/^%$/'],
            '\_' => ['\\', '/^_$/'],
            '#%' => ['#', '/^%$/'],
            '#_' => ['#', '/^_$/'],
            '\\\\%' => ['\\', '/^\\\\.*$/'],
            '\\\\_' => ['\\', '/^\\\\.$/'],
            '##%' => ['#', '/^#.*$/'],
            '##_' => ['#', '/^#.$/'],
        ];
        foreach ($patterns as $pattern => $options) {
            list ($escape, $expected) = $options;
            $this->assertEquals($expected, SqlLikeToRegex::convert($pattern, '/', $escape));
        }
    }
}
