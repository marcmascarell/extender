<?php

namespace Mascame\Extender\Installer;

use Mascame\Extender\Event\Eventable;

abstract class AbstractInstaller implements InstallerInterface
{
    use Eventable;

    const STATUS_INSTALLED = 'installed';
    const STATUS_UNINSTALLED = 'uninstalled';

    const ACTION_INSTALL = 'install';
    const ACTION_UNINSTALL = 'uninstall';

    protected $actionToStatus = [
        self::ACTION_INSTALL => self::STATUS_INSTALLED,
        self::ACTION_UNINSTALL => self::STATUS_UNINSTALLED,
    ];
}
