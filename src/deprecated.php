<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace {
    class_alias(\fab2s\Math\Math::class, \fab2s\OpinHelpers\Math::class);
    class_alias(\fab2s\FileLock\FileLock::class, \fab2s\OpinHelpers\FileLock::class);
    class_alias(\fab2s\Bom\Bom::class, \fab2s\OpinHelpers\Bom::class);
    class_alias(\fab2s\Utf8\Utf8::class, \fab2s\OpinHelpers\Utf8::class);
    class_alias(\fab2s\Strings\Strings::class, \fab2s\OpinHelpers\Strings::class);
}

namespace fab2s\OpinHelpers {
    if (!class_exists(Math::class)) {
        /** @deprecated Math this is intended to help IDEs */
        class Math
        {
        }
    }

    if (!class_exists(FileLock::class)) {
        /** @deprecated FileLock this is intended to help IDEs */
        class FileLock
        {
        }
    }

    if (!class_exists(Bom::class)) {
        /** @deprecated Bom this is intended to help IDEs */
        class Bom
        {
        }
    }

    if (!class_exists(Utf8::class)) {
        /** @deprecated Utf8 this is intended to help IDEs */
        class Utf8
        {
        }
    }

    if (!class_exists(Strings::class)) {
        /** @deprecated Strings this is intended to help IDEs */
        class Strings
        {
        }
    }
}
