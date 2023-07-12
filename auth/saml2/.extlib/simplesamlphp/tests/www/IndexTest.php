<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Web;

use PHPUnit\Framework\TestCase;
use SimpleSAML\Test\BuiltInServer;

/**
 * Simple test for the www/index.php script.
 *
 * This test is intended mostly as a demonstration of how to test the public web interface in SimpleSAMLphp.
 *
 * @author Jaime Pérez Crespo <jaime.perez@uninett.no>
 * @package SimpleSAMLphp
 */
class IndexTest extends TestCase
{
    /**
     * @var \SimpleSAML\Test\BuiltInServer
     */
    protected $server;

    /**
     * @var string
     */
    protected $server_addr;

    /**
     * @var int
     */
    protected $server_pid;

    /**
     * @var string
     */
    protected $shared_file;


    /**
     * The setup method that is run before any tests in this class.
     * @return void
     */
    protected function setup()
    {
        $this->server = new BuiltInServer('configLoader');
        $this->server_addr = $this->server->start();
        $this->server_pid = $this->server->getPid();

        $this->shared_file = sys_get_temp_dir() . '/' . $this->server_pid . '.lock';
        @unlink($this->shared_file); // remove it if it exists
    }


    /**
     * @param array $config
     * @return void
     */
    protected function updateConfig(array $config)
    {
        @unlink($this->shared_file);
        $config = "<?php\n\$config = " . var_export($config, true) . ";\n";
        file_put_contents($this->shared_file, $config);
    }


    /**
     * A simple test to make sure the index.php file redirects appropriately to the right URL.
     * @return void
     */
    public function testRedirection()
    {
        // test most basic redirection
        $this->updateConfig([
                'baseurlpath' => 'http://example.org/simplesaml/'
        ]);
        $resp = $this->server->get('/index.php', [], [
            CURLOPT_FOLLOWLOCATION => 0,
        ]);
        $this->assertEquals('302', $resp['code']);
        $this->assertEquals(
            'http://example.org/simplesaml/module.php/core/frontpage_welcome.php',
            $resp['headers']['Location']
        );

        // test non-default path and https
        $this->updateConfig([
            'baseurlpath' => 'https://example.org/'
        ]);
        $resp = $this->server->get('/index.php', [], [
            CURLOPT_FOLLOWLOCATION => 0,
        ]);
        $this->assertEquals('302', $resp['code']);
        $this->assertEquals(
            'https://example.org/module.php/core/frontpage_welcome.php',
            $resp['headers']['Location']
        );

        // test URL guessing
        $this->updateConfig([
            'baseurlpath' => '/simplesaml/'
        ]);
        $resp = $this->server->get('/index.php', [], [
            CURLOPT_FOLLOWLOCATION => 0,
        ]);
        $this->assertEquals('302', $resp['code']);
        $this->assertEquals(
            'http://' . $this->server_addr . '/simplesaml/module.php/core/frontpage_welcome.php',
            $resp['headers']['Location']
        );
    }


    /**
     * The tear down method that is executed after all tests in this class.
     * @return void
     */
    protected function tearDown()
    {
        unlink($this->shared_file);
        $this->server->stop();
    }
}
