<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Test for s3_client class.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
namespace tool_s3_fs;
use Aws\CommandInterface;
use Aws\MockHandler;
use Aws\Result;
use Aws\S3\Exception\S3Exception;
use GuzzleHttp\Psr7\Response;
use local_aws_sdk\aws_sdk;
use org\bovigo\vfs\vfsStream;
use Psr\Http\Message\RequestInterface;
use tool_s3_fs\config;
use tool_s3_fs\s3_client;
use tool_s3_fs\stream;

/**
 * Test for s3_client class.
 *
 * @package   tool_s3_fs
 * @copyright Copyright (c) 2017 Open LMS (https://www.openlms.net)
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class s3_client_test extends \basic_testcase {

    protected function setUp(): void {
        aws_sdk::autoload();
    }

    /**
     * @param MockHandler $handler
     * @return s3_client
     */
    protected function create_client(MockHandler $handler) {
        return new s3_client([
            'version'     => config::S3_VERSION,
            'region'      => 'us-west-2',
            'handler'     => $handler,
            'retries'     => 0,
            'credentials' => ['key' => 'key', 'secret' => 'secret']
        ], 'phpunit', 'filedir');
    }

    /**
     * Test get stream.
     */
    public function test_get_stream_path() {
        $client = $this->create_client(new MockHandler());
        $this->assertSame('s3://phpunit/filedir/ab/c1/abc123', $client->get_stream_path('abc123'));
    }

    /**
     * Test file size.
     */
    public function test_file_size() {
        $mock = new MockHandler();
        $mock->append(new Result(['ContentLength' => 1024]));

        $client = $this->create_client($mock);
        $this->assertSame(1024, $client->file_size('abc123'));
        $this->assertEmpty($mock->count());
    }

    /**
     * Test error handling of file size method.
     *
     * @dataProvider file_size_exception_provider
     * @param callable $exception
     */
    public function test_file_size_errors(callable $exception) {
        $this->expectException(\file_exception::class);

        $mock = new MockHandler();
        $mock->append($exception);

        $client = $this->create_client($mock);
        $client->file_size('abc123');
    }

    /**
     * Test error codes that just mean the file does not exist yet.
     */
    public function test_file_size_ok_errors() {
        $mock = new MockHandler();
        $mock->append(function (CommandInterface $cmd, RequestInterface $req) {
            return new S3Exception('Mock exception', $cmd, ['code' => 'NoSuchKey']);
        });
        $mock->append(function (CommandInterface $cmd, RequestInterface $req) {
            return new S3Exception('Mock exception', $cmd, ['code' => 'NotFound']);
        });

        $client = $this->create_client($mock);
        $this->assertNull($client->file_size('abc123'));
        $this->assertNull($client->file_size('abc123'));
        $this->assertEmpty($mock->count());
    }

    /**
     * Test download.
     */
    public function test_download() {
        $mock = new MockHandler();
        $mock->append(new Result());

        $destination = vfsStream::setup('root')->url().'/file1';

        $client = $this->create_client($mock);
        $client->download('abc123', $destination);

        $this->assertEmpty($mock->count());
        $command = $mock->getLastCommand();
        $this->assertSame('GetObject', $command->getName());
        $this->assertSame('phpunit', $command['Bucket']);
        $this->assertSame('filedir/ab/c1/abc123', $command['Key']);
    }

    /**
     * Test upload.
     */
    public function test_upload() {
        $mock = new MockHandler();
        $mock->append(new Result());

        $client = $this->create_client($mock);
        $client->upload('abc123', stream::from_string('content'));

        $this->assertEmpty($mock->count());
        $command = $mock->getLastCommand();
        $this->assertSame('PutObject', $command->getName());
        $this->assertSame('phpunit', $command['Bucket']);
        $this->assertSame('filedir/ab/c1/abc123', $command['Key']);
    }

    /**
     * Test delete.
     */
    public function test_delete() {
        $mock = new MockHandler();
        $mock->append(new Result());

        $client = $this->create_client($mock);
        $client->delete('abc123');

        $this->assertEmpty($mock->count());
        $command = $mock->getLastCommand();
        $this->assertSame('DeleteObject', $command->getName());
        $this->assertSame('phpunit', $command['Bucket']);
        $this->assertSame('filedir/ab/c1/abc123', $command['Key']);
    }

    /**
     * Test hash collision.
     */
    public function test_hash_collision() {
        $mock = new MockHandler();
        $mock->append(new Result());
        $mock->append(new Result(['ContentLength' => 10]));  // API does a HeadObject.
        $mock->append(new Result());

        $client = $this->create_client($mock);
        $client->hash_collision('abc123', stream::from_string('content'));

        $this->assertEmpty($mock->count());

        $command = $mock->getLastCommand();
        $this->assertSame('CopyObject', $command->getName());
        $this->assertSame('phpunit', $command['Bucket']);
        $this->assertSame('filedir/jackpot/abc123_2', $command['Key']);
    }

    /**
     * @return array
     */
    public function file_size_exception_provider() {
        return [
            [
                function (CommandInterface $cmd, RequestInterface $req) {
                    return new S3Exception('Mock exception', $cmd, [
                        'response' => new Response(500)
                    ]);
                }
            ],
            [
                function (CommandInterface $cmd, RequestInterface $req) {
                    return new S3Exception('Mock exception', $cmd, ['code' => 'Hodor']);
                }
            ],
        ];
    }
}
