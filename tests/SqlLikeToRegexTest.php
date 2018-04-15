<?php

namespace Menthol\SqlLikeToRegexTests;

use Menthol\SqlLikeToRegex\SqlLikeToRegex;
use PHPUnit\Framework\TestCase;

class SqlLikeToRegexTest extends TestCase
{
    /** @var SqlLikeToRegex */
    private $instance;

    public function setUp()
    {
        $this->instance = (new SqlLikeToRegex())->setCaseSensitive();
    }

    /** @test */
    public function it_should_test_a_subject()
    {
        $this->assertTrue($this->instance->setPattern('te_t')->test('test'));
        $this->assertFalse($this->instance->setPattern('te_t')->test('bar'));
    }

    /** @test */
    public function it_do_not_touch_pattern_without_special_chars()
    {
        $patterns = ['foo', 'bar', 123, '', true, null];
        foreach ($patterns as $pattern) {
            $this->assertEquals("/^{$pattern}$/", $this->instance->setPattern($pattern)->getRegex());
        }
    }

    /** @test */
    public function it_should_add_case_sensitive_option_to_pattern()
    {
        $this->instance->setCaseInsensitive();
        $patterns = ['foo', 'bar', 123, '', true, null];
        foreach ($patterns as $pattern) {
            $this->assertEquals("/^{$pattern}$/i", $this->instance->setPattern($pattern)->getRegex());
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
            $this->assertEquals($expected, $this->instance->setPattern($pattern)->getRegex());
        }

        $patterns = [
            '+delimiters+' => '+^\+delimiters\+$+',
            '+delimiters with option+s' => '+^\+delimiters with option\+s$+',
        ];
        $this->instance->setDelimiter('+');
        foreach ($patterns as $pattern => $expected) {
            $this->assertEquals($expected, $this->instance->setPattern($pattern)->getRegex());
        }

        $patterns = [
            '(this [is] a (pattern))' => ['(', '(^\(this \[is\] a \(pattern\)\)$)'],
            '{this [is] a (pattern)}' => ['{', '{^\{this \[is\] a \(pattern\)\}$}'],
            '[this [is] a (pattern)]' => ['[', '[^\[this \[is\] a \(pattern\)\]$]'],
            '<this [is] a (pattern)>' => ['<', '<^\<this \[is\] a \(pattern\)\>$>'],
        ];
        foreach ($patterns as $pattern => $options) {
            list ($delimiter, $expected) = $options;
            $this->instance->setDelimiter($delimiter);
            $this->assertEquals($expected, $this->instance->setPattern($pattern)->getRegex());
        }
    }

    /** @test */
    public function it_should_protect_meta_characters()
    {
        $converted = $this->instance->setPattern('\^$.[]|()?*+{}')->getRegex();
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
            $this->assertEquals($expected, $this->instance->setPattern($pattern)->getRegex());
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
            $this->instance->setEscape($escape);
            $this->assertEquals($expected, $this->instance->setPattern($pattern)->getRegex());
        }
    }
}
