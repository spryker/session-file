<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Shared\SessionFile\Handler;

use SessionHandlerInterface;
use Spryker\Shared\SessionFile\Dependency\Service\SessionFileToMonitoringServiceInterface;

class SessionHandlerFile implements SessionHandlerInterface
{
    use SessionHandlerFileTrait;

    /**
     * @var string
     */
    public const METRIC_SESSION_DELETE_TIME = 'File/Session_delete_time';

    /**
     * @var string
     */
    public const METRIC_SESSION_WRITE_TIME = 'File/Session_write_time';

    /**
     * @var string
     */
    public const METRIC_SESSION_READ_TIME = 'File/Session_read_time';

    /**
     * @var string
     */
    protected $keyPrefix = 'session:';

    /**
     * @var int
     */
    protected $lifetime;

    /**
     * @var string
     */
    protected $savePath;

    /**
     * @var \Spryker\Shared\SessionFile\Dependency\Service\SessionFileToMonitoringServiceInterface
     */
    protected $monitoringService;

    /**
     * @param string $savePath
     * @param int $lifetime
     * @param \Spryker\Shared\SessionFile\Dependency\Service\SessionFileToMonitoringServiceInterface $monitoringService
     */
    public function __construct(string $savePath, int $lifetime, SessionFileToMonitoringServiceInterface $monitoringService)
    {
        $this->savePath = $savePath;
        $this->lifetime = $lifetime;
        $this->monitoringService = $monitoringService;
    }

    /**
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * @param string $savePath
     * @param string $sessionName
     *
     * @return bool
     */
    protected function executeOpen(string $savePath, string $sessionName): bool
    {
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0775, true);
        }

        return true;
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    protected function executeRead(string $sessionId): string
    {
        $startTime = microtime(true);
        $sessionKey = $this->buildSessionKey($sessionId);
        $sessionFile = $this->savePath . DIRECTORY_SEPARATOR . $sessionKey;
        if (!file_exists($sessionFile)) {
            return '';
        }

        $content = file_get_contents($sessionFile);

        $this->monitoringService->addCustomParameter(static::METRIC_SESSION_READ_TIME, microtime(true) - $startTime);

        return $content === false ? '' : $content;
    }

    /**
     * @param string $sessionId
     * @param string $sessionData
     *
     * @return bool
     */
    protected function executeWrite(string $sessionId, string $sessionData): bool
    {
        $sessionKey = $this->buildSessionKey($sessionId);

        if (strlen($sessionData) < 1) {
            return false;
        }

        $startTime = microtime(true);
        $result = file_put_contents($this->savePath . DIRECTORY_SEPARATOR . $sessionKey, $sessionData);
        $this->monitoringService->addCustomParameter(static::METRIC_SESSION_WRITE_TIME, microtime(true) - $startTime);

        return $result > 0;
    }

    /**
     * @param string $sessionId
     *
     * @return bool
     */
    protected function executeDestroy(string $sessionId): bool
    {
        $sessionKey = $this->buildSessionKey($sessionId);
        $file = $this->savePath . DIRECTORY_SEPARATOR . $sessionKey;
        if (file_exists($file)) {
            $startTime = microtime(true);
            unlink($file);
            $this->monitoringService->addCustomParameter(static::METRIC_SESSION_DELETE_TIME, microtime(true) - $startTime);
        }

        return true;
    }

    /**
     * @param int $maxLifetime
     *
     * @return int|false
     */
    protected function executeGc(int $maxLifetime): int|false
    {
        $deletedSessionsCount = 0;
        $time = time();
        $files = glob($this->buildSessionFilePattern(), GLOB_NOSORT) ?: [];
        foreach ($files as $file) {
            $fileTime = filemtime($file);
            $fileExpired = $fileTime + $maxLifetime < $time;
            $fileExist = (bool)$fileTime;
            if ($fileExist && $fileExpired) {
                unlink($file);
                $deletedSessionsCount++;
            }
        }

        return $deletedSessionsCount;
    }

    /**
     * @return string
     */
    protected function buildSessionFilePattern(): string
    {
        return sprintf(
            '%s%s%s*',
            $this->savePath,
            DIRECTORY_SEPARATOR,
            $this->keyPrefix,
        );
    }

    /**
     * @param string $sessionId
     *
     * @return string
     */
    protected function buildSessionKey(string $sessionId): string
    {
        return $this->keyPrefix . $sessionId;
    }
}
