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
    protected $lockType = self::LOCK_EXTERNAL;

    /**
     * max number of lock attempt
     *
     * @var int
     */
    protected $lockTry = 3;

    /**
     * The number of seconds to wait between lock attempts
     *
     * @var float
     */
    protected $lockWait = 0.1;

    /**
     * @var string
     */
    protected $file;

    /**
     * @var resource
     */
    protected $handle;

    /**
     * @var string fopen mode
     */
    protected $mode;

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
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($file, $lockMethod, $mode = 'wb')
    {
        $fileDir = dirname($file);
        if (!($fileDir = realpath($fileDir))) {
            throw new \InvalidArgumentException('File path not valid');
        }

        if ($lockMethod === self::LOCK_SELF) {
            $this->lockType = self::LOCK_SELF;
            $this->mode     = $mode;
            $this->file     = $fileDir . '/' . basename($file);

            return;
        }

        $fileDir    = is_writeable($fileDir) ? $fileDir . '/' : sys_get_temp_dir() . '/' . sha1($fileDir) . '_';
        $this->file = $fileDir . basename($file) . '.lock';
    }

    /**
     * since there is no more auto unlocking
     */
    public function __destruct()
    {
        $this->unLock();
    }

    /**
     * @param string     $file
     * @param string     $mode     fopen() mode
     * @param int|null   $maxTries 0|null for single non blocking attempt
     *                             1 for a single blocking attempt
     *                             1-N Number of non blocking attempts
     * @param float|null $lockWait Time to wait between attempts in second
     *
     * @return bool|static
     */
    public static function open($file, $mode, $maxTries = null, $lockWait = null)
    {
        $instance = new static($file, self::LOCK_SELF, $mode);
        $maxTries = max(0, (int) $maxTries);
        if ($maxTries > 1) {
            $instance->setLockTry($maxTries);
            $lockWait = max(0, (float) $lockWait);
            if ($lockWait > 0) {
                $instance->setLockWait($lockWait);
            }
            $instance->obtainLock();
        } else {
            $instance->doLock((bool) $maxTries);
        }

        if ($instance->isLocked()) {
            return $instance;
        }

        $instance->unLock();

        return false;
    }

    /**
     * @return resource
     */
    public function getHandle()
    {
        return $this->handle;
    }

    /**
     * @return string
     */
    public function getLockType()
    {
        return $this->lockType;
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
        $tries       = 0;
        $waitClosure = $this->getWaitClosure();
        do {
            if ($this->doLock()->isLocked()) {
                return $this;
            }

            ++$tries;
            $waitClosure();
        } while ($tries < $this->lockTry);

        return $this;
    }

    /**
     * @param bool $blocking
     *
     * @return $this
     */
    public function doLock($blocking = false)
    {
        if ($this->lockAcquired) {
            return $this;
        }

        $this->mode   = $this->mode ?: (is_file($this->file) ? 'rb' : 'wb');
        $this->handle = fopen($this->file, $this->mode) ?: null;
        if (
            $this->lockType === self::LOCK_EXTERNAL &&
            $this->mode === 'wb' &&
            !$this->handle
        ) {
            // if another process won the race at creating lock file
            $this->mode   = 'rb';
            $this->handle = fopen($this->file, $this->mode) ?: null;
        }

        $this->lockAcquired = $this->handle ? flock($this->handle, $blocking ? LOCK_EX : LOCK_EX | LOCK_NB) : false;

        if (!$this->lockAcquired) {
            $this->unLock();
        }

        return $this;
    }

    /**
     * release the lock
     */
    public function unLock()
    {
        if (is_resource($this->handle)) {
            fflush($this->handle);
            flock($this->handle, LOCK_UN);
            fclose($this->handle);
        }

        $this->lockAcquired = false;
        $this->handle       = null;

        return $this;
    }

    /**
     * @param int $number
     *
     * @return $this
     */
    public function setLockTry($number)
    {
        $this->lockTry = max(1, (int) $number);

        return $this;
    }

    /**
     * @param float $float
     *
     * @return $this
     */
    public function setLockWait($float)
    {
        $this->lockWait = max(0.0001, $float);

        return $this;
    }

    /**
     * @return \Closure
     */
    protected function getWaitClosure()
    {
        if ($this->lockWait > 300) {
            $wait = (int) $this->lockWait;

            return function() use ($wait) {
                sleep($wait);
            };
        }

        $wait = (int) ($this->lockWait * 1000000);

        return function() use ($wait) {
            usleep($wait);
        };
    }
}
