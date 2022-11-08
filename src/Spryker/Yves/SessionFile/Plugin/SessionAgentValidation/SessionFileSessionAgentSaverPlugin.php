<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Yves\SessionFile\Plugin\SessionAgentValidation;

use Generated\Shared\Transfer\SessionEntityRequestTransfer;
use Generated\Shared\Transfer\SessionEntityResponseTransfer;
use Spryker\Yves\Kernel\AbstractPlugin;
use SprykerShop\Yves\SessionAgentValidationExtension\Dependency\Plugin\SessionAgentSaverPluginInterface;

/**
 * @method \Spryker\Client\SessionFile\SessionFileClient getClient()
 * @method \Spryker\Yves\SessionFile\SessionFileFactory getFactory()
 * @method \Spryker\Yves\SessionFile\SessionFileConfig getConfig()
 */
class SessionFileSessionAgentSaverPlugin extends AbstractPlugin implements SessionAgentSaverPluginInterface
{
    /**
     * {@inheritDoc}
     * - Requires `SessionEntityRequestTransfer.idEntity` transfer property to be set.
     * - Requires `SessionEntityRequestTransfer.idSession` transfer property to be set.
     * - Requires `SessionEntityRequestTransfer.entityType` transfer property to be set.
     * - Saves agent's session data to a file.
     *
     * @api
     *
     * @param \Generated\Shared\Transfer\SessionEntityRequestTransfer $sessionEntityRequestTransfer
     *
     * @return \Generated\Shared\Transfer\SessionEntityResponseTransfer
     */
    public function saveSession(SessionEntityRequestTransfer $sessionEntityRequestTransfer): SessionEntityResponseTransfer
    {
        return $this->getFactory()
            ->createSessionEntitySaver()
            ->save($sessionEntityRequestTransfer);
    }
}
