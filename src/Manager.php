<?php namespace Mascame\Extender;

use Mascame\Extender\Booter\BooterInterface;
use Mascame\Extender\Event\EventInterface;
use Mascame\Extender\Installer\InstallerInterface;

class Manager implements ManagerInterface {

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
     * Event name => method name
     *
     * @var array
     */
    protected $events = [
        'install' => 'install',
        'uninstall' => 'uninstall',
    ];

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
    )
    {
        $this->installer = $installer;

        $this->setBooter($booter);
        $this->setEventDispatcher($eventDispatcher);
    }

    /**
     * @param $booter
     * @throws \Exception
     */
    protected function setBooter($booter) {
        if ($booter && is_a($booter, BooterInterface::class)) {
            $this->booter = $booter;
            $this->booter->setManager($this);
        } else if ($booter) {
            throw new \Exception('Booter must implement BooterInterface');
        }
    }

    /**
     * @param $eventDispatcher
     * @throws \Exception
     */
    protected function setEventDispatcher($eventDispatcher) {
        if ($eventDispatcher && is_a($eventDispatcher, EventInterface::class)) {
            $this->eventDispatcher = $eventDispatcher;

            if ($this->booter) {
                $this->booter->setEventDispatcher($this->eventDispatcher);
            }

            $this->installer->setEventDispatcher($this->eventDispatcher);
        } else if ($eventDispatcher) {
            throw new \Exception('Dispatcher must implement EventInterface');
        }
    }

    /**
     * @return array
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param array $events
     */
    public function setEvents($events)
    {
        $this->events = $events;
    }

    /**
     * @return InstallerInterface
     */
    public function installer() {
        return $this->installer;
    }

    /**
     * @return array
     */
    public function getAll()
    {
        return $this->extensionInstances;
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
    public function add($name, \Closure $plugin)
    {
        $this->extensions[$name] = $plugin;
    }

    /**
     * @param $name
     * @return mixed
     */
    protected function instantiate($name) {
        return $this->extensions[$name]();
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        if (isset($this->extensionInstances['installed'][$name])) {
            return $this->extensionInstances['installed'][$name];
        }

        if (isset($this->extensionInstances['uninstalled'][$name])) {
            return $this->extensionInstances['uninstalled'][$name];
        }

        throw new \Exception("Extension {$name} was not instantiated or registered");
    }

    /**
     * @param $name
     * @return mixed
     */
    public function isInstalled($name) {
        return $this->installer->isInstalled($name);
    }

    /**
     *
     */
    public function boot()
    {
        if ($this->booted) return;

        foreach ($this->extensions as $name => $closure) {
            $isInstalled = $this->isInstalled($name);

            $instance = $this->instantiate($name);

            if ($this->booter) {
                $this->booter->beforeBooting($instance, $name);

                if ($this->eventDispatcher) {
                    $this->booter->addListeners($this->getEvents(), $instance, $name);
                }

                $this->booter->boot($instance, $name);
            }

            if ($isInstalled) {
                $this->extensionInstances['installed'][$name] = $instance;
            } else {
                $this->extensionInstances['uninstalled'][$name] = $instance;
            }
        }

        $this->installer->handleExtensionChanges(array_keys($this->extensions));

        $this->booted = true;
    }

}
