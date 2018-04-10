<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Tests;

use fab2s\OpinHelpers\Utf8;

class Utf8Test extends \PHPUnit_Framework_TestCase
{
    public function replaceMb4Data()
    {
        return [
            // input, expected
            ['this is a test with an emoji!! 😘 ohoh', 'this is a test with an emoji!!  ohoh'],
            ['this is a test with many 🍵🍶 emoji!! 🍷🍸 ahah', 'this is a test with many  emoji!!  ahah'],
        ];
    }

    /**
     * @dataProvider replaceMb4Data
     *
     * @param mixed $input
     * @param mixed $expected
     */
    public function testReplaceMb4($input, $expected)
    {
        $this->assertSame($expected, Utf8::replaceMb4($input));
    }
}
