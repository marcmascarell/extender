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
     * @var array
     */
    protected $arrayBuilderConfig = [
        'indexes' => false
    ];

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
        if (!isset($config[self::STATUS_INSTALLED])) $config[self::STATUS_INSTALLED] = [];
        if (!isset($config[self::STATUS_UNINSTALLED])) $config[self::STATUS_UNINSTALLED] = [];

        return $config;
    }

    /**
     * @param $extensions
     */
    public function handleExtensionChanges($extensions)
    {
        if (empty($extensions)) return;

        $configExtensions = array_merge($this->config[self::STATUS_INSTALLED], $this->config[self::STATUS_UNINSTALLED]);
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
        return isset($this->config[self::STATUS_INSTALLED]) && in_array($name, $this->config[self::STATUS_INSTALLED]);
    }

    /**
     * @param $extension
     * @return bool
     * @throws \Exception
     */
    public function install($extension) {
        return $this->action($extension, self::ACTION_INSTALL);
    }

    /**
     * @param $extension
     * @return bool
     * @throws \Exception
     */
    public function uninstall($extension) {
        return $this->action($extension, self::ACTION_UNINSTALL);
    }

    /**
     * @param $extension
     * @param $action
     * @return bool
     * @throws \Exception
     */
    protected function action($extension, $action)
    {
        $from = self::STATUS_INSTALLED;
        $to = self::STATUS_UNINSTALLED;

        if ($action == self::ACTION_INSTALL) {
            $from = self::STATUS_UNINSTALLED;
            $to = self::STATUS_INSTALLED;
        }

        if (isset($this->config[$to])) {
            if (in_array($extension, $this->config[$to])) {
                return false;
            }
        }

        if ($this->hasDispatcher()) {
            $this->fire("before.{$action}.{$extension}", [$extension]);
        }

        $result = $this->makeOperation($this->config, $extension, $from, $to);

        if ($this->hasDispatcher()) {
            $this->fire("after.{$action}.{$extension}", [$extension]);
        }

        return $result;
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

            $result = $this->writer->put($file, (new Arrayer\Builder\ArrayBuilder($extensions, $this->arrayBuilderConfig))->getContent());

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
            $this->config[self::STATUS_UNINSTALLED][] = $name;
        }

        foreach ($removed as $name) {
            if (($key = array_search($name, $this->config[self::STATUS_INSTALLED])) !== false) {
                unset($this->config[self::STATUS_INSTALLED][$key]);
                continue;
            }

            if (($key = array_search($name, $this->config[self::STATUS_UNINSTALLED])) !== false) {
                unset($this->config[self::STATUS_UNINSTALLED][$key]);
                continue;
            }
        }

        $builder = new Arrayer\Builder\ArrayBuilder($this->config, $this->arrayBuilderConfig);
        $this->writer->put($this->configFile, $builder->getContent());
    }

    /**
     * @return array
     */
    public function getInstalled()
    {
        return isset($this->config[self::STATUS_INSTALLED]) ? $this->config[self::STATUS_INSTALLED] : [];
    }

    /**
     * @return array
     */
    public function getUninstalled()
    {
        return isset($this->config[self::STATUS_UNINSTALLED]) ? $this->config[self::STATUS_UNINSTALLED] : [];
    }
}
