<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Tests;

use fab2s\OpinHelpers\Math;

/**
 * Class MathTest
 */
class MathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @return array
     */
    public function number_formatData()
    {
        return [
            // number, expected [, precision[, dec_point[, thousands_sep]]]
            ['255173029255255255', '255 173 029 255 255 255'],
            ['255173029255255255.98797', '255 173 029 255 255 256'],
            ['255173029255255255.98797', '255 173 029 255 255 255.98797', 5],
            ['255173029255255255.98797', '255 173 029 255 255 255.988', 3],
            ['-255173029255255255.98797', '-255 173 029 255 255 255.988', 3],
            ['-255173029255255255.98797', '-255 173 029 255 255 255.98797000', 8],
            ['-255173029255255255.98797', '-255 173 029 255 255 255,98797000', 8, ','],
            ['-255173029255255255.98797', '-255,173,029,255,255,255.98797000', 8, '.', ','],
            ['0.000000001', '0.00000000', 8],
            ['0.000000001', '0.000000001', 9],
            ['-0', '0.00000000', 8],
            ['+0', '0.00000000', 8],
            ['0', '0'],
        ];
    }

    /**
     * @dataProvider number_formatData
     *
     * @param string $number
     * @param bool   $expected
     * @param int    $decimals
     * @param string $dec_point
     * @param string $thousands_sep
     */
    public function testNumber_format($number, $expected, $decimals = 0, $dec_point = '.', $thousands_sep = ' ')
    {
        $this->assertSame($expected, (string) Math::number($number)->format($decimals, $dec_point, $thousands_sep));
    }

    /**
     * @return array
     */
    public function compData()
    {
        return [
            [
                'left'    => '255173029255255255',
                'operator'=> '<',
                'right'   => '255173',
                'expected'=> false,
            ],
            [
                'left'     => '255173029255255255',
                'operator' => '>',
                'right'    => '255173',
                'expected' => true,
            ],
            [
                'left'     => '255173029255255255',
                'operator' => '=',
                'right'    => '255173',
                'expected' => false,
            ],
            [
                'left'     => '255173029255255255',
                'operator' => '=',
                'right'    => '255173029255255255',
                'expected' => true,
            ],
            [
                'left'     => '255173029255255255.' . str_repeat('0', Math::PRECISION + 1) . '1',
                'operator' => '>',
                'right'    => '255173029255255255.00',
                'expected' => false,
            ],
            [
                'left'     => '255173029255255255.' . str_repeat('0', Math::PRECISION - 1) . '2',
                'operator' => '>',
                'right'    => '255173029255255255.00' . str_repeat('0', Math::PRECISION - 1) . '1',
                'expected' => true,
            ],
            [
                'left'    => '54',
                'operator'=> '<=',
                'right'   => '0',
                'expected'=> false,
            ],
            [
                'left'     => '54',
                'operator' => '<=',
                'right'    => '-0',
                'expected' => false,
            ],
            [
                'left'     => '54',
                'operator' => '<=',
                'right'    => '-32',
                'expected' => false,
            ],
            [
                'left'     => '-23',
                'operator' => '<=',
                'right'    => '-32',
                'expected' => false,
            ],
            [
                'left'     => '-23',
                'operator' => '<=',
                'right'    => '22',
                'expected' => true,
            ],
            [
                'left'     => '0',
                'operator' => '<',
                'right'    => '0',
                'expected' => false,
            ],
            [
                'left'     => '+0',
                'operator' => '>',
                'right'    => '-0',
                'expected' => false,
            ],
            [
                'left'     => '-0',
                'operator' => '>',
                'right'    => '0',
                'expected' => false,
            ],
            [
                'left'     => '-0',
                'operator' => '=',
                'right'    => '0',
                'expected' => true,
            ],
            [
                'left'     => '0000042.420000',
                'operator' => '=',
                'right'    => '42.42',
                'expected' => true,
            ],
            [
                'left'     => '0000042.420000',
                'operator' => '!=',
                'right'    => '42.42',
                'expected' => false,
            ],
            [
                'left'     => '-42.420000',
                'operator' => '!=',
                'right'    => Math::number('-00042.4200'),
                'expected' => false,
            ],
        ];
    }

    /**
     * @dataProvider compData
     *
     * @param mixed $left
     * @param mixed $operator
     * @param mixed $right
     * @param mixed $expected
     */
    public function testComp($left, $operator, $right, $expected)
    {
        switch ($operator) {
            case '<':
                $this->assertSame(
                    $expected,
                    Math::number($left)->lt($right)
                );
                break;
            case '<=':
                $this->assertSame(
                    $expected,
                    Math::number($left)->lte($right)
                );
                break;
            case '>':
                $this->assertSame(
                    $expected,
                    Math::number($left)->gt($right)
                );
                break;
            case '>=':
                $this->assertSame(
                    $expected,
                    Math::number($left)->gte($right)
                );
                break;
            case '=':
                $this->assertSame(
                    $expected,
                    Math::number($left)->eq($right)
                );
                break;
            case '!=':
                $this->assertSame(
                    $expected,
                    !Math::number($left)->eq($right)
                );
                break;
        }
    }

    /**
     * @return array
     */
    public function roundData()
    {
        return [
            [
                'number'    => '255173029255255255.98797',
                'precision' => 0,
                'expected'  => '255173029255255256',
            ],
            [
                'number'    => '255173029255255255.98797',
                'precision' => 2,
                'expected'  => '255173029255255255.99',
            ],
            [
                'number'    => '1000',
                'precision' => 0,
                'expected'  => '1000',
            ],
            [
                'number'    => '1000.000',
                'precision' => 0,
                'expected'  => '1000',
            ],
            [
                'number'    => '54',
                'precision' => 0,
                'expected'  => '54',
            ],
            [
                'number'    => '54.001',
                'precision' => 0,
                'expected'  => '54',
            ],
            [
                'number'    => '54.99',
                'precision' => 0,
                'expected'  => '55',
            ],
            [
                'number'    => '54.99',
                'precision' => 1,
                'expected'  => '55',
            ],
            [
                'number'    => '54.5',
                'precision' => 1,
                'expected'  => '54.5',
            ],
            [
                'number'    => '54.55',
                'precision' => 1,
                'expected'  => '54.6',
            ],
            [
                'number'    => '-3.4',
                'precision' => 1,
                'expected'  => '-3.4',
            ],
            [
                'number'    => '-3.6',
                'precision' => 1,
                'expected'  => '-3.6',
            ],
            [
                'number'    => '-3.6',
                'precision' => 2,
                'expected'  => '-3.6',
            ],
            [
                'number'    => '-3.6',
                'precision' => 0,
                'expected'  => '-4',
            ],
        ];
    }

    /**
     * @dataProvider roundData
     *
     * @param mixed $number
     * @param mixed $precision
     * @param mixed $expected
     */
    public function testRound($number, $precision, $expected)
    {
        $this->assertSame($expected, (string) Math::number($number)->round($precision));
    }

    /**
     * @return array
     */
    public function maxMinData()
    {
        return [
            [
                'param' => ['54', '32', '23', '0', '255173029255255255', '255173029255255256', '.0'],
                'min'   => '0',
                'max'   => '255173029255255256',
            ],
            [
                'param' => ['54', '32', '23', '0', '255173029255255255'],
                'min'   => '0',
                'max'   => '255173029255255255',
            ],
            [
                'param' => ['54', '32', '23', '0'],
                'min'   => '0',
                'max'   => '54',
            ],
            [
                'param' => ['54', '-32', '23', '0'],
                'min'   => '-32',
                'max'   => '54',
            ],
            [
                'param' => ['-54', '-32', '-23', '0'],
                'min'   => '-54',
                'max'   => '0',
            ],
            [
                'param' => ['-54', '-32', '-23', '-0'],
                'min'   => '-54',
                'max'   => '0',
            ],
            [
                'param' => ['53.28', '52.65', '53.27', '52.64'],
                'min'   => '52.64',
                'max'   => '53.28',
            ],
        ];
    }

    /**
     * @dataProvider maxMinData
     *
     * @param array  $param
     * @param string $min
     * @param string $max
     */
    public function testMax(array $param, $min, $max)
    {
        $first = $param[0];
        unset($param[0]);
        $this->assertSame(
            $max,
            (string) Math::number($first)->max(...$param)
        );
    }

    /**
     * @dataProvider maxMinData
     *
     * @param array  $param
     * @param string $min
     * @param string $max
     */
    public function testMin(array $param, $min, $max)
    {
        $first = $param[0];
        unset($param[0]);
        $this->assertSame(
            $min,
            (string) Math::number($first)->min(...$param)
        );
    }

    /**
     * @return array
     */
    public function normalizeData()
    {
        return [
            [
                'number'   => '000255173029255255255.00000005',
                'expected' => '255173029255255255.00000005',
            ],
            [
                'number'   => '000255173029255255255.000',
                'expected' => '255173029255255255',
            ],
            [
                'number'   => '255173029255255255',
                'expected' => '255173029255255255',
            ],
            [
                'number'   => '1000',
                'expected' => '1000',
            ],
            [
                'number'   => '.000',
                'expected' => '0',
            ],
            [
                'number'   => '-.000',
                'expected' => '0',
            ],
            [
                'number'   => '+.000',
                'expected' => '0',
            ],
            [
                'number'   => '-000.000',
                'expected' => '0',
            ],
            [
                'number'   => '+000.000',
                'expected' => '0',
            ],
            [
                'number'   => '.0001',
                'expected' => '0.0001',
            ],
            [
                'number'   => '-.0001',
                'expected' => '-0.0001',
            ],
            [
                'number'   => '0000.0001',
                'expected' => '0.0001',
            ],
            [
                'number'   => '-0000.0001',
                'expected' => '-0.0001',
            ],
            [
                'number'   => '+.0001',
                'expected' => '0.0001',
            ],
            [
                'number'   => '00100.0001',
                'expected' => '100.0001',
            ],
            [
                'number'   => '-00100.0001',
                'expected' => '-100.0001',
            ],
            [
                'number'   => '+00100.0001',
                'expected' => '100.0001',
            ],
        ];
    }

    /**
     * @dataProvider normalizeData
     *
     * @param mixed $number
     * @param mixed $expected
     */
    public function testNormalize($number, $expected)
    {
        $this->assertSame($expected, (string) Math::number($number));
    }

    /**
     * @dataProvider addData
     *
     * @param string $left
     * @param string $right
     * @param string $expected
     */
    public function testAdd($left, $right, $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($left)->add($right)
        );
    }

    /**
     * @return array
     */
    public function addData()
    {
        return [
            [
                'left'     => '1',
                'right'    => '0',
                'expected' => '1',
            ],
            [
                'left'     => '1',
                'right'    => '-1',
                'expected' => '0',
            ],
            [
                'left'     => '.9',
                'right'    => '+0.1',
                'expected' => '1',
            ],
            [
                'left'     => '.9',
                'right'    => '41.1',
                'expected' => '42',
            ],
        ];
    }

    /**
     * @dataProvider subData
     *
     * @param string $left
     * @param string $right
     * @param string $expected
     */
    public function testSub($left, $right, $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($left)->sub($right)
        );
    }

    /**
     * @return array
     */
    public function subData()
    {
        return [
            [
                'left'     => '1',
                'right'    => '0',
                'expected' => '1',
            ],
            [
                'left'     => '1',
                'right'    => '-1',
                'expected' => '2',
            ],
            [
                'left'     => '-1',
                'right'    => '27',
                'expected' => '-28',
            ],
            [
                'left'     => '.9',
                'right'    => '+.1',
                'expected' => '0.8',
            ],
            [
                'left'     => '.8',
                'right'    => '-41.2',
                'expected' => '42',
            ],
        ];
    }

    /**
     * @dataProvider mulDivData
     *
     * @param string $left
     * @param string $right
     * @param string $expected
     */
    public function testMulDiv($left, $right, $expected)
    {
        $result = Math::number($left)->mul($right);
        $this->assertSame(
            $expected,
            (string) $result
        );

        $this->assertSame(
            $left,
            (string) $result->div($right)
        );
    }

    /**
     * @return array
     */
    public function mulDivData()
    {
        return [
            [
                'left'     => '2',
                'right'    => '21',
                'expected' => '42',
            ],
            [
                'left'     => '0',
                'right'    => '42',
                'expected' => '0',
            ],
            [
                'left'     => '-546.2255',
                'right'    => '42',
                'expected' => '-22941.471',
            ],
        ];
    }

    /**
     * @dataProvider sqrtData
     *
     * @param string $number
     * @param string $expected
     */
    public function testSqrt($number, $expected)
    {
        $result = Math::number($number)->sqrt();
        $this->assertSame(
            $expected,
            (string) $result
        );

        $this->assertSame(
            $number,
            (string) $result->pow(2)
        );
    }

    /**
     * @return array
     */
    public function sqrtData()
    {
        $result = [
            [
            'number'   => '64',
            'expected' => '8',
        ],
            [
                'number'   => '9.8596',
                'expected' => '3.14',
            ],
        ];

        for ($i = 1; $i < 50; ++$i) {
            $number   = mt_rand(1, 10000) . '.' . mt_rand(0, 10000);
            $result[] = [
                'number'   => (string) Math::number($number)->pow(2),
                'expected' => (string) Math::number($number),
            ];
        }

        return $result;
    }

    /**
     * @dataProvider modData
     *
     * @param string $number
     * @param string $mod
     * @param string $expected
     */
    public function testMod($number, $mod, $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->mod($mod)
        );
    }

    /**
     * @return array
     */
    public function modData()
    {
        $result = [
            [
                'number'   => '64',
                'mod'      => '8',
                'expected' => '0',
            ],
            [
                'number'   => '42',
                'mod'      => '7',
                'expected' => '0',
            ],
            [
                'number'   => '42',
                'mod'      => '4',
                'expected' => '2',
            ],
        ];

        for ($i = 1; $i < 50; ++$i) {
            $number   = mt_rand(1, 10000);
            $mod      = mt_rand(1, 100);
            $result[] = [
                'number'   => (string) Math::number($number),
                'mod'      => (string) $mod,
                'expected' => (string) ($number % $mod),
            ];
        }

        return $result;
    }

    /**
     * @dataProvider powModData
     *
     * @param string $number
     * @param string $pow
     * @param string $mod
     */
    public function testPowMod($number, $pow, $mod)
    {
        $this->assertSame(
            (string) Math::number($number)->powMod($pow, $mod),
            (string) Math::number($number)->pow($pow)->mod($mod)
        );
    }

    /**
     * @return array
     */
    public function powModData()
    {
        $result = [];
        for ($i = 1; $i < 50; ++$i) {
            $result[] = [
                'number' => (string) mt_rand(1, 100000),
                'pow'    => (string) mt_rand(1, 1000),
                'mod'    => (string) mt_rand(1, 1000),
            ];
        }

        return $result;
    }

    /**
     * @dataProvider ceilData
     *
     * @param string $number
     * @param string $expected
     */
    public function testCeil($number, $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->ceil()
        );
    }

    /**
     * @return array
     */
    public function ceilData()
    {
        return [
            [
                'number'   => '1',
                'expected' => '1',
            ],
            [
                'number'   => '1.000001',
                'expected' => '2',
            ],
            [
                'number'   => '1.000000000000',
                'expected' => '1',
            ],
            [
                'number'   => '1.' . str_repeat('0', 2 * Math::PRECISION) . '1',
                'expected' => '2',
            ],
            [
                'number'   => '-1.' . str_repeat('9', 2 * Math::PRECISION),
                'expected' => '-1',
            ],
            [
                'number'   => '-6.99',
                'expected' => '-6',
            ],
            [
                'number'   => '-0',
                'expected' => '0',
            ],
            [
                'number'   => '+0',
                'expected' => '0',
            ],
        ];
    }

    /**
     * @dataProvider floorData
     *
     * @param string $number
     * @param string $expected
     */
    public function testFloor($number, $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->floor()
        );
    }

    /**
     * @return array
     */
    public function floorData()
    {
        return [
            [
                'number'   => '1',
                'expected' => '1',
            ],
            [
                'number'   => '1.000001',
                'expected' => '1',
            ],
            [
                'number'   => '1.000000000000',
                'expected' => '1',
            ],
            [
                'number'   => '1.' . str_repeat('0', 2 * Math::PRECISION) . '1',
                'expected' => '1',
            ],
            [
                'number'   => '-1.' . str_repeat('9', 2 * Math::PRECISION),
                'expected' => '-2',
            ],
            [
                'number'   => '-6.99',
                'expected' => '-7',
            ],
            [
                'number'   => '-0',
                'expected' => '0',
            ],
            [
                'number'   => '+0',
                'expected' => '0',
            ],
        ];
    }

    /**
     * @dataProvider absData
     *
     * @param string $number
     * @param string $expected
     */
    public function testAbs($number, $expected)
    {
        $this->assertSame(
            $expected,
            (string) Math::number($number)->abs()
        );
    }

    /**
     * @return array
     */
    public function absData()
    {
        return [
            [
                'number'   => '-42',
                'expected' => '42',
            ],
            [
                'number'   => '+42',
                'expected' => '42',
            ],
        ];
    }

    /**
     * @dataProvider isNumberData
     *
     * @param string $number
     * @param string $expected
     */
    public function testIsNumber($number, $expected)
    {
        $this->assertSame(
            $expected,
            Math::isNumber($number)
        );
    }

    /**
     * @return array
     */
    public function isNumberData()
    {
        return [
            [
                'number'   => '-42',
                'expected' => true,
            ],
            [
                'number'   => '',
                'expected' => false,
            ],
            [
                'number'   => '+42',
                'expected' => true,
            ],
            [
                'number'   => '+00004200000',
                'expected' => true,
            ],
            [
                'number'   => '-000042000.00',
                'expected' => true,
            ],
            [
                'number'   => '-000042000.',
                'expected' => false,
            ],
            [
                'number'   => '000.042000.',
                'expected' => false,
            ],
            [
                'number'   => '.042000.',
                'expected' => false,
            ],
            [
                'number'   => '42e64',
                'expected' => false,
            ],
            [
                'number'   => ' 42',
                'expected' => false,
            ],
            [
                'number'   => '4 2',
                'expected' => false,
            ],
            [
                'number'   => '42 ',
                'expected' => false,
            ],
            [
                'number'   => '000',
                'expected' => true,
            ],
            [
                'number'   => '.000',
                'expected' => true,
            ],
        ];
    }

    /**
     * @return array
     */
    public function baseConvertData()
    {
        return [
            [
                'number' => '0',
                'base'   => '62',
            ],
            [
                'number' => '0',
                'base'   => '36',
            ],
            [
                'number' => '10',
                'base'   => '62',
            ],
            [
                'number' => '10',
                'base'   => '36',
            ],
            [
                'number' => '62',
                'base'   => '62',
            ],
            [
                'number' => '36',
                'base'   => '36',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '16',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '63',
            ],
            [
                'number' => '00025517302925525525',
                'base'   => '28',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '8',
            ],
            [
                'number' => '000255173029255255255',
                'base'   => '36',
            ],
            [
                'number' => '255173029255255255',
                'base'   => '2',
            ],
            [
                'number' => '25517993029255255255',
                'base'   => '37',
            ],
            [
                'number' => '25517993029255255255',
                'base'   => '35',
            ],
            [
                'number' => '1000',
                'base'   => '64',
            ],
            [
                'number' => '0',
                'base'   => '48',
            ],
            [
                'number' => '9856565',
                'base'   => '62',
            ],
        ];
    }

    /**
     * @dataProvider baseConvertData
     *
     * @param mixed $number
     * @param mixed $base
     *
     * @throws \InvalidArgumentException
     */
    public function testBaseConvert($number, $base)
    {
        $this->assertSame(
            (string) Math::number($number),
            (string) Math::fromBase(Math::number($number)->toBase($base), $base)
        );

        if ($base > 62) {
            return;
        }

        $this->assertSame(
            (string) Math::number($number),
            Math::normalizeNumber(Math::baseConvert(Math::baseConvert($number, 10, $base), $base, 10))
        );

        if (!Math::gmpSupport()) {
            return;
        }

        $expected = gmp_strval(gmp_init((string) Math::number($number)), $base);
        $this->assertSame(
            $expected,
            Math::baseConvert($number, 10, $base)
        );

        Math::gmpSupport(true);
        $this->assertSame(
            $expected,
            (string) Math::number($number)->toBase($base)
        );
        Math::gmpSupport(false);
    }
}
