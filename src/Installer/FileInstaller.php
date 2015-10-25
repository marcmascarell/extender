<?php namespace Mascame\Extender\Installer;

use Mascame\Arrayer;

/**
 * Class FileInstaller
 * @package Mascame\Extender\Installer
 */
class FileInstaller extends AbstractInstaller implements InstallerInterface {

    /**
     * @var string
     */
    public $configFile;

    /**
     * @var FileWriterInterface
     */
    protected $writer;

    /**
     * @var array|mixed
     */
    protected $config = [];

    /**
     * @param FileWriterInterface $writer
     * @param $configPath
     */
    public function __construct(FileWriterInterface $writer, $configPath) {
        $this->writer = $writer;
        $this->configFile = $configPath;
        $this->config = $this->getConfig();
    }

    /**
     * @return array|mixed
     */
    public function getConfig() {
        if (! empty($this->config)) return $this->config;

        $config = require $this->configFile;

        // Set defaults if empty
        if (!isset($config['installed'])) $config['installed'] = [];
        if (!isset($config['uninstalled'])) $config['uninstalled'] = [];

        return $config;
    }

    /**
     * @param $extensions
     * @return bool
     */
    public function handleExtensionChanges($extensions)
    {
        if (empty($extensions)) return;

        $configExtensions = array_merge($this->config['installed'], $this->config['uninstalled']);
        $added = array_diff($extensions, $configExtensions);
        $removed = array_diff($configExtensions, $extensions);

        $needsUpdate = (! empty($added) || ! empty($removed) || count($configExtensions) != count($extensions));

        if ($needsUpdate) $this->generateConfigFile($added, $removed);
    }

    /**
     * @param $name
     * @return bool
     */
    public function isInstalled($name) {
        return isset($this->config['installed']) && in_array($name, $this->config['installed']);
    }

    /**
     * @param $extension
     * @return bool
     * @throws \Exception
     */
    public function install($extension) {
        if (! $this->action($extension, 'install')) return false;

        if ($this->isDispatchable()) {
            $this->fire("install.{$extension}");
        }

        return true;
    }

    /**
     * @param $extension
     * @return bool
     * @throws \Exception
     */
    public function uninstall($extension) {
        if (! $this->action($extension, 'uninstall')) return false;

        if ($this->isDispatchable()) {
            $this->fire("uninstall.{$extension}");
        }

        return true;
    }

    /**
     * @param $extension
     * @param $operation
     * @return bool
     * @throws \Exception
     */
    protected function action($extension, $operation)
    {
        $from = 'installed';
        $to = 'uninstalled';

        if ($operation == 'install') {
            $from = 'uninstalled';
            $to = 'installed';
        }

        if (isset($this->config[$to])) {
            if (in_array($extension, $this->config[$to])) {
                return false;
            }
        }

        return $this->makeOperation($this->config, $extension, $from, $to);
    }

    /**
     * @param $extensions
     * @param $plugin
     * @param $from
     * @param $to
     * @return bool
     * @throws \Exception
     */
    protected function makeOperation($extensions, $plugin, $from, $to)
    {
        try {
            $this->modifyFile($this->configFile, $extensions, $plugin, $from, $to);
        } catch (\Exception $e) {
            throw new \Exception("Failed to modify plugins config");
        }

        return true;
    }

    /**
     * @param $file
     * @param $extensions
     * @param $plugin
     * @param $from
     * @param $to
     * @return bool
     * @throws \Exception
     */
    protected function modifyFile($file, $extensions, $plugin, $from, $to)
    {
        if (($key = array_search($plugin, $extensions[$from])) !== false) {
            unset($extensions[$from][$key]);
            $extensions[$to][] = $plugin;

            if (!file_exists($file)) {
                throw new \Exception("File not found {$file}");
            }

            $result = $this->writer->put($file, (new Arrayer\Builder\ArrayBuilder($extensions))->getContent());

            if ($result) $this->config = $extensions;
        }

        return false;
    }

    /**
     * @param $added
     * @param $removed
     */
    protected function generateConfigFile($added, $removed)
    {
        foreach ($added as $name) {
            $this->config['uninstalled'][] = $name;
        }

        foreach ($removed as $name) {
            if (($key = array_search($name, $this->config['installed'])) !== false) {
                unset($this->config['installed'][$key]);
                continue;
            }

            if (($key = array_search($name, $this->config['uninstalled'])) !== false) {
                unset($this->config['uninstalled'][$key]);
                continue;
            }
        }

        $builder = new Arrayer\Builder\ArrayBuilder($this->config);
        $this->writer->put($this->configFile, $builder->getContent());
    }

    /**
     * @return array
     */
    public function getInstalled()
    {
        return isset($this->config['installed']) ? $this->config['installed'] : [];
    }

    /**
     * @return array
     */
    public function getUninstalled()
    {
        return isset($this->config['uninstalled']) ? $this->config['uninstalled'] : [];
    }
}
