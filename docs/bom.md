# Bom

A purely static zero dependencies [BOM](https://en.wikipedia.org/wiki/Byte_order_mark) Helper to handle unicode BOMs.

## Methods

Signature | Description
------------ | -------------
`extract(string $string):string/null` | Lookup for Bom at beginning of $string
`drop(string $string):string` | Drop eventual Bom at beginning of $string
`getBomEncoding(string $bom):string/null` | Translate $bom to encoding
`getEncodingBom(string $encoding):string/null` | Translate $encoding to bom
