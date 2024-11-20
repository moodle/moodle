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

namespace communication_matrix;

use communication_matrix\local\spec\{v1p1, v1p2, v1p3, v1p4, v1p5, v1p6, v1p7};
use communication_matrix\tests\fixtures\mocked_matrix_client;
use core\http_client;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;

/**
 * A trait with shared tooling for handling matrix_client tests.
 *
 * @package    communication_matrix
 * @category   test
 * @copyright  2023 Andrew Lyons <andrew@nicols.co.uk>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
trait matrix_client_test_trait {
    public static function setUpBeforeClass(): void {
        parent::setUpBeforeClass();

        // Ensure that the mocked client is available.
        require_once(__DIR__ . '/fixtures/mocked_matrix_client.php');
    }

    public function setUp(): void {
        parent::setUp();

        // Reset the test client.
        mocked_matrix_client::reset_client();
    }

    public function tearDown(): void {
        parent::tearDown();

        // Reset the test client.
        mocked_matrix_client::reset_client();
    }

    /**
     * Get a mocked instance for a specific Matrix API version,
     *
     * @param string $version
     * @param array $historycontainer An array which will be filled with history for the mocked client.
     * @param MockHandler|null $mock A MockHandler object that can be appended to
     * @return matrix_client
     */
    protected function get_mocked_instance_for_version(
        string $version,
        array &$historycontainer = [],
        ?MockHandler $mock = null,
    ): matrix_client {
        // If no mock is provided, use get_mocked_http_client to create the mock and client.
        if ($mock === null) {
            ['mock' => $mock, 'client' => $client] = $this->get_mocked_http_client(
                history: $historycontainer
            );
        } else {
            // If mock is provided, create the handlerstack and history middleware.
            $handlerstack = HandlerStack::create($mock);
            $history = Middleware::history($historycontainer);
            $handlerstack->push($history);
            $client = new http_client(['handler' => $handlerstack]);
        }
        // Add the version response.
        $mock->append($this->get_mocked_version_response([$version]));

        mocked_matrix_client::set_client($client);

        $instance = mocked_matrix_client::instance(
            'https://example.com',
            'testtoken',
        );

        // Remove the request that is required to fetch the version from the history.
        array_shift($historycontainer);

        return $instance;
    }

    /**
     * Get a mocked response for the /versions well-known URI.
     *
     * @param array|null $versions
     * @param array|null $unstablefeatures
     * @return Response
     */
    protected function get_mocked_version_response(
        ?array $versions = null,
        ?array $unstablefeatures = null,
    ): Response {
        $data = (object) [
            "versions" => array_values(self::get_current_versions()),
            "unstable_features" => self::get_current_unstable_features(),
        ];

        if ($versions) {
            $data->versions = array_values($versions);
        }

        if ($unstablefeatures) {
            $data->unstable_features = $unstablefeatures;
        }

        return new Response(200, [], json_encode($data));
    }

    /**
     * A helper to get the current versions returned by synapse.
     *
     * @return array
     */
    protected static function get_current_versions(): array {
        return [
            v1p1::class => "v1.1",
            v1p2::class => "v1.2",
            v1p3::class => "v1.3",
            v1p4::class => "v1.4",
            v1p5::class => "v1.5",
            v1p6::class => "v1.6",
            v1p7::class => "v1.7",
        ];
    }

    /**
     * A helper to get the current unstable features returned by synapse.
     * @return array
     */
    protected static function get_current_unstable_features(): array {
        return [
            "org.matrix.label_based_filtering" => true,
            "org.matrix.e2e_cross_signing" => true,
            "org.matrix.msc2432" => true,
            "uk.half-shot.msc2666.query_mutual_rooms" => true,
            "io.element.e2ee_forced.public" => false,
            "io.element.e2ee_forced.private" => false,
            "io.element.e2ee_forced.trusted_private" => false,
            "org.matrix.msc3026.busy_presence" => false,
            "org.matrix.msc2285.stable" => true,
            "org.matrix.msc3827.stable" => true,
            "org.matrix.msc2716" => false,
            "org.matrix.msc3440.stable" => true,
            "org.matrix.msc3771" => true,
            "org.matrix.msc3773" => false,
            "fi.mau.msc2815" => false,
            "fi.mau.msc2659.stable" => true,
            "org.matrix.msc3882" => false,
            "org.matrix.msc3881" => false,
            "org.matrix.msc3874" => false,
            "org.matrix.msc3886" => false,
            "org.matrix.msc3912" => false,
            "org.matrix.msc3952_intentional_mentions" => false,
            "org.matrix.msc3981" => false,
            "org.matrix.msc3391" => false,
        ];
    }
}
