<?php

namespace Mascame\Extender\Installer;

use Mascame\Extender\Event\EventInterface;

interface InstallerInterface
{
    /**
     * Compares registered extensions and current stored extensions and
     * adds or removes them in case of changes.
     *
     * @param $extensions
     * @return mixed
     */
    public function handleExtensionChanges($extensions);

    /**
     * @return array
     */
    public function getInstalled();

    /**
     * @return array
     */
    public function getUninstalled();

    /**
     * @param $name
     * @return mixed
     */
    public function isInstalled($name);

    /**
     * @param $extension
     * @return mixed
     */
    public function install($extension);

    /**
     * @param $extension
     * @return mixed
     */
    public function uninstall($extension);

    /**
     * @return EventInterface
     */
    public function getEventDispatcher();

    /**
     * @param EventInterface $eventDispatcher
     */
    public function setEventDispatcher($eventDispatcher);
}
