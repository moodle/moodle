<?php

declare(strict_types=1);

namespace SimpleSAML\Test\Utils;

use InvalidArgumentException;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Utils\System;

/**
 * Tests for SimpleSAML\Utils\System.
 */
class SystemTest extends TestCase
{
    private const ROOTDIRNAME = 'testdir';

    private const DEFAULTTEMPDIR = 'tempdir';

    /** @var \org\bovigo\vfs\vfsStreamDirectory */
    protected $root;

    /** @var string */
    protected $root_directory;


    /**
     * @return void
     */
    public function setUp()
    {
        $this->root = vfsStream::setup(
            self::ROOTDIRNAME,
            null,
            [
                self::DEFAULTTEMPDIR => [],
            ]
        );
        $this->root_directory = vfsStream::url(self::ROOTDIRNAME);
    }


    /**
     * @covers \SimpleSAML\Utils\System::getOS
     * @test
     * @return void
     */
    public function testGetOSBasic(): void
    {
        $res = System::getOS();

        $this->assertInternalType("int", $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::resolvePath
     * @test
     * @return void
     */
    public function testResolvePathRemoveTrailingSlashes(): void
    {
        $base = "/base////";
        $path = "test";

        $res = System::resolvePath($path, $base);
        $expected = "/base/test";

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::resolvePath
     * @test
     * @return void
     */
    public function testResolvePathPreferAbsolutePathToBase(): void
    {
        $base = "/base/";
        $path = "/test";

        $res = System::resolvePath($path, $base);
        $expected = "/test";

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::resolvePath
     * @test
     * @return void
     */
    public function testResolvePathCurDirPath(): void
    {
        $base = "/base/";
        $path = "/test/.";

        $res = System::resolvePath($path, $base);
        $expected = "/test";

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::resolvePath
     * @test
     * @return void
     */
    public function testResolvePathParentPath(): void
    {
        $base = "/base/";
        $path = "/test/child/..";

        $res = System::resolvePath($path, $base);
        $expected = "/test";

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::resolvePath
     * @test
     * @return void
     */
    public function testResolvePathAllowsStreamWrappers(): void
    {
        $base = '/base/';
        $path = 'vfs://simplesaml';

        $res = System::resolvePath($path, $base);
        $expected = $path;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::resolvePath
     * @test
     * @return void
     */
    public function testResolvePathAllowsAwsS3StreamWrappers(): void
    {
        $base = '/base/';
        $path = 's3://bucket-name/key-name';

        $res = System::resolvePath($path, $base);
        $expected = $path;

        $this->assertEquals($expected, $res);
    }


    /**
     * @covers \SimpleSAML\Utils\System::writeFile
     * @test
     * @deprecated Test becomes obsolete as soon as the codebase is fully type hinted
     * @return void
     */
    public function testWriteFileInvalidArguments()
    {
        $this->expectException(InvalidArgumentException::class);

        /** @psalm-suppress NullArgument */
        System::writeFile(null, null, null);
    }


    /**
     * @covers \SimpleSAML\Utils\System::writeFile
     * @test
     * @return void
     */
    public function testWriteFileBasic(): void
    {
        $tempdir = $this->root_directory . DIRECTORY_SEPARATOR . self::DEFAULTTEMPDIR;
        $config = $this->setConfigurationTempDir($tempdir);

        $filename = $this->root_directory . DIRECTORY_SEPARATOR . 'test';

        System::writeFile($filename, '');

        $this->assertFileExists($filename);

        $this->clearInstance($config, Configuration::class);
    }


    /**
     * @covers \SimpleSAML\Utils\System::writeFile
     * @test
     * @return void
     */
    public function testWriteFileContents(): void
    {
        $tempdir = $this->root_directory . DIRECTORY_SEPARATOR . self::DEFAULTTEMPDIR;
        $config = $this->setConfigurationTempDir($tempdir);

        $filename = $this->root_directory . DIRECTORY_SEPARATOR . 'test';
        $contents = 'TEST';

        System::writeFile($filename, $contents);

        $res = file_get_contents($filename);
        $expected = $contents;

        $this->assertEquals($expected, $res);

        $this->clearInstance($config, Configuration::class);
    }


    /**
     * @covers \SimpleSAML\Utils\System::writeFile
     * @test
     * @return void
     */
    public function testWriteFileMode(): void
    {
        $tempdir = $this->root_directory . DIRECTORY_SEPARATOR . self::DEFAULTTEMPDIR;
        $config = $this->setConfigurationTempDir($tempdir);

        $filename = $this->root_directory . DIRECTORY_SEPARATOR . 'test';
        $mode = 0666;

        System::writeFile($filename, '', $mode);

        $res = $this->root->getChild('test')->getPermissions();
        $expected = $mode;

        $this->assertEquals($expected, $res);

        $this->clearInstance($config, Configuration::class);
    }


    /**
     * @covers \SimpleSAML\Utils\System::getTempDir
     * @test
     * @return void
     */
    public function testGetTempDirBasic(): void
    {
        $tempdir = $this->root_directory . DIRECTORY_SEPARATOR . self::DEFAULTTEMPDIR;
        $config = $this->setConfigurationTempDir($tempdir);

        $res = System::getTempDir();
        $expected = $tempdir;

        $this->assertEquals($expected, $res);
        $this->assertFileExists($res);

        $this->clearInstance($config, Configuration::class);
    }


    /**
     * @covers \SimpleSAML\Utils\System::getTempDir
     * @test
     * @return void
     */
    public function testGetTempDirNonExistant(): void
    {
        $tempdir = $this->root_directory . DIRECTORY_SEPARATOR . 'nonexistant';
        $config = $this->setConfigurationTempDir($tempdir);

        $res = System::getTempDir();
        $expected = $tempdir;

        $this->assertEquals($expected, $res);
        $this->assertFileExists($res);

        $this->clearInstance($config, Configuration::class);
    }


    /**
     * @covers \SimpleSAML\Utils\System::getTempDir
     * @test
     * @return void
     */
    public function testGetTempDirBadPermissions(): void
    {
        $tempdir = $this->root_directory . DIRECTORY_SEPARATOR . self::DEFAULTTEMPDIR;
        $config = $this->setConfigurationTempDir($tempdir);

        chmod($tempdir, 0440);

        $this->expectException(Error\Exception::class);
        System::getTempDir();

        $this->clearInstance($config, Configuration::class);
    }


    /**
     * @param string $directory
     * @return \SimpleSAML\Configuration
     */
    private function setConfigurationTempDir(string $directory): Configuration
    {
        $config = Configuration::loadFromArray([
            'tempdir' => $directory,
        ], '[ARRAY]', 'simplesaml');

        return $config;
    }


    /**
     * @param \SimpleSAML\Configuration $service
     * @param class-string $className
     * @return void
     */
    protected function clearInstance(Configuration $service, string $className): void
    {
        $reflectedClass = new ReflectionClass($className);
        $reflectedInstance = $reflectedClass->getProperty('instance');
        $reflectedInstance->setAccessible(true);
        $reflectedInstance->setValue($service, null);
        $reflectedInstance->setAccessible(false);
    }
}
