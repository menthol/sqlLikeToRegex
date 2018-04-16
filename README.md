# Sql Like To Regex
Simple package to convert (my)sql LIKE syntax to regex (preg)

[![Build Status](https://travis-ci.org/menthol/sqlLikeToRegex.svg?branch=master)](https://travis-ci.org/menthol/sqlLikeToRegex)

## Install
```bash
composer require menthol/sql-like-to-regex
```

## Usage

```php
use Menthol\SqlLikeToRegex\SqlLikeToRegex;

print (new SqlLikeToRegex)
        ->setPattern('foo%')
        ->toRegex();
// => /^foo.*$/i

var_dump((new SqlLikeToRegex)
    ->setPattern('foo%')
    ->test('FooBar')
);
// => bool(true)

var_dump((new SqlLikeToRegex)
    ->setPattern('foo%')
    ->test('Baz')
);
// => bool(false)

print (new SqlLikeToRegex)
        ->setPattern('B_o#(F%o##Moo#%')
        ->setEscape('#')
        ->setCaseSensitive()
        ->toRegex();
// => /^B.o\(F.*o#Moo%$/

print (new SqlLikeToRegex)
        ->setPattern('_b_a_r_')
        ->setDelimiter('#')
        ->toRegex();
// => #^.b.a.r.$#i

print (new SqlLikeToRegex)
        ->setPattern('/.*[baz]{5}^/')
        ->setDelimiter('<')
        ->toRegex();
// => <^/\.\*\[baz\]\{5\}\^/$>i
```

### License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

