<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\SessionFile;

use Spryker\Shared\SessionFile\SessionFileConstants;
use Spryker\Yves\Kernel\AbstractBundleConfig;

/**
 * @method \Spryker\Shared\SessionFile\SessionFileConfig getSharedConfig()
 */
class SessionFileConfig extends AbstractBundleConfig
{
    /**
     * @var string
     */
    public const SESSION_HANDLER_FILE = 'file';

    /**
     * @api
     *
     * @return int
     */
    public function getSessionLifeTime(): int
    {
        return (int)$this->get(SessionFileConstants::YVES_SESSION_TIME_TO_LIVE, 0);
    }

    /**
     * @api
     *
     * @return string
     */
    public function getSessionHandlerFileSavePath(): string
    {
        return $this->get(SessionFileConstants::YVES_SESSION_FILE_PATH, '');
    }

    /**
     * @api
     *
     * @return string
     */
    public function getSessionHandlerFileName(): string
    {
        return $this->getSharedConfig()->getSessionHandlerFileName();
    }

    /**
     * Specification:
     * - Returns file path for saving active session IDs.
     *
     * @api
     *
     * @return string
     */
    public function getActiveSessionFilePath(): string
    {
        return $this->getSharedConfig()->getActiveSessionFilePath();
    }
}
