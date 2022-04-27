<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SessionFile\Communication\Plugin\Session;

use SessionHandlerInterface;
use Spryker\Shared\SessionExtension\Dependency\Plugin\SessionHandlerProviderPluginInterface;
use Spryker\Zed\Kernel\Communication\AbstractPlugin;

/**
 * @method \Spryker\Zed\SessionFile\Communication\SessionFileCommunicationFactory getFactory()
 * @method \Spryker\Zed\SessionFile\SessionFileConfig getConfig()
 * @method \Spryker\Zed\SessionFile\Business\SessionFileFacadeInterface getFacade()
 */
class SessionHandlerFileProviderPlugin extends AbstractPlugin implements SessionHandlerProviderPluginInterface
{
    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return string
     */
    public function getSessionHandlerName(): string
    {
        return $this->getConfig()->getSessionHandlerFileName();
    }

    /**
     * {@inheritDoc}
     *
     * @api
     *
     * @return \SessionHandlerInterface
     */
    public function getSessionHandler(): SessionHandlerInterface
    {
        return $this->getFactory()->createSessionHandlerFile();
    }
}
