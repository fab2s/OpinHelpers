# OpinHelpers

OpinHelpers is a bellow "Swiss Army Knife" level set of opinionated Helper libs (hence the [opin[h]el](https://en.wikipedia.org/wiki/Opinel_knife)^^) covering some of the most annoying aspects of php programing, such as UTF8 string manipulation, arbitrary precision Mathematics or properly locking a file.

OpinHelpers libs are opinionated in several ways and do not aim at being universal, but they should hopefully be pretty useful in many IRL cases.

## Installing

OpinHelpers can be installed using composer :

```
composer require "fab2s/opinhelpers"
```

## `Utf8`

A purely static UTF8 Helper based on [mb_string](http://php.net/mb_string) and [ext-intl](http://php.net/intl). It does nto try to be smart and just fails without `mb_string` for all string manipulations.

### Prerequisites

`Utf8` requires [mb_string](http://php.net/mb_string), [ext-intl](http://php.net/intl) is auto detected and used when available for UTF8 Normalization. 

### Methods

Signature | Description
------------ | -------------
`strrpos(string string $str, string $needle, int $offset = null):int|false` | UTF8 aware [strrpos()](http://php.net/strrpos) replacement
`strpos(string $str, string $needle, int $offset = 0):int|false` | UTF8 aware [strpos()](http://php.net/strpos) replacement
`strtolower(string $str):string` | UTF8 aware [strtolower()](http://php.net/strtolower) replacement
`strtoupper(string $str):string` | UTF8 aware [strtoupper()](http://php.net/strtoupper) replacement
`substr(string $str, int $offset, int $length = null):string` | UTF8 aware [substr()](http://php.net/substr) replacement
`strlen(string $str):int` | UTF8 aware [strlen()](http://php.net/strlen) replacement
`ucfirst(string $str):string` | UTF8 aware [ucfirst()](http://php.net/ucfirst) replacement
`ucwords(string $str):string` | UTF8 aware [ucwords()](http://php.net/ucwords) replacement
`ord(string $chr):int|null` | UTF8 aware [ord()](http://php.net/ord) replacement
`chr(int $num):string` | UTF8 aware [chr()](http://php.net/chr) replacement
`normalize(string $string, int $canonicalForm = Normalizer::NFC):string` | UTF8 [ext-intl](http://php.net/intl) [Normalizer](http://php.net/normalizer.normalize)
`hasUtf8(string $string):bool` | Tells if the input string contains some UTF8
`isUtf8(string $string):bool` | Tells if the input string is valid UTF8
`replaceMb4(string $string, string $replace = ''):string` | Replaces all [Utf8Mb4](https://stackoverflow.com/a/30074553/7630496) characters (aka mostly [emoji](https://en.wikipedia.org/wiki/Emoji))
`normalizerSupport(bool $disable = false):bool` | Tells if [Normalizer](http://php.net/normalizer.normalize) is available or disable Normalizer support

## `Bom`

A purely static zero dependencies BOM Helper to handle unicode BOMs.

### Methods

Signature | Description
------------ | -------------
`extract(string $string):string|null` | Lookup for Bom at beginning of $string
`drop(string $string):string` | Drop eventual Bom at beginning of $string
`getBomEncoding(string $bom):string|null` | Translate $bom to encoding
`getEncodingBom(string $encoding):string|null` | Translate $encoding to bom

## `Strings`

A purely static String Helper to handle more advanced utf8 string manipulations.

### Prerequisites

Just like `Utf8`, `Strings` requires [mb_string](http://php.net/mb_string), [ext-intl](http://php.net/intl) is auto detected and used when available for UTF8 Normalization. 

### Methods

Signature | Description
------------ | -------------
`filter(string $string):string` | Drops Zero Width white chars, normalizes EOL and Normalize UTF8 if [ext-intl](http://php.net/intl) is available
`singleWsIze(string $string, bool $normalize = false, bool $includeTabs = true):string` | Replace repeated white-spaces to a single one, preserve original white-spaces unless normalized (every white-spaces to ' '), with or without tabs (\t)
`singleLineIze(string $string):string` | Make string fit in one line by replacing EOLs and white-spaces to normalized single white-spaces
`dropZwWs(string $string):string` | Remove Zero Width white-spaces 
`normalizeWs(string $string, bool $includeTabs = true, int $maxConsecutive = null):string` | Normalize white-spaces to a single ' ' by default, include tabs by default
`normalizeEol($string, $maxConsecutive = null, $eol = null):string` | Normalize EOLs to a single LF by default
`normalizeText(string $text):string` | Return `trim`'d and `filter`'d $text 
`normalizeTitle(string $title):string` | Return `singleLineIze`'d and `normalizeText`'d $title
`normalizeName(string $name):string` | Return `ucword`'d and `normalizeTitle`'d $name (`"john \n\t doe  "` -> `"John Doe"`) 
`escape(string $string, int $flag = ENT_COMPAT, bool $hardEscape = true):string` | [htmlspecialchars()](http://php.net/htmlspecialchars) wrapper with UTF8 set as encoding
`escape(string $string, int $flag = ENT_COMPAT, bool $hardEscape = true):string` | [htmlspecialchars()](http://php.net/htmlspecialchars) wrapper with UTF8 set as encoding
`softEscape(string $string, int $flag = ENT_COMPAT):string` | Shortcut for `escape(string $string, $flag, true)`
`unEscape(string $string, int $quoteStyle = ENT_COMPAT):string` | [htmlspecialchars_decode()](http://php.net/htmlspecialchars_decode) wrapper 
`convert(string $string, string $from = null, string $to = self::ENCODING):string` | Convert encoding to UTF8 by default. Basic $from encoding detection using `Strings::detectEncoding()`
`detectEncoding(string $string):string|null` | Detect encoding by checking `Utf8::isUf8()`, then trying with BOMs and ultimately fall back to [mb_detect_encoding()](http://php.net/mb_detect_encoding) with limited charsets first, then more internally in [mb_convert_encoding()](http://php.net/mb_convert_encoding) 
`secureCompare(string $test, string $reference):bool` | Perform a [Timing Attack](https://en.wikipedia.org/wiki/Timing_attack) safe string comparison (Truly constant operations comparison)
`contentHash(string $content):string` | Return a `sha256` hash of the $content prefixed with $content length. Indented to quickly and reliably detect $content updates.

White-spaces is a not so trivial matter, `Strings` defines to classes of white-spaces :
- Zero width white-spaces: 

```php
     /**
     * U+200B zero width space
     * U+FEFF zero width no-break space
     */
    const ZERO_WIDTH_WS_CLASS = '\x{200B}\x{FEFF}';
```

- Non standard white-spaces: 

```php
     /**
     * U+00A0  no-break space
     * U+2000  en quad
     * U+2001  em quad
     * U+2002  en space
     * U+2003  em space
     * U+2004  three-per-em space
     * U+2005  four-per-em space
     * U+2006  six-per-em space
     * U+2007  figure space
     * U+2008  punctuation space
     * U+2009  thin space
     * U+200A  hair space
     * U+202F  narrow no-break space
     * U+3000  ideographic space
     */
    const NON_STANDARD_WS_CLASS = '\x{00A0}\x{2000}-\x{200A}\x{202F}\x{3000}';
```

Zero width white-spaces do not include Joiners because the idea is to remove text formatting, not to transform input text.
Non standard white-spaces are also pretty specific to just match actual white-spaces and nothing more when removing / normalizing white-spaces.

## `Math`

A fluent [bcmath](http://php.net/bcmath) based _Helper_ to handle arbitrary precision calculus with a rather strict approach (want precision for something right?). It does not try to be smart and just fails without `bcmath`, but it does auto detect [GMP](http://php.net/GMP) for faster base conversions.

### Prerequisites

`Math` requires [bcmath](http://php.net/bcmath), [GMP](http://php.net/GMP) is auto detected and used when available for faster base conversions (up to 62). 

### In practice

As `Math` is meant to be used where precision matters, it is pretty strict with input numbers : it will throw an exception whenever an input number does not match `^([+-]{1})?([0-9]+(\.[0-9]+)?|\.[0-9]+)$` after passing though `trim()`.

In practice this means that "-.0051" and "00028.34" are ok, but "1E12" or "1.1.1" will throw an exception. This is done so because in `bcmath` world, "1E12", "1.1.1" and "abc" are all "0", which could result in some disaster in you where to do nothing.

A `Math` instance is just initialized with a valid number. From there you can do the math and just cast the instance as string to get current result. 

```php
// instance way
$number = new Math('42');

// fluent grammar
$result = (string) $number->add('1')->sub(2)->div(1)->add(1)->mul(-1); // '42'

// factory way: number
$result = (string) Math::number('42')->add('1')->sub(2)->div('1')->add(1)->mul(-1); // '42'

// factory way: fromBase
$result = (string) Math::fromBase('LZ', 62); // '1337'
$result = (string) Math::fromBase('LZ', 62)->sub(1295); // '42'

// combos
$number = Math::number('42')
    ->add(Math::fromBase('LZ', 62), '-42')
    ->sub('1337', '42')
    ->mul(3, 4, 1)
    ->div(4, 3)
    ->sub('.1')
    ->abs()
    ->round(0)
    ->floor()
    ->ceil()
    ->min('512', '256')
    ->max('8', '16', '32');

// formatting does not mutate internal number
$result = (string) $number->format(2); // '42.00'
$result = (string) $number; '42';
$result = (string) $number->add('1295')->toBase(62); 'LZ';

// toBase does not mutate base 10 internal representation
$result = (string) $number; '1337';
```

The string form of any such calculus is normalized (things like '-0', '+.0' or '0.00' to '0'), which means that you can accurately compare `Math` instances results:

```php
$result = (string) Math::number('0000042.000000'); // '42'

// raw form
$result = Math::number('0000042.000000')->getNumber(); // '0000042.000000'

// with some tolerance
$result = Math::number('  42.0000 ')->getNumber(); // '42.0000'

// at all time
if ((string) $number1 === (string) $number2) {
    // both instance number are equals
}

// same as
if ($number1->eq($number2)) {
    // both instance numbers are equals
}
```

Since `__toString()` is implemented, you can transparently re-use partial $calculus directly as instance when calculating:

```php
$number = (new Math('42'));
// same as
$number = Math::number('42');

// in constructor
$result = (string) (new Math($number))->div('1337'); // '42'
// same as
$result = (string) Math::number($number)->mul('1337'); // '42'

// in calc method
$result = (string) Math::number('42')->add(number)->sub('42')->div('1337'); // '42'
```

Doing so is actually faster than casting a pre-existing instance to string because it does not trigger a normalization (internal number state is only normalized when exporting result) nor a number validation, as internal $number is already valid at all times.

Arguments should be string or `Math`, but it is ok to use integers up to `INT_(32|64)`. 

DO NOT use `floats` as casting them to `string` may result in local dependent format, such as using a coma instead of a dot for decimals. 
The way floats are handled is also the reason why `bcmath` exists, so even if you trust your locale settings, using floats kinda defeats the purpose of using such lib.

### Internal precision

Precision handling does not rely on [bcscale](http://php.net/bcscale) as it is not so reliable IRL. As it is a global setup, it may affect or be affected by far away/unrelated code (with fpm it can actually spread to all PHP processes).

`Math` handle precisions at both instance and global (limited to the current PHP process) precision. The global precision is stored in a static instance, when set, each new instance will start with this global precision as precision (you can still set the instance precision after instantiation). When no global precision is set, initial instance precision defaults to `Math::PRECISION` (currently 9, or 9 digit after the dot)

```php
// set global precision
Math::setGlobalPrecision(18);

$number = (new Math('100'))->div('3'); // uses precision 18
$number->setPrecision(14); // will use precision 14 for any further calculations
```

