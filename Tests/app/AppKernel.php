<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Kunstmaan\TranslatorBundle\Tests\app;

use Psr\Log\NullLogger;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * App Test Kernel for functional tests.
 *
 * @see https://github.com/symfony/symfony/blob/25f9ec6a541a6820888721d63e09f94e02628a21/src/Symfony/Bundle/FrameworkBundle/Tests/Functional/app/AppKernel.php
 */
class AppKernel extends Kernel
{
    private $varDir;

    private $testCase;

    private $rootConfig;

    public function __construct($varDir, $testCase, $rootConfig, $environment, $debug)
    {
        if (!is_dir(__DIR__ . '/' . $testCase)) {
            throw new \InvalidArgumentException(sprintf('The test case "%s" does not exist.', $testCase));
        }
        $this->varDir = $varDir;
        $this->testCase = $testCase;

        $fs = new Filesystem();
        if (!$fs->isAbsolutePath($rootConfig) && !file_exists($rootConfig = __DIR__ . '/' . $testCase . '/' . $rootConfig)) {
            throw new \InvalidArgumentException(sprintf('The root config "%s" does not exist.', $rootConfig));
        }
        $this->rootConfig = $rootConfig;

        parent::__construct($environment, $debug);
    }

    /**
     * @return BundleInterface[]
     */
    public function registerBundles()
    {
        if (!file_exists($filename = $this->getProjectDir() . '/' . $this->testCase . '/bundles.php')) {
            throw new \RuntimeException(sprintf('The bundles file "%s" does not exist.', $filename));
        }

        return include $filename;
    }

    /**
     * @return string
     */
    public function getProjectDir()
    {
        return __DIR__;
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return sys_get_temp_dir() . '/' . $this->varDir . '/' . $this->testCase . '/cache/' . $this->environment;
    }

    /**
     * @return string
     */
    public function getLogDir()
    {
        return sys_get_temp_dir() . '/' . $this->varDir . '/' . $this->testCase . '/logs';
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load($this->rootConfig);
    }

    protected function build(ContainerBuilder $container)
    {
        $container->register('logger', NullLogger::class);
    }

    public function serialize()
    {
        return serialize([$this->varDir, $this->testCase, $this->rootConfig, $this->getEnvironment(), $this->isDebug()]);
    }

    public function unserialize($str)
    {
        $a = unserialize($str);
        $this->__construct($a[0], $a[1], $a[2], $a[3], $a[4]);
    }

    /**
     * @return array
     */
    protected function getKernelParameters()
    {
        $parameters = parent::getKernelParameters();
        $parameters['kernel.test_case'] = $this->testCase;

        return $parameters;
    }
}
