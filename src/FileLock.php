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
     * external lock type actually locks "inputFile.lock"
     */
    const LOCK_EXTERNAL = 'external';

    /**
     * lock the file itself
     */
    const LOCK_SELF = 'self';

    /**
     * @var string
     */
    protected $lockMethod = self::LOCK_EXTERNAL;

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
     * @var string fopen mode
     */
    protected $lockMode;

    /**
     * @var bool
     */
    protected $lockAcquired = false;

    /**
     * FileLock constructor.
     *
     * @param string $file
     * @param string $lockMethod
     * @param string $mode
     */
    public function __construct($file, $lockMethod, $mode = 'wb')
    {
        $fileDir = dirname($file);
        if (!($fileDir = realpath($fileDir))) {
            throw new \InvalidArgumentException('File path not valid');
        }

        if ($lockMethod === self::LOCK_SELF) {
            $this->lockMethod = self::LOCK_SELF;
            $this->lockMode   = $mode;
            $this->lockFile   = $fileDir . '/' . basename($file);

            return;
        }

        $fileDir        = is_writeable($fileDir) ? $fileDir . '/' : sys_get_temp_dir() . '/' . sha1($fileDir) . '_';
        $this->lockFile = $fileDir . basename($file) . '.lock';
    }

    /**
     * since there is no more auto unlocking
     */
    public function __destruct()
    {
        $this->releaseLock();
    }

    /**
     * @return resource
     */
    public function getLockHandle()
    {
        return $this->lockHandle;
    }

    /**
     * @return string
     */
    public function getLockMethod()
    {
        return $this->lockMethod;
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
     * @return $this
     */
    public function obtainLock()
    {
        $tries = 0;
        $uWait = (int) ($this->lockWait * 1000000);
        do {
            if ($this->setLock()->isLocked()) {
                return $this;
            }

            ++$tries;
            usleep($uWait);
        } while ($tries < $this->maxTry);

        return $this;
    }

    /**
     * @param bool $blocking
     *
     * @return $this
     */
    public function setLock($blocking = false)
    {
        if ($this->lockAcquired) {
            return $this;
        }

        $this->lockMode   = $this->lockMode ?: (is_file($this->lockFile) ? 'rb' : 'wb');
        $this->lockHandle = fopen($this->lockFile, $this->lockMode) ?: null;
        if (
            $this->lockMethod === self::LOCK_EXTERNAL &&
            $this->lockMode === 'wb' &&
            !$this->lockHandle
        ) {
            // if another process won the race at creating lock file
            $this->lockMode   = 'rb';
            $this->lockHandle = fopen($this->lockFile, $this->lockMode) ?: null;
        }

        $this->lockAcquired = $this->lockHandle ? flock($this->lockHandle, $blocking ? LOCK_EX : LOCK_EX | LOCK_NB) : false;

        return $this;
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
