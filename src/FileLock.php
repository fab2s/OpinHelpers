<?php

/*
 * This file is part of OpinHelpers.
 *     (c) Fabrice de Stefanis / https://github.com/fab2s/OpinHelpers
 * This source file is licensed under the MIT license which you will
 * find in the LICENSE file or at https://opensource.org/licenses/MIT
 */

namespace fab2s\OpinHelpers;

/**
 * Class FileLock
 */
class FileLock
{
    /**
     * max number of lock attempt
     *
     * @var int
     */
    protected $maxTry = 3;

    /**
     * The number of seconds to wait between lock attempts
     *
     * @var float
     */
    protected $lockWait = 0.1;

    /**
     * @var string
     */
    protected $lockFile;

    /**
     * @var resource
     */
    protected $lockHandle;

    /**
     * @var bool
     */
    protected $lockAcquired = false;

    /**
     * FsLock constructor.
     *
     * @param string $lockFile
     */
    public function __construct($lockFile)
    {
        $this->lockFile = $lockFile . '.lock';
    }

    /**
     * since there is no more auto unlocking
     */
    public function __destruct()
    {
        $this->releaseLock();
    }

    /**
     * @return bool
     */
    public function isLocked()
    {
        return $this->lockAcquired;
    }

    /**
     * obtain a lock with retries
     *
     * @return bool
     */
    public function obtainLock()
    {
        $tries = 0;
        $uWait = $this->lockWait * 1000000;
        do {
            if ($this->setLock()) {
                return true;
            }

            ++$tries;
            usleep($uWait);
        } while ($tries < $this->maxTry);

        return false;
    }

    /**
     * @return bool
     */
    public function setLock()
    {
        if ($this->lockAcquired) {
            return true;
        }

        $mode             = is_file($this->lockFile) ? 'rb' : 'wb';
        $this->lockHandle = fopen($this->lockFile, $mode);
        if ($mode == 'wb') {
            if (!$this->lockHandle) {
                // in case another process won the race
                $mode             = 'rb';
                $this->lockHandle = fopen($this->lockFile, $mode);
            }
        }

        return $this->lockAcquired = $this->lockHandle ? flock($this->lockHandle, LOCK_EX | LOCK_NB) : false;
    }

    /**
     * release the lock
     */
    public function releaseLock()
    {
        if (is_resource($this->lockHandle)) {
            fflush($this->lockHandle);
            flock($this->lockHandle, LOCK_UN);
            fclose($this->lockHandle);
        }

        $this->lockAcquired = false;
        $this->lockHandle   = null;

        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function setMaxTry($number)
    {
        $this->maxTry = max(1, (int) $number);

        return $this;
    }

    /**
     * @param float $float
     *
     * @return $this
     */
    public function setLockWait($float)
    {
        $this->lockWait = max(0.001, $float);

        return $this;
    }
}
