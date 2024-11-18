<?php
// This file is part of IOMAD SAML2 Authentication Plugin
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

defined('MOODLE_INTERNAL') || die();

require_once(__DIR__ . '/../_autoload.php');

/**
 * Test Saml2 Autoloader
 *
 * @package     auth_iomadsaml2
 * @author      Daniel Thee Roperto <daniel.roperto@catalyst-au.net>
 * @copyright   2018 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_iomadsaml2_autoloader_test extends advanced_testcase {
    public function test_it_loads_classes() {
        $classes = [
            \Psr\Log\LoggerInterface::class,
            \SAML2\Utils::class,
            \SimpleSAML\Configuration::class,
            \RobRichards\XMLSecLibs\XMLSecEnc::class,
            Twig\Loader\FilesystemLoader::class,
        ];
        foreach ($classes as $class) {
            $found = class_exists($class) || interface_exists($class);
            self::assertTrue($found, "Class not found: {$class}");
        }
    }
}
