<?php

namespace SimpleSAML\Test\TwigConfigurableI18n\Twig;

use PHPUnit\Framework\TestCase;
use SimpleSAML\TwigConfigurableI18n\Twig\Environment;
use Twig\Loader\FilesystemLoader;

class EnvironmentTest extends TestCase
{
    /**
     * @covers \SimpleSAML\TwigConfigurableI18n\Twig\Environment::getOptions()
     * @return void
     */
    public function testOptions(): void
    {
        $loader = new FilesystemLoader();
        $options = [1, 'testcase', 1.0, [], $loader];
        $env = new Environment($loader, $options);
        $this->assertEquals($env->getOptions(), $options);
    }
}
