# Utf8

A purely static UTF8 Helper based on [mb_string](https://php.net/mb_string) and [ext-intl](https://php.net/intl). It does nto try to be smart and just fails without `mb_string` for all string manipulations.

### Prerequisites

`Utf8` requires [mb_string](https://php.net/mb_string), [ext-intl](https://php.net/intl) is auto detected and used when available for UTF8 Normalization. 

### Methods

Signature | Description
------------ | -------------
`strrpos(string string $str, string $needle, int $offset = null):int/false` | UTF8 aware [strrpos()](https://php.net/strrpos) replacement
`strpos(string $str, string $needle, int $offset = 0):int/false` | UTF8 aware [strpos()](https://php.net/strpos) replacement
`strtolower(string $str):string` | UTF8 aware [strtolower()](https://php.net/strtolower) replacement
`strtoupper(string $str):string` | UTF8 aware [strtoupper()](https://php.net/strtoupper) replacement
`substr(string $str, int $offset, int $length = null):string` | UTF8 aware [substr()](https://php.net/substr) replacement
`strlen(string $str):int` | UTF8 aware [strlen()](https://php.net/strlen) replacement
`ucfirst(string $str):string` | UTF8 aware [ucfirst()](https://php.net/ucfirst) replacement
`ucwords(string $str):string` | UTF8 aware [ucwords()](https://php.net/ucwords) replacement
`ord(string $chr):int/null` | UTF8 aware [ord()](https://php.net/ord) replacement
`chr(int $num):string` | UTF8 aware [chr()](https://php.net/chr) replacement
`normalize(string $string, int $canonicalForm = self::NORMALIZE_NFC):string` | UTF8 [ext-intl](https://php.net/intl) [Normalizer](https://php.net/normalizer.normalize)
`hasUtf8(string $string):bool` | Tells if the input string contains some UTF8
`isUtf8(string $string):bool` | Tells if the input string is valid UTF8
`replaceMb4(string $string, string $replace = ''):string` | Replaces all [Utf8Mb4](https://stackoverflow.com/a/30074553/7630496) characters (aka mostly [emoji](https://en.wikipedia.org/wiki/Emoji))
`normalizerSupport(bool $disable = false):bool` | Tells if [Normalizer](https://php.net/normalizer.normalize) is available or disable Normalizer support
