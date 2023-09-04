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


/**
 * Unit tests for test data generator.
 *
 * @package     auth_iomadsaml2
 * @category    test
 * @group       auth_iomadsaml2
 * @covers      \auth_iomadsaml2_generator
 * @copyright   2021 Catalyst IT Australia {@link http://www.catalyst-au.net}
 * @copyright   2021 Moodle Pty Ltd <support@moodle.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class generator_testcase extends \advanced_testcase {
    /**
     * Set up
     */
    public function setUp(): void {
        $this->resetAfterTest();
    }

    /**
     * Get generator
     *
     * @return auth_iomadsaml2_generator|auth_iomadsaml2\testing\generator
     */
    protected function get_generator() {
        if (class_exists('\core\testing\component_generator')) { // Required for Totara 15 support
            return $generator = \auth_iomadsaml2\testing\generator::instance();
        } else {
            return $this->getDataGenerator()->get_plugin_generator('auth_iomadsaml2');
        }
    }

    /**
     * Test create_idp_entity
     */
    public function test_create_idp_entity(): void {
        // Sanity check.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertFalse($auth->is_configured());
        $this->assertCount(0, $auth->metadataentities);

        // Create one entity, check files and fields.
        $entity1 = $this->get_generator()->create_idp_entity();
        $auth = get_auth_plugin('iomadsaml2');

        $files = array(
            'crt' => $auth->certcrt,
            'pem' => $auth->certpem,
            'xml' => $auth->get_file(md5($entity1->metadataurl) . '.idp.xml'),
        );
        foreach ($files as $file) {
            $this->assertFileExists($file);
        }
        $this->assertTrue($auth->is_configured());
        $this->assertCount(1, $auth->metadataentities);
        $this->assertEquals($entity1->defaultname, reset($auth->metadataentities)->name);
        $this->assertEquals($entity1->entityid, reset($auth->metadataentities)->entityid);
        $this->assertEquals($entity1->metadataurl, reset($auth->metadataentities)->metadataurl);

        // Create another entity.
        $this->get_generator()->create_idp_entity();
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertCount(2, $auth->metadataentities);
        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            $this->assertEqualsCanonicalizing(['Test IdP 1', 'Test IdP 2'], array_column($auth->metadataentities, 'name'));
        } else {
            $this->assertEquals(['Test IdP 1', 'Test IdP 2'], array_column($auth->metadataentities, 'name'), '', 0, 10, true);
        }

        // Create non-active entity, it should not be added to metadataentities.
        $this->get_generator()->create_idp_entity(['activeidp' => 0]);
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertCount(2, $auth->metadataentities);
        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            $this->assertEqualsCanonicalizing(['Test IdP 1', 'Test IdP 2'], array_column($auth->metadataentities, 'name'));
        } else {
            $this->assertEquals(['Test IdP 1', 'Test IdP 2'], array_column($auth->metadataentities, 'name'), '', 0, 10, true);
        }

        // Create custom named entity.
        $this->get_generator()->create_idp_entity(['defaultname' => 'Generator']);
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertCount(3, $auth->metadataentities);
        // Backwards compatibility with older PHPUnit - use old Canonicalizing method.
        if (method_exists($this, 'assertEqualsCanonicalizing')) {
            $this->assertEqualsCanonicalizing(['Test IdP 1', 'Test IdP 2', 'Generator'], array_column($auth->metadataentities, 'name'));
        } else {
            $this->assertEquals(['Test IdP 1', 'Test IdP 2', 'Generator'], array_column($auth->metadataentities, 'name'), '', 0, 10, true);
        }
    }

    /**
     * Test create_idp_entity
     */
    public function test_create_idp_entity_no_files(): void {
        // Sanity check.
        $auth = get_auth_plugin('iomadsaml2');
        $this->assertFalse($auth->is_configured());
        $this->assertCount(0, $auth->metadataentities);

        // Create one entity, check no files were created.
        $entity1 = $this->get_generator()->create_idp_entity([], false);
        $auth = get_auth_plugin('iomadsaml2');

        $files = array(
            'crt' => $auth->certcrt,
            'pem' => $auth->certpem,
            'xml' => $auth->get_file(md5($entity1->metadataurl) . '.idp.xml'),
        );
        foreach ($files as $file) {
            // Backwards compatibility with older PHPUnit - use old assertFile method.
            if (method_exists($this, 'assertFileDoesNotExist')) {
                $this->assertFileDoesNotExist($file);
            } else {
                $this->assertFileNotExists($file);
            }
        }
    }
}
