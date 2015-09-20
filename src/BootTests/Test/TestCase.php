<?php

namespace BootTests\Test;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Service\ServiceManagerConfig;
use Zend\Mvc\MvcEvent;
use Doctrine\ORM\EntityManager;

chdir(__DIR__.'/../../../../../../');

class TestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * Módulos para serem testados
     * @var
     */
    protected $modulesForTests;

    /**
     * @var Zend\ServiceManager\ServiceManager
     */
    protected $serviceManager;

    /**
     * @return mixed
     */
    private function getModulesForTest()
    {
        return $this->modulesForTests;
    }

    /**
     * @param $modules
     */
    private function setModulesForTests($modules)
    {
        $this->modulesForTests = $modules;
    }

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var
     */
    protected $modules;

    /**
     * Método de configuração dos módulos que serão testados, configurando
     * as rotas, criado base de dados para rodar os tests.
     */
    public function setup()
    {
        parent::setup();

        $pathDir = getcwd()."/";
        $config = include $pathDir.'config/application.config.php';
        $this->setModulesForTests($config['module_tests']);

        $this->serviceManager = new ServiceManager(new ServiceManagerConfig(
            isset($config['service_manager']) ? $config['service_manager'] : []
        ));

        $this->serviceManager->setService('ApplicationConfig', $config);
        $this->serviceManager->setFactory('ServiceListener', 'Zend\Mvc\Service\ServiceListenerFactory');

        $moduleManager = $this->serviceManager->get('ModuleManager');
        $moduleManager->loadModules();
        $this->routes = [];
        $this->modules = $moduleManager->getModules();

        foreach ($this->getModulesForTest()  as $m)
        {
            $moduleConfig = include $pathDir.'module/' . ucfirst($m) . '/config/module.config.php';

            if (isset($moduleConfig['router']))
            {
                foreach ($moduleConfig['router']['routes'] as $key => $name)
                {
                    $this->routes[$key] = $name;
                }
            }

            $this->createDatabase($m);
        }

        $this->serviceManager->setAllowOverride(true);

        $this->application = $this->serviceManager->get('Application');
        $this->event = new MvcEvent();
        $this->event->setTarget($this->application);
        $this->event->setApplication($this->application)
            ->setRequest($this->application->getRequest())
            ->setResponse($this->application->getResponse())
            ->setRouter($this->serviceManager->get('Router'));

        $this->em = $this->serviceManager->get('Doctrine\ORM\EntityManager');
    }

    /**
     * Criação da base de dados para tests, sendo executado arquivo
     * SQL localizado dentro da pasta de cada module
     *
     * @param $module
     */
    public function createDatabase($module)
    {
        if (file_exists(getcwd().'/module/' . $module . '/db/create.sql'))
        {
            $sql = file_get_contents(getcwd().'/module/' . $module . '/db/create.sql');
            $this->getEm()->getConnection()->exec($sql);
            $this->getEm()->getConnection()->exec('SET FOREIGN_KEY_CHECKS = 0;');
        }
    }

    /**
     * Método tearDown chama parent::tearDown()
     * para ser executado as rotinas de remoção de tabelas do último test
     */
    public function tearDown()
    {
        parent::tearDown();

        foreach($this->getModulesForTest()  as $m)
        {
            if (file_exists(getcwd().'/module/' . $m . '/db/drop.sql'))
            {
                $sql = file_get_contents(getcwd().'/module/' . $m . '/db/drop.sql');
                $this->getEm()->getConnection()->exec($sql);
            }
        }
    }

    /**
     * @return mixed
     */
    public function getEm()
    {
        return $this->em = $this->serviceManager->get('Doctrine\ORM\EntityManager');
    }
}
