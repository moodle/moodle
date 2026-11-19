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

namespace smsgateway_aws;

use Aws\Command;
use Aws\Exception\AwsException;
use Aws\Exception\CredentialsException;
use Aws\MockHandler;
use Aws\Result;
use Aws\Sns\SnsClient;
use core_sms\message;
use core_sms\message_status;
use smsgateway_aws\local\sns_client_factory;

/**
 * AWS SMS gateway tests.
 *
 * @package    smsgateway_aws
 * @category   test
 * @copyright  2024 Safat Shahin <safat.shahin@moodle.com>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @covers     \smsgateway_aws\gateway
 * @covers     \smsgateway_aws\local\service\aws_sns
 */
final class gateway_test extends \advanced_testcase {
    /**
     * Updating message statuses only accepts messages sent by this gateway.
     */
    public function test_update_message_status(): void {
        $this->resetAfterTest();

        $config = new \stdClass();
        $config->api_key = 'test_api_key';

        $manager = \core\di::get(\core_sms\manager::class);
        $gw = $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'aws',
            enabled: true,
            config: $config,
        );
        $othergw = $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'aws',
            enabled: true,
            config: $config,
        );

        $message = new message(
            recipientnumber: '1234567890',
            content: 'Hello, world!',
            component: 'smsgateway_aws',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
            gatewayid: $gw->id,
        );
        $message2 = new message(
            recipientnumber: '1234567890',
            content: 'Hello, world!',
            component: 'smsgateway_aws',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
            gatewayid: $gw->id,
        );

        $updatedmessages = $gw->update_message_statuses([$message, $message2]);
        $this->assertEquals([$message, $message2], $updatedmessages);

        $this->expectException(\coding_exception::class);
        $othergw->update_message_status($message);
    }

    /**
     * Sending through the manager publishes the message to SNS with the gateway config.
     */
    public function test_send(): void {
        $this->resetAfterTest();

        $mock = new MockHandler([new Result(['MessageId' => 'test-message-id'])]);
        $this->mock_sns_client($mock);

        $manager = \core\di::get(\core_sms\manager::class);
        $gw = $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'aws',
            enabled: true,
            config: $this->get_config(),
        );

        $message = $manager->send(
            recipientnumber: '0412 345 678',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
        $this->assertEquals($gw->id, $message->gatewayid);
        $this->assertEquals($message, $manager->get_message(['id' => $message->id]));

        $command = $mock->getLastCommand();
        $this->assertEquals('Publish', $command->getName());
        $this->assertEquals('Hello, world!', $command['Message']);
        // The country code from the gateway config replaces the leading 0.
        $this->assertEquals('+61412345678', $command['PhoneNumber']);
        $this->assertEquals('phpunit', $command['MessageAttributes']['AWS.SNS.SMS.SenderID']['StringValue']);
        $this->assertEquals('Transactional', $command['MessageAttributes']['AWS.SNS.SMS.SMSType']['StringValue']);

        // The request uses the region and credentials from the gateway config.
        $request = $mock->getLastRequest();
        $this->assertEquals('sns.ap-southeast-2.amazonaws.com', $request->getUri()->getHost());
        $this->assertStringContainsString('Credential=test_api_key/', $request->getHeaderLine('Authorization'));
    }

    /**
     * The gateway sends with its own config, not a copy read from the database.
     */
    public function test_send_uses_gateway_config(): void {
        $this->resetAfterTest();

        $mock = new MockHandler([new Result(['MessageId' => 'test-message-id'])]);
        $this->mock_sns_client($mock);

        // This gateway is not saved, so its config only exists in the instance.
        $gw = new gateway(
            enabled: true,
            name: 'aws',
            config: json_encode($this->get_config()),
        );
        $message = $gw->send(new message(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            issensitive: false,
        ));

        $this->assertEquals(message_status::GATEWAY_SENT, $message->status);
        $this->assertEquals('+447123456789', $mock->getLastCommand()['PhoneNumber']);
        $this->assertEquals('sns.ap-southeast-2.amazonaws.com', $mock->getLastRequest()->getUri()->getHost());
    }

    /**
     * An error from the AWS SDK marks the message as failed.
     *
     * @param \Exception $exception The exception returned by the mocked request.
     * @param string $region The configured AWS region.
     * @param string|null $lastcommand The name of the last command sent, or null if nothing was sent.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('aws_error_provider')]
    public function test_send_aws_error(\Exception $exception, string $region, ?string $lastcommand): void {
        $this->resetAfterTest();

        $mock = new MockHandler([$exception]);
        $this->mock_sns_client($mock);

        $config = $this->get_config();
        $config->api_region = $region;

        $manager = \core\di::get(\core_sms\manager::class);
        $manager->create_gateway_instance(
            classname: gateway::class,
            name: 'aws',
            enabled: true,
            config: $config,
        );

        $message = $manager->send(
            recipientnumber: '+447123456789',
            content: 'Hello, world!',
            component: 'core',
            messagetype: 'test',
            recipientuserid: null,
            async: false,
        );

        $this->assertEquals(message_status::GATEWAY_FAILED, $message->status);
        $this->assertEquals($lastcommand, $mock->getLastCommand()?->getName());
    }

    /**
     * Data provider for test_send_aws_error.
     *
     * @return array
     */
    public static function aws_error_provider(): array {
        return [
            'AWS error' => [
                'exception' => new AwsException('Mocked AWS error', new Command('Publish')),
                'region' => 'ap-southeast-2',
                'lastcommand' => 'Publish',
            ],
            'No credentials found' => [
                'exception' => new CredentialsException('No credentials found'),
                'region' => 'ap-southeast-2',
                'lastcommand' => 'Publish',
            ],
            'Invalid region' => [
                'exception' => new AwsException('Not sent', new Command('Publish')),
                'region' => 'bad region!',
                'lastcommand' => null,
            ],
            'Blank region' => [
                'exception' => new AwsException('Not sent', new Command('Publish')),
                'region' => '',
                'lastcommand' => null,
            ],
        ];
    }

    /**
     * Get a gateway config for the SNS service.
     *
     * @return \stdClass
     */
    private function get_config(): \stdClass {
        return (object) [
            'gateway' => 'aws_sns',
            'api_key' => 'test_api_key',
            'api_secret' => 'test_api_secret',
            'api_region' => 'ap-southeast-2',
            'countrycode' => '61',
        ];
    }

    /**
     * Make the gateway use an SNS client that answers from the mock handler instead of AWS.
     *
     * @param MockHandler $handler
     */
    private function mock_sns_client(MockHandler $handler): void {
        \core\di::set(sns_client_factory::class, new class ($handler) extends sns_client_factory {
            /**
             * Constructor.
             *
             * @param MockHandler $handler The handler that returns the queued results.
             */
            public function __construct(
                /** @var MockHandler The handler that returns the queued results. */
                private readonly MockHandler $handler,
            ) {
            }

            #[\Override]
            public function create_client(array $params): SnsClient {
                return parent::create_client(array_merge($params, ['handler' => $this->handler]));
            }
        });
    }
}
