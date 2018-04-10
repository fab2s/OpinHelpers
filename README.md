# OpinHelpers

[![Build Status](https://travis-ci.org/fab2s/OpinHelpers.svg?branch=master)](https://travis-ci.org/fab2s/OpinHelpers) [![Latest Stable Version](https://poser.pugx.org/fab2s/opinhelpers/v/stable)](https://packagist.org/packages/fab2s/opinhelpers) [![PRs Welcome](https://img.shields.io/badge/PRs-welcome-brightgreen.svg?style=flat)](http://makeapullrequest.com) [![License](https://poser.pugx.org/fab2s/opinhelpers/license)](https://packagist.org/packages/fab2s/opinhelpers)

OpinHelpers is a bellow "Swiss Army Knife" level set of opinionated Helper libs (hence the [opin[h]el](https://en.wikipedia.org/wiki/Opinel_knife)^^) covering some of the most annoying aspects of php programing, such as UTF8 string manipulation, arbitrary precision Mathematics or properly locking a file.

OpinHelpers libs are opinionated in several ways and do not aim at being universal, but they should hopefully be pretty useful in many IRL cases.

## Installation

OpinHelpers can be installed using composer :

```
composer require "fab2s/opinhelpers"
```

## Citizens

- [Math](docs/math.md): Arbitrary precision fluent helper with a rather strict approach
- [Utf8](docs/utf8.md): Purely static UTF8 Helper
- [Strings](docs/strings.md): Purely static String Helper to handle more advanced utf8 string manipulations
- [Bom](docs/bom.md): Purely static zero dependencies BOM Helper to handle unicode BOMs

## Requirements

OpinHelpers is tested against php 5.6, 7.0, 7.1, 7.2 and hhvm.

## Contributing

Contributions are welcome, do not hesitate to open issues and submit pull requests.

## License

SoUuid is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).