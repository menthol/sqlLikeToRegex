# Sql Like To Regex
Simple package to convert (my)sql LIKE syntax to regex (preg)

## Install
```bash
TODO: composer require menthol/sql-like-to-regex
```

## Usage

```php
use Menthol\SqlLikeToRegex\SqlLikeToRegex;

print SqlLikeToRegex::convert('foo%');
// => /^foo.*$/

print SqlLikeToRegex::convert('_b_a_r_', '#');
// => #^.b.a.r.$#

print SqlLikeToRegex::convert('/.*[baz]{5}^/', '<');
// => <^/\.\*\[baz\]\{5\}\^/$>
```

### License

This project is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

