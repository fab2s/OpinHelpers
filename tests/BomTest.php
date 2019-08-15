<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Tests;

use fab2s\OpinHelpers\Bom;

/**
 * Class BomTest
 */
class BomTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider extractData
     *
     * @param string      $input
     * @param string|null $bom
     * @param string      $encoding
     */
    public function testExtract($input, $bom, $encoding)
    {
        $this->assertSame(Bom::extract($input), $bom, $encoding);
    }

    /**
     * @dataProvider dropData
     *
     * @param string      $input
     * @param string|null $bom
     * @param string      $encoding
     */
    public function testDrop($input, $bom, $encoding)
    {
        $this->assertSame(Bom::drop($input), $bom, $encoding);
    }

    /**
     * @return array
     */
    public function extractData()
    {
        $string = "I am an irrelevant string\n which content does not matter";
        $result = [];

        foreach (Bom::getBoms() as $encoding => $bom) {
            $result = [
                // no BOM case
                [
                    $string,
                    null,
                    $encoding,
                ],
            ];

            $result[] = [
                "$bom$string",
                $bom,
                $encoding,
            ];

            $result[] = [
                "$bom $string",
                $bom,
                $encoding,
            ];

            $result[] = [
                "$bom\n$string",
                $bom,
                $encoding,
            ];

            $result[] = [
                " $bom$string",
                null,
                $encoding,
            ];
        }

        return $result;
    }

    /**
     * @return array
     */
    public function dropData()
    {
        $string = "I am yet another irrelevant string\n which content does not matter";
        $result = [];

        foreach (Bom::getBoms() as $encoding => $bom) {
            $result = [
                // no BOM case
                [
                    $string,
                    $string,
                    $encoding,
                ],
            ];

            $result[] = [
                "$bom$string",
                $string,
                $encoding,
            ];

            $result[] = [
                "$bom $string",
                " $string",
                $encoding,
            ];

            $result[] = [
                "$bom\n$string",
                "\n$string",
                $encoding,
            ];

            $result[] = [
                " $bom$string",
                " $bom$string",
                $encoding,
            ];
        }

        return $result;
    }
}
