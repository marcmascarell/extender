<?php

namespace Mascame\Extender;

use Mascame\Extender\Event\Eventable;
use Mascame\Extender\Event\EventInterface;
use Mascame\Extender\Booter\BooterInterface;
use Mascame\Extender\Installer\InstallerInterface;

class Manager implements ManagerInterface
{
    use Eventable;

    /**
     * @var array
     */
    protected $extensions = [];

    /**
     * @var array
     */
    protected $extensionInstances = [];

    /**
     * @var InstallerInterface
     */
    protected $installer;

    /**
     * @var BooterInterface
     */
    protected $booter = null;

    /**
     * @var EventInterface
     */
    protected $eventDispatcher = null;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @param InstallerInterface $installer
     * @param BooterInterface|null $booter
     * @param EventInterface|null $eventDispatcher
     * @throws \Exception
     */
    public function __construct(
        InstallerInterface $installer,
        BooterInterface $booter = null,
        EventInterface $eventDispatcher = null
    ) {
        $this->installer = $installer;

        $this->setBooter($booter);
        $this->setEventDispatchers($eventDispatcher);
    }

    /**
     * @param $booter
     * @throws \Exception
     */
    protected function setBooter($booter)
    {
        if ($booter && is_a($booter, BooterInterface::class)) {
            $this->booter = $booter;
            $this->booter->setManager($this);
        } elseif ($booter) {
            throw new \Exception('Booter must implement BooterInterface');
        }
    }

    /**
     * @param $eventDispatcher
     * @throws \Exception
     */
    protected function setEventDispatchers($eventDispatcher)
    {
        if ($eventDispatcher && is_a($eventDispatcher, EventInterface::class)) {
            $this->eventDispatcher = $eventDispatcher;

            if ($this->booter) {
                $this->booter->setEventDispatcher($this->eventDispatcher);
            }

            $this->installer->setEventDispatcher($this->eventDispatcher);
        } elseif ($eventDispatcher) {
            throw new \Exception('Dispatcher must implement EventInterface');
        }
    }

    /**
     * @return InstallerInterface
     */
    public function installer()
    {
        return $this->installer;
    }

    /**
     * @return EventInterface
     */
    public function eventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return [
            'installed' => $this->getInstalled(),
            'uninstalled' => $this->getUninstalled(),
        ];
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getInstalled()
    {
        $extensions = [];

        foreach ($this->installer()->getInstalled() as $extension) {
            $extensions[$extension] = $this->get($extension);
        }

        return $extensions;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getUninstalled()
    {
        $extensions = [];

        foreach ($this->installer()->getUninstalled() as $extension) {
            $extensions[$extension] = $this->get($extension);
        }

        return $extensions;
    }

    /**
     * @return array
     */
    public function getRegistered()
    {
        return array_keys($this->extensions);
    }

    /**
     * @param $name
     * @param \Closure $plugin
     */
    public function add($name, $plugin)
    {
        $this->extensions[$name] = $plugin;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    protected function instantiate($name)
    {
        $instance = $this->extensions[$name]();

        if (! $instance) {
            throw new \Exception("Extension '{$name}' is not instantiable.");
        }

        return $instance;
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function get($name)
    {
        if (isset($this->extensionInstances[$name])) {
            return $this->extensionInstances[$name];
        }

        throw new \Exception("Extension '{$name}' was not registered.");
    }

    /**
     * @param $name
     * @return mixed
     */
    public function isInstalled($name)
    {
        return $this->installer->isInstalled($name);
    }

    public function boot()
    {
        if ($this->booted) {
            return;
        }

        foreach ($this->extensions as $name => $closure) {
            $instance = $this->instantiate($name);

            if ($this->booter) {
                if ($this->hasDispatcher()) {
                    $this->eventDispatcher->fire('before.boot.'.$name, [$instance]);
                }

                $this->booter->boot($instance, $name);

                if ($this->hasDispatcher()) {
                    $this->eventDispatcher->fire('after.boot.'.$name, [$instance]);
                }
            }

            $this->extensionInstances[$name] = $instance;
        }

        $this->installer->handleExtensionChanges(array_keys($this->extensions));

        $this->booted = true;
    }
}
