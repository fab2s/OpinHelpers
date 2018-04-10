<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

use fab2s\Math\OpinHelpers\MathAbstract;

/**
 * Class Math
 */
class Math extends MathAbstract
{
    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function add(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcadd($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function sub(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcsub($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function mul(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcmul($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @param string[] $numbers
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function div(...$numbers)
    {
        foreach ($numbers as $number) {
            $this->number = bcdiv($this->number, static::validateInputNumber($number), $this->precision);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function sqrt()
    {
        $this->number = bcsqrt($this->number, $this->precision);

        return $this;
    }

    /**
     * @param string $exponent
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function pow($exponent)
    {
        $this->number = bcpow($this->number, static::validatePositiveInteger($exponent), $this->precision);

        return $this;
    }

    /**
     * @param string $modulus
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function mod($modulus)
    {
        $this->number = bcmod($this->number, static::validatePositiveInteger($modulus));

        return $this;
    }

    /**
     * @param string $exponent
     * @param string $modulus
     *
     * @throws \InvalidArgumentException
     *
     * @return $this
     */
    public function powMod($exponent, $modulus)
    {
        $this->number = bcpowmod($this->number, static::validatePositiveInteger($exponent), static::validatePositiveInteger($modulus));

        return $this;
    }

    /**
     * @param int $precision
     *
     * @return $this
     */
    public function round($precision = 0)
    {
        $precision = max(0, (int) $precision);
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, '0.' . str_repeat('0', $precision) . '5', $precision);

                return $this;
            }

            $this->number = bcsub($this->number, '0.' . str_repeat('0', $precision) . '5', $precision);
        }

        return $this;
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

    /**
     * @return $this
     */
    public function ceil()
    {
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, (preg_match('`\.[0]*$`', $this->number) ? '0' : '1'), 0);

                return $this;
            }

            $this->number = bcsub($this->number, '0', 0);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function floor()
    {
        if ($this->hasDecimals()) {
            if ($this->isPositive()) {
                $this->number = bcadd($this->number, 0, 0);

                return $this;
            }

            $this->number = bcsub($this->number, (preg_match('`\.[0]*$`', $this->number) ? '0' : '1'), 0);
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function abs()
    {
        $this->number = ltrim($this->number, '-');

        return $this;
    }

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
     * convert any based value bellow or equals to 64 to its decimal value
     *
     * @param string $number
     * @param int    $base
     *
     * @throws \InvalidArgumentException
     *
     * @return static
     */
    public static function fromBase($number, $base)
    {
        // cleanup
        $number   = trim($number);
        $baseChar = static::getBaseChar($base);
        // Convert string to lower case since base36 or less is case insensitive
        if ($base < 37) {
            $number = strtolower($number);
        }

        // clean up the input string if it uses particular input formats
        switch ($base) {
            case 16:
                // remove 0x from start of string
                if (substr($number, 0, 2) === '0x') {
                    $number = substr($number, 2);
                }
                break;
            case 8:
                // remove the 0 from the start if it exists - not really required
                if ($number[0] === 0) {
                    $number = substr($number, 1);
                }
                break;
            case 2:
                // remove an 0b from the start if it exists
                if (substr($number, 0, 2) === '0b') {
                    $number = substr($number, 2);
                }
                break;
            case 64:
                // remove padding chars: =
                $number = rtrim($number, '=');
                break;
        }

        // only support positive integers
        $number = ltrim($number, '-');
        if ($number === '' || strpos($number, '.') !== false) {
            throw new \InvalidArgumentException('Argument number is not an integer');
        }

        if (trim($number, $baseChar[0]) === '') {
            return new static('0');
        }

        if (static::$gmpSupport && $base <= 62) {
            return new static(static::baseConvert($number, $base, 10));
        }

        // By now we know we have a correct base and number
        $result    = '';
        $numberLen = strlen($number);
        // Now loop through each digit in the number
        for ($i = $numberLen - 1; $i >= 0; --$i) {
            $char = $number[$i]; // extract the last char from the number
            $ord  = strpos($baseChar, $char); // get the decimal value
            if ($ord === false || $ord > $base) {
                throw new \InvalidArgumentException('Argument number is invalid');
            }

            // Now convert the value+position to decimal
            $result = bcadd($result, bcmul($ord, bcpow($base, ($numberLen - $i - 1))));
        }

        return new static($result ? $result : '0');
    }

    /**
     * Convert a from a given base (up to 62) to base 10.
     *
     * WARNING This method requires ext-gmp
     *
     * @param string $number
     * @param int    $fromBase
     * @param int    $toBase
     *
     * @return string
     *
     * @internal param int $base
     */
    public static function baseConvert($number, $fromBase = 10, $toBase = 62)
    {
        return gmp_strval(gmp_init($number, $fromBase), $toBase);
    }
}

// OMG a dynamic static anti pattern ^^
Math::gmpSupport();
