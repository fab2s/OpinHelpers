# OpinHelpers

[![Build Status](https://travis-ci.org/fab2s/OpinHelpers.svg?branch=master)](https://travis-ci.org/fab2s/OpinHelpers) [![Total Downloads](https://poser.pugx.org/fab2s/opinhelpers/downloads)](https://packagist.org/packages/fab2s/opinhelpers) [![Monthly Downloads](https://poser.pugx.org/fab2s/opinhelpers/d/monthly)](https://packagist.org/packages/fab2s/opinhelpers) [![Latest Stable Version](https://poser.pugx.org/fab2s/opinhelpers/v/stable)](https://packagist.org/packages/fab2s/opinhelpers) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/fab2s/OpinHelpers/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/fab2s/OpinHelpers/?branch=master) [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com) [![License](https://poser.pugx.org/fab2s/opinhelpers/license)](https://packagist.org/packages/fab2s/opinhelpers)

`OpinHelpers` is a bellow "Swiss Army Knife" level set of opinionated Helper libs (hence the [opin[h]el](https://en.wikipedia.org/wiki/Opinel_knife)^^) covering some of the most annoying aspects of php programing, such as UTF8 string manipulation, arbitrary precision Mathematics or properly locking a file.

`OpinHelpers` libs are opinionated in several ways and do not aim at being universal, but they should hopefully be pretty useful in many IRL cases.

## Installation

`OpinHelpers` can be installed using composer:

```
composer require "fab2s/opinhelpers"
```

If you want to specifically install the php >=7.1.0 version, use:

```
composer require "fab2s/opinhelpers" ^1
```

If you want to specifically install the php 5.6/7.0 version, use:

```
composer require "fab2s/opinhelpers" ^0
```

There are mostly ([see Compatibility](#compatibility)) no changes other than further typing from 0.x to 1.x

## Documentation

`OpinHelpers` is just requiring individual libraries that each has its own repository and documentation

- [Math](https://github.com/fab2s/Math): High precision base10 fluent helper with a rather strict approach
- [Utf8](https://github.com/fab2s/Utf8): Purely static UTF8 Helper
- [Strings](docs/https://github.com/fab2s/Strings): Purely static String Helper to handle more advanced utf8 string manipulations
- [Bom](https://github.com/fab2s/Bom): Purely static zero dependencies BOM Helper to handle unicode BOMs
- [FileLock](https://github.com/fab2s/FileLock): fluent file locking _helper_

## Compatibility

`OpinHelpers` comes with a `deprecated.php` file you can `require` in your project should you need to keep using the old `namespace` from before **v1**

```php

require 'vendor/fab2s/opinHelpers/src/deprecated.php';

// no you can 
use fab2s\OpinHelpers\Math;

// same as 
use fab2s\Math\Math;

// old version will be marked as deprecated 
$number = fab2s\OpinHelpers\Math::numder('42');

// new one is ok with full type hints
$number = fab2s\Math\Math::numder('42');

```

> No notable changes where made to the methods names or signature, but there was some small return types changes (false vs null in `Utf8`), one edge value (`Utf8::chr(0) = "\0"`) and one bug with `UTF-32-LE` BOM detection. It should be ok in most of the cases but it is still preferable to refactor to new `namespace` and review the usage

## Requirements

`OpinHelpers` is tested against php 7.1, 7.2 and 7.3.

## Contributing

Contributions are welcome, do not hesitate to open issues and submit pull requests.

## License

Each of `OpinHelpers` components are open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT)