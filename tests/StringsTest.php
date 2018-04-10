<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Tests;

use fab2s\OpinHelpers\Strings;

/**
 * Class StringsTest
 */
class StringsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider filterData
     *
     * @param mixed $input
     * @param mixed $expected
     */
    public function testFilter($input, $expected)
    {
        $this->assertSame($expected, Strings::filter($input));
    }

    /**
     * @dataProvider singleWsIzeData
     *
     * @param mixed $input
     * @param mixed $expected
     */
    public function testSingleWsIze($input, $expected)
    {
        $this->assertSame($expected, Strings::singleWsIze($input));
    }

    /**
     * @dataProvider normalizeWsData
     *
     * @param mixed    $input
     * @param bool     $includeTabs
     * @param int|null $maxConsecutive
     * @param mixed    $expected
     */
    public function testNormalizeWs($input, $includeTabs, $maxConsecutive, $expected)
    {
        $this->assertSame($expected, Strings::normalizeWs($input, $includeTabs, $maxConsecutive));
    }

    /**
     * @dataProvider normalizeEolData
     *
     * @param mixed    $input
     * @param int|null $maxConsecutive
     * @param string   $eol
     * @param mixed    $expected
     */
    public function testNormalizeEol($input, $maxConsecutive, $eol, $expected)
    {
        $this->assertSame($expected, Strings::normalizeEol($input, $maxConsecutive, $eol));
    }

    /**
     * @return array
     */
    public function normalizeEolData()
    {
        return [
            [
                'input'          => "this is\r\r\none text \n\f\nwith" . json_decode('"\u2028"') . json_decode('"\u2029"') . "tons of ws \f\tand LF's \r\nevery" . json_decode('"\u000B"') . 'where',
                'maxConsecutive' => null,
                'eol'            => "\n",
                'expected'       => "this is\n\none text \n\n\nwith\n\ntons of ws \n\tand LF's \nevery\nwhere",
            ],
            [
                'input'          => "this is\r\r\none text \n\f\nwith" . json_decode('"\u2028"') . json_decode('"\u2029"') . "tons of ws \f\tand LF's \r\nevery" . json_decode('"\u000B"') . 'where',
                'maxConsecutive' => 1,
                'eol'            => "\n",
                'expected'       => "this is\none text \nwith\ntons of ws \n\tand LF's \nevery\nwhere",
            ],
            [
                'input'          => "this is\r\r\n\rone text \n\r\n\nwith" . json_decode('"\u2028"') . json_decode('"\u2029"') . "tons of ws \f\tand LF's \r\nevery" . json_decode('"\u000B"') . 'where',
                'maxConsecutive' => 2,
                'eol'            => "\n",
                'expected'       => "this is\n\none text \n\nwith\n\ntons of ws \n\tand LF's \nevery\nwhere",
            ],
        ];
    }

    /**
     * @return array
     */
    public function normalizeWsData()
    {
        return [
            [
                'input'          => "this is                     one   text \nwith " . json_decode('"\u2009"') . json_decode('"\u2009"') . "tons of ws \t\tand LF's \r\neverywhere",
                'includeTabs'    => true,
                'maxConsecutive' => 1,
                'expected'       => "this is one text \nwith tons of ws and LF's \r\neverywhere",
            ],
            [
                'input'          => "this is     one more   text \nwith tons of ws \t\tand LF's \r\neverywhere",
                'includeTabs'    => false,
                'maxConsecutive' => 1,
                'expected'       => "this is one more text \nwith tons of ws \t\tand LF's \r\neverywhere",
            ],
            [
                'input'          => "this is     another   text \nwith " . json_decode('"\u2009"') . json_decode('"\u2009"') . "tons of ws \t\tand LF's \r\neverywhere",
                'includeTabs'    => false,
                'maxConsecutive' => 2,
                'expected'       => "this is  another  text \nwith  tons of ws \t\tand LF's \r\neverywhere",
            ],
        ];
    }

    /**
     * @return array
     */
    public function singleWsIzeData()
    {
        return [
            // input, expected
            [
                'input'    => "this is   a   text \nwith tons of ws \t\tand LF's \r\neverywhere",
                'expected' => "this is a text \nwith tons of ws \tand LF's \r\neverywhere",
            ],
            [
                'input'    => "this is a   text \f\nwith" . json_decode('"\u2009"') . json_decode('"\u2009"') . "tons of ws \t  \tand LF's \r\neverywhere",
                'expected' => "this is a text \f\nwith" . json_decode('"\u2009"') . "tons of ws \t \tand LF's \r\neverywhere",
            ],
        ];
    }

    /**
     * @return array
     */
    public function filterData()
    {
        return [
            // input, expected
            ["this is a \x00text \n\nwith \rws \t\tand LF's \r\neverywh\0ere", "this is a text \n\nwith \nws \t\tand LF's \neverywhere"],
            ["this is another text \0\nwith \r\rws \t\tand LF's \r\neverywhere", "this is another text \nwith \n\nws \t\tand LF's \neverywhere"],
            ['this is yet an' . json_decode('"\uFEFF"') . "other text \0\nwith \n\rws \t\tand LF's \r\nevery" . json_decode('"\u200B"') . 'where !!ù€@à!!', "this is yet another text \nwith \n\nws \t\tand LF's \neverywhere !!ù€@à!!"],
        ];
    }
}
