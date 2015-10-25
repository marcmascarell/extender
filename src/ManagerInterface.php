<?php namespace Mascame\Extender;

use Mascame\Extender\Installer\InstallerInterface;

interface ManagerInterface {

    /**
     * @param InstallerInterface $installer
     */
    public function __construct(InstallerInterface $installer);

    /**
     * @return InstallerInterface
     */
    public function installer();

    /**
     * @param $name
     * @return bool
     */
    public function isInstalled($name);

    /**
     * @return array
     */
    public function getAll();

    /**
     * @return array
     */
    public function getRegistered();

    /**
     * @param $name
     * @param \Closure $plugin
     */
    public function add($name, \Closure $plugin);

    public function boot();
}
