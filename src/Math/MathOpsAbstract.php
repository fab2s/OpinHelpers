<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\Math\OpinHelpers;

/**
 * Abstract class MathOpsAbstract
 */
abstract class MathOpsAbstract extends MathBaseAbstract
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
}
