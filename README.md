BootTests
=========

__Configuração no arquivo `application.config.php`__

```php

return array(
    'modules' => array(
        'YourModulesInExecution'
    ),
    'module_listener_options' => array(
        'module_paths' => array(
            './module',
            './vendor',
        ),
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
    ),
	'module_tests' => [
		'YourModulesForTests',
		'Exemplo1',
		'Exemplo2',
		'Exemplo3'
	]
);


```

__Estrutura de pastas para execução dos tests__

    YourModule
        config
        src
        tests
            src => yourTests
            Bootstrap.php => arquivo de inicialização
            phpunit.xml => arquivo de configuração dos tests
        db => pasta para instruções SQL
            create.sql
            drop.sql
        view
        Module.php


__Arquivo de inicialização do PHPUnit__


    touch /var/www/yourproject/module/YourModule/tests/Bootstrap.php


```php

namespace YourModule;

require_once(getcwd() . '/../../../vendor/hiamina/son-base/src/BootTests/Test/AbstractBootstrap.php');

use BootTests\Test\AbstractBootstrap;

error_reporting(E_ALL | E_STRICT);
chdir(__DIR__);

class Bootstrap extends AbstractBootstrap {}
Bootstrap::init();

```

__Arquivo de configuração dos tests PHPUnit__


    touch /var/www/yourproject/module/YourModule/tests/phpunit.xml


Para mais detalhes sobre a configuração desse arquivo analisar na documentação do PHPUnit
[Documentação phpunit.de](https://phpunit.de/manual/current/en/organizing-tests.html#organizing-tests.xml-configuration)


```php
<phpunit
	backupGlobals="false"
	backupStaticAttributes="false"
	bootstrap="Bootstrap.php"
	cacheTokens="false"
	colors="true"
	convertErrorsToExceptions="true"
	convertNoticesToExceptions="true"
	convertWarningsToExceptions="true"
	forceCoversAnnotation="false"
	mapTestClassNameToCoveredClassName="false"
	processIsolation="false"
	stopOnError="true"
	stopOnFailure="true"
	stopOnIncomplete="false"
	stopOnSkipped="false"
	timeoutForSmallTests="1"
	timeoutForMediumTests="10"
	timeoutForLargeTests="60"
	verbose="true">

	<!-- MUDAR O NOME DOS MODULOS -->
	<testsuites>
		<testsuite name="">
            <directory>./src/YourModule/Controller</directory>
            <directory>./src/YourModule/Filter</directory>
            <directory>./src/YourModule/Form</directory>
		</testsuite>
	</testsuites>

	<!-- CODE COVERAGE CONFIGURATION -->
	<filter>
		<whitelist>
			<directory suffix=".php">../</directory>
			<exclude>
				<file>../Module.php</file>
				<directory>../config</directory>
				<directory>../tests</directory>
			</exclude>
		</whitelist>
	</filter>

	<logging>
		<!-- COVERAGE == COBERTURA DE CODIGO -->
		<log type="coverage-html" target="_reports/coverage" title="" charset="UTF-8" yui="true" highlight="true" lowUpperBound="75" highLowerBound="90" />
		<log type="testdox-text" target="_reports/testdox/executed.txt" />
	</logging>
</phpunit>

```

__Execução dos tests__

    cd /var/www/yourproject/module/YourModule/tests/
    php ../../../vendor/bin/phpunit (Pode ser executado de diversas maneiras, somente ilustrando uma das maneiras)
