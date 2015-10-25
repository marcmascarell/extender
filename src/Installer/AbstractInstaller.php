<?php namespace Mascame\Extender\Installer;

use Mascame\Extender\Event\Eventable;

abstract class AbstractInstaller implements InstallerInterface {
    use Eventable;
}
