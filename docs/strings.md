# Strings

A purely static String Helper to handle more advanced utf8 string manipulations.

## Prerequisites

Just like `Utf8`, `Strings` requires [mb_string](https://php.net/mb_string), [ext-intl](https://php.net/intl) is auto detected and used when available for UTF8 Normalization. 

## Methods

Signature | Description
------------ | -------------
`filter(string $string):string` | Drops Zero Width white chars, normalizes EOL and Normalize UTF8 if [ext-intl](https://php.net/intl) is available
`singleWsIze(string $string, bool $normalize = false, bool $includeTabs = true):string` | Replace repeated white-spaces to a single one, preserve original white-spaces unless normalized (every white-spaces to ' '), with or without tabs (\t)
`singleLineIze(string $string):string` | Make string fit in one line by replacing EOLs and white-spaces to normalized single white-spaces
`dropZwWs(string $string):string` | Remove Zero Width white-spaces 
`normalizeWs(string $string, bool $includeTabs = true, int $maxConsecutive = null):string` | Normalize white-spaces to a single ' ' by default, include tabs by default
`normalizeEol($string, $maxConsecutive = null, $eol = null):string` | Normalize EOLs to a single LF by default
`normalizeText(string $text):string` | Return `trim`'d and `filter`'d $text 
`normalizeTitle(string $title):string` | Return `singleLineIze`'d and `normalizeText`'d $title
`normalizeName(string $name):string` | Return `ucword`'d and `normalizeTitle`'d $name (`"john \n\t doe  "` -> `"John Doe"`) 
`escape(string $string, int $flag = ENT_COMPAT, bool $hardEscape = true):string` | [htmlspecialchars()](https://php.net/htmlspecialchars) wrapper with UTF8 set as encoding
`escape(string $string, int $flag = ENT_COMPAT, bool $hardEscape = true):string` | [htmlspecialchars()](https://php.net/htmlspecialchars) wrapper with UTF8 set as encoding
`softEscape(string $string, int $flag = ENT_COMPAT):string` | Shortcut for `escape(string $string, $flag, true)`
`unEscape(string $string, int $quoteStyle = ENT_COMPAT):string` | [htmlspecialchars_decode()](https://php.net/htmlspecialchars_decode) wrapper 
`convert(string $string, string $from = null, string $to = self::ENCODING):string` | Convert encoding to UTF8 by default. Basic $from encoding detection using `Strings::detectEncoding()`
`detectEncoding(string $string):string/null` | Detect encoding by checking `Utf8::isUf8()`, then trying with BOMs and ultimately fall back to [mb_detect_encoding()](https://php.net/mb_detect_encoding) with limited charsets first, then more internally in [mb_convert_encoding()](https://php.net/mb_convert_encoding) 
`secureCompare(string $test, string $reference):bool` | Perform a [Timing Attack](https://en.wikipedia.org/wiki/Timing_attack) safe string comparison (Truly constant operations comparison)
`contentHash(string $content):string` | Return a `sha256` hash of the $content prefixed with $content length. Indented to quickly and reliably detect $content updates.

## White-spaces

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
