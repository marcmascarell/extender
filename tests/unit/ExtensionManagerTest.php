<?php

class ExtensionManagerTest extends \PHPUnit_Framework_TestCase
{
    const CONFIG_FILE = 'extensions-test.php';

    /**
     * @var \Mascame\Extender\Installer\InstallerInterface
     */
    protected $installer;

    protected $registeredExtensions = ['Maria', 'Jose', 'Eva', 'Adan'];

    public function setup()
    {
        parent::setUp();

        $this->prepareFile();

        $this->installer = new \Mascame\Extender\Installer\FileInstaller(
            new \Mascame\Extender\Installer\FileWriter(),
            $this->getConfigFile()
        );
    }

    public function tearDown()
    {
        $this->deleteFile();

        parent::tearDown();
    }

    protected function getConfigFile()
    {
        return __DIR__.'/../_data/'.(self::CONFIG_FILE);
    }

    protected function prepareFile()
    {
        $file = $this->getConfigFile();

        if (! file_exists($this->getConfigFile())) {
            file_put_contents($file, '<?php return [ "installed" => ["remove-me" => ""] ];');
        }
    }

    protected function deleteFile()
    {
        @unlink($this->getConfigFile());
    }

    public function test_installer_removes_not_registered_extensions()
    {
        $config = $this->installer->getConfig();

        $this->assertTrue(isset($config['installed']['remove-me']));

        $this->installer->handleExtensionChanges($this->registeredExtensions);

        $config = $this->installer->getConfig();
        $this->assertFalse(isset($config['installed']['remove-me']));
    }

    public function test_extension_uninstalls()
    {
        $this->installer->handleExtensionChanges($this->registeredExtensions);

        $this->installer->install('Maria');
        $this->assertTrue($this->installer->isInstalled('Maria'));

        $this->installer->uninstall('Maria');
        $this->assertFalse($this->installer->isInstalled('Maria'));

        $this->installer->install('Jose');
        $this->assertTrue($this->installer->isInstalled('Jose'));

        $this->installer->uninstall('Jose');
        $this->assertFalse($this->installer->isInstalled('Jose'));
    }

    public function test_extension_installs()
    {
        $this->installer->handleExtensionChanges($this->registeredExtensions);

        $this->installer->install('Maria');
        $this->assertTrue($this->installer->isInstalled('Maria'));

        $this->installer->uninstall('Maria');
        $this->assertFalse($this->installer->isInstalled('Maria'));

        $this->installer->install('Jose');
        $this->assertTrue($this->installer->isInstalled('Jose'));
    }

    public function test_extension_registration()
    {
        $extensionNamespace = 'coconut';

        $pluginManager = new \Mascame\Extender\Manager($this->installer, new \Mascame\Extender\Booter\Booter());

        $pluginManager->add($extensionNamespace, function () {
            return new ExtensionManagerTest();
        });

        $registereds = $pluginManager->getRegistered();

        $this->assertTrue(in_array($extensionNamespace, $registereds));

        $pluginManager->boot();

        $config = $pluginManager->installer()->getConfig();

        $this->assertTrue(in_array($extensionNamespace, $config['uninstalled']));
    }
}
