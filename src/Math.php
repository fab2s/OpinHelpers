<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

use fab2s\Math\OpinHelpers\MathOpsAbstract;

/**
 * Class Math
 */
class Math extends MathOpsAbstract
{
    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function gte($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) >= 0);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function gt($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === 1);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function lte($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) <= 0);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function lt($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === -1);
    }

    /**
     * @param string $number
     *
     * @throws \InvalidArgumentException
     *
     * @return bool
     */
    public function eq($number)
    {
        return (bool) (bccomp($this->number, static::validateInputNumber($number), $this->precision) === 0);
    }

    /**
     * returns the highest number among all arguments
     *
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function max(...$numbers)
    {
        foreach ($numbers as $number) {
            if (bccomp(static::validateInputNumber($number), $this->number, $this->precision) === 1) {
                $this->number = $number;
            }
        }

        return $this;
    }

    /**
     * returns the smallest number among all arguments
     *
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function min(...$numbers)
    {
        foreach ($numbers as $number) {
            if (bccomp(static::validateInputNumber($number), $this->number, $this->precision) === -1) {
                $this->number = $number;
            }
        }

        return $this;
    }

    /**
     * convert decimal value to any other base bellow or equals to 64
     *
     * @param int $base
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function toBase($base)
    {
        if ($this->normalize()->hasDecimals()) {
            throw new \InvalidArgumentException('Argument number is not an integer in ' . __METHOD__);
        }

        // do not mutate, only support positive integers
        $number = ltrim((string) $this, '-');
        if (static::$gmpSupport && $base <= 62) {
            return static::baseConvert($number, 10, $base);
        }

        $result   = '';
        $baseChar = static::getBaseChar($base);
        while (bccomp($number, 0) != 0) { // still data to process
            $rem    = bcmod($number, $base); // calc the remainder
            $number = bcdiv(bcsub($number, $rem), $base);
            $result = $baseChar[$rem] . $result;
        }

        $result = $result ? $result : $baseChar[0];

        return (string) $result;
    }

    /**
     * @param int    $decimals
     * @param string $decPoint
     * @param string $thousandsSep
     *
     * @return string
     */
    public function format($decimals = 0, $decPoint = '.', $thousandsSep = ' ')
    {
        $decimals = max(0, (int) $decimals);
        $dec      = '';
        // do not mutate
        $number   = (new static($this))->round($decimals)->normalize();
        $sign     = $number->isPositive() ? '' : '-';
        if ($number->abs()->hasDecimals()) {
            list($number, $dec) = explode('.', (string) $number);
        }

        if ($decimals) {
            $dec = sprintf("%'0-" . $decimals . 's', $dec);
        }

        return $sign . preg_replace("/(?<=\d)(?=(\d{3})+(?!\d))/", $thousandsSep, $number) . ($decimals ? $decPoint . $dec : '');
    }
}

// OMG a dynamic static anti pattern ^^
Math::gmpSupport();
