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

namespace quizaccess_seb;

/**
 * PHPUnit for property_list class.
 *
 * @package   quizaccess_seb
 * @author    Andrew Madden <andrewmadden@catalyst-au.net>
 * @copyright 2020 Catalyst IT
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class property_list_test extends \advanced_testcase {

    /**
     * Test that an empty PList with a root dictionary is created.
     */
    public function test_create_empty_plist(): void {
        $emptyplist = new property_list();
        $xml = trim($emptyplist->to_xml());
        $this->assertEquals('<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0"><dict/></plist>', $xml);
    }

    /**
     * Test that a Plist is constructed from an XML string.
     */
    public function test_construct_plist_from_xml(): void {
        $xml = $this->get_plist_xml_header()
            . "<key>testKey</key>"
            . "<string>testValue</string>"
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $generatedxml = trim($plist->to_xml());
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\"><dict><key>testKey</key><string>testValue</string></dict></plist>", $generatedxml);
    }

    /**
     * Test that an element can be added to the root dictionary.
     */
    public function test_add_element_to_root(): void {
        $plist = new property_list();
        $newelement = new \CFPropertyList\CFString('testValue');
        $plist->add_element_to_root('testKey', $newelement);
        $generatedxml = trim($plist->to_xml());
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\"><dict><key>testKey</key><string>testValue</string></dict></plist>", $generatedxml);
    }

    /**
     * Test that an element's value can be retrieved.
     */
    public function test_get_element_value(): void {
        $xml = $this->get_plist_xml_header()
                . "<key>testKey</key>"
                . "<string>testValue</string>"
                . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $this->assertEquals('testValue', $plist->get_element_value('testKey'));
    }

    /**
     * Test that an element's value can be retrieved.
     */
    public function test_get_element_value_if_not_exists(): void {
        $plist = new property_list();
        $this->assertEmpty($plist->get_element_value('testKey'));
    }

    /**
     * Test an element's value can be retrieved if it is an array.
     */
    public function test_get_element_value_if_array(): void {
        $xml = $this->get_plist_xml_header()
            . "<key>testDict</key>"
            . "<dict>"
            . "<key>testKey</key>"
            . "<string>testValue</string>"
            . "</dict>"
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $this->assertEquals(['testKey' => 'testValue'], $plist->get_element_value('testDict'));
    }

    /**
     * Test that a element's value can be updated that is not an array or dictionary.
     *
     * @param string $xml XML to create PList.
     * @param string $key Key of element to try and update.
     * @param mixed $value Value to try to update with.
     *
     * @dataProvider good_update_data_provider
     */
    public function test_updating_element_value($xml, $key, $value): void {
        $xml = $this->get_plist_xml_header()
            . $xml
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $plist->update_element_value($key, $value);
        $this->assertEquals($value, $plist->get_element_value($key));
    }

    /**
     * Test that a element's value can be updated that is not an array or dictionary.
     *
     * @param string $xml XML to create PList.
     * @param string $key Key of element to try and update.
     * @param mixed $value Bad value to try to update with.
     * @param mixed $expected Expected value of element after update is called.
     * @param string $exceptionmessage Message of exception expected to be thrown.

     * @dataProvider bad_update_data_provider
     */
    public function test_updating_element_value_with_bad_data(string $xml, string $key, $value, $expected, $exceptionmessage): void {
        $xml = $this->get_plist_xml_header()
            . $xml
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);

        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage($exceptionmessage);

        $plist->update_element_value($key, $value);
        $plistarray = json_decode($plist->to_json()); // Export elements.
        $this->assertEquals($expected, $plistarray->$key);
    }

    /**
     * Test that a dictionary can have it's value (array) updated.
     */
    public function test_updating_element_array_if_dictionary(): void {
        $xml = $this->get_plist_xml_header()
            . "<key>testDict</key>"
            . "<dict>"
            . "<key>testKey</key>"
            . "<string>testValue</string>"
            . "</dict>"
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $plist->update_element_array('testDict', ['newKey' => new \CFPropertyList\CFString('newValue')]);
        $this->assertEquals(['newKey' => 'newValue'], $plist->get_element_value('testDict'));
    }

    /**
     * Test that a dictionary can have it's value (array) updated.
     */
    public function test_updating_element_array_if_dictionary_with_bad_data(): void {
        $xml = $this->get_plist_xml_header()
            . "<key>testDict</key>"
            . "<dict>"
            . "<key>testKey</key>"
            . "<string>testValue</string>"
            . "</dict>"
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);

        $this->expectException(\invalid_parameter_exception::class);
        $this->expectExceptionMessage('New array must only contain CFType objects.');

        $plist->update_element_array('testDict', [false]);
        $this->assertEquals(['testKey' => 'testValue'], $plist->get_element_value('testDict'));
        $this->assertDebuggingCalled('property_list: If updating an array in PList, it must only contain CFType objects.',
                DEBUG_DEVELOPER);
    }

    /**
     * Test that an element can be deleted.
     */
    public function test_delete_element(): void {
        $xml = $this->get_plist_xml_header()
            . "<key>testKey</key>"
            . "<string>testValue</string>"
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $plist->delete_element('testKey');
        $generatedxml = trim($plist->to_xml());
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\"><dict/></plist>", $generatedxml);
    }

    /**
     * Test that an element can be deleted.
     */
    public function test_delete_element_if_not_exists(): void {
        $xml = $this->get_plist_xml_header()
            . "<key>testKey</key>"
            . "<string>testValue</string>"
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $plist->delete_element('nonExistentKey');
        $generatedxml = trim($plist->to_xml());
        // The xml should be unaltered.
        $this->assertEquals("<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<!DOCTYPE plist PUBLIC \"-//Apple//DTD PLIST 1.0//EN\" \"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">
<plist version=\"1.0\"><dict><key>testKey</key><string>testValue</string></dict></plist>", $generatedxml);
    }

    /**
     * Test that json is exported correctly according to SEB Config Key requirements.
     *
     * @param string $xml PList XML used to generate CFPropertyList.
     * @param string $expectedjson Expected JSON output.
     *
     * @dataProvider json_data_provider
     */
    public function test_export_to_json($xml, $expectedjson): void {
        $xml = $this->get_plist_xml_header()
            . $xml
            . $this->get_plist_xml_footer();
        $plist = new property_list($xml);
        $generatedjson = $plist->to_json();
        $this->assertEquals($expectedjson, $generatedjson);
    }

    /**
     * Test that the xml is exported to JSON from a real SEB config file. Expected JSON extracted from SEB logs.
     */
    public function test_export_to_json_full_file(): void {
        $xml = file_get_contents(self::get_fixture_path(__NAMESPACE__, 'unencrypted_mac_001.seb'));
        $plist = new property_list($xml);
        $plist->delete_element('originatorVersion'); // JSON should not contain originatorVersion key.
        $generatedjson = $plist->to_json();
        $json = trim(file_get_contents(self::get_fixture_path(__NAMESPACE__, 'JSON_unencrypted_mac_001.txt')));
        $this->assertEquals($json, $generatedjson);
    }

    /**
     * Test the set_or_update_value function.
     */
    public function test_set_or_update_value(): void {
        $plist = new property_list();

        $this->assertEmpty($plist->get_element_value('string'));
        $this->assertEmpty($plist->get_element_value('bool'));
        $this->assertEmpty($plist->get_element_value('number'));

        // Setting values.
        $plist->set_or_update_value('string', new \CFPropertyList\CFString('initial string'));
        $plist->set_or_update_value('bool', new \CFPropertyList\CFBoolean(true));
        $plist->set_or_update_value('number', new \CFPropertyList\CFNumber('10'));

        $this->assertEquals('initial string', $plist->get_element_value('string'));
        $this->assertEquals(true, $plist->get_element_value('bool'));
        $this->assertEquals(10, $plist->get_element_value('number'));

        // Updating values.
        $plist->set_or_update_value('string', new \CFPropertyList\CFString('new string'));
        $plist->set_or_update_value('bool', new \CFPropertyList\CFBoolean(false));
        $plist->set_or_update_value('number', new \CFPropertyList\CFNumber('42'));

        $this->assertEquals('new string', $plist->get_element_value('string'));
        $this->assertEquals(false, $plist->get_element_value('bool'));
        $this->assertEquals(42, $plist->get_element_value('number'));

        // Type exception.
        $this->expectException(\TypeError::class);
        $plist->set_or_update_value('someKey', 'We really need to pass in CFTypes here');
    }

    /**
     * Get a valid PList header. Must also use footer.
     *
     * @return string
     */
    private function get_plist_xml_header(): string {
        return "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n"
                . "<!DOCTYPE plist PUBLIC \"-//Apple Computer//DTD PLIST 1.0//EN\" "
                . "\"http://www.apple.com/DTDs/PropertyList-1.0.dtd\">\n"
                . "<plist version=\"1.0\">\n"
                . "  <dict>";
    }

    /**
     * Get a valid PList footer. Must also use header.
     *
     * @return string
     */
    private function get_plist_xml_footer(): string {
        return "  </dict>\n"
                . "</plist>";
    }

    /**
     * Data provider for good data on update.
     *
     * @return array Array with test data.
     */
    public static function good_update_data_provider(): array {
        return [
            'Update string' => ['<key>testKey</key><string>testValue</string>', 'testKey', 'newValue'],
            'Update bool' => ['<key>testKey</key><true/>', 'testKey', false],
            'Update number' => ['<key>testKey</key><real>888</real>', 'testKey', 123.4],
        ];
    }

    /**
     * Data provider for bad data on update.
     *
     * @return array Array with test data.
     */
    public static function bad_update_data_provider(): array {

        return [
            'Update string with bool' => ['<key>testKey</key><string>testValue</string>', 'testKey', true, 'testValue',
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFString'],
            'Update string with number' => ['<key>testKey</key><string>testValue</string>', 'testKey', 999, 'testValue',
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFString'],
            'Update string with null' => ['<key>testKey</key><string>testValue</string>', 'testKey', null, 'testValue',
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFString'],
            'Update string with array' => ['<key>testKey</key><string>testValue</string>', 'testKey', ['arrayValue'], 'testValue',
                    'Use update_element_array to update a collection.'],
            'Update bool with string' => ['<key>testKey</key><true/>', 'testKey', 'testValue', true,
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFBool'],
            'Update bool with number' => ['<key>testKey</key><true/>', 'testKey', 999, true,
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFBool'],
            'Update bool with null' => ['<key>testKey</key><true/>', 'testKey', null, true,
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFBool'],
            'Update bool with array' => ['<key>testKey</key><true/>', 'testKey', ['testValue'], true,
                    'Use update_element_array to update a collection.'],
            'Update number with string' => ['<key>testKey</key><real>888</real>', 'testKey', 'string', 888,
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFNumber'],
            'Update number with bool' => ['<key>testKey</key><real>888</real>', 'testKey', true, 888,
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFNumber'],
            'Update number with null' => ['<key>testKey</key><real>888</real>', 'testKey', null, 888,
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFNumber'],
            'Update number with array' => ['<key>testKey</key><real>888</real>', 'testKey', ['testValue'], 888,
                    'Use update_element_array to update a collection.'],
            'Update date with string' => ['<key>testKey</key><date>1940-10-09T22:13:56Z</date>', 'testKey', 'string',
                    '1940-10-10T06:13:56+08:00',
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFDate'],
            'Update data with number' => ['<key>testKey</key><data>testData</data>', 'testKey', 789, 'testData',
                    'Invalid parameter value detected (Only string, number and boolean elements can be updated, '
                    . 'or value type does not match element type: CFPropertyList\CFData'],
        ];
    }

    /**
     * Data provider for expected JSON from PList.
     *
     * Examples extracted from requirements listed in SEB Config Key documents.
     * https://safeexambrowser.org/developer/seb-config-key.html
     *
     * 1. Date should be in ISO 8601 format.
     * 2. Data should be base 64 encoded.
     * 3. String should be UTF-8 encoded.
     * 4, 5, 6, 7. No requirements for bools, arrays or dicts.
     * 8. Empty dicts should not be included.
     * 9. JSON key ordering should be case insensitive, and use string ordering.
     * 10. URL forward slashes should not be escaped.
     *
     * @return array
     */
    public static function json_data_provider(): array {
        $data = "blahblah";
        $base64data = base64_encode($data);

        return [
            'date' => ["<key>date</key><date>1940-10-09T22:13:56Z</date>", "{\"date\":\"1940-10-09T22:13:56+00:00\"}"],
            'data' => ["<key>data</key><data>$base64data</data>", "{\"data\":\"$base64data\"}"],
            'string' => ["<key>string</key><string>hello wörld</string>", "{\"string\":\"hello wörld\"}"],
            'string with 1 backslash' => ["<key>string</key><string>ws:\localhost</string>", "{\"string\":\"ws:\localhost\"}"],
            'string with 2 backslashes' => ["<key>string</key><string>ws:\\localhost</string>",
                    '{"string":"ws:\\localhost"}'],
            'string with 3 backslashes' => ["<key>string</key><string>ws:\\\localhost</string>",
                    '{"string":"ws:\\\localhost"}'],
            'string with 4 backslashes' => ["<key>string</key><string>ws:\\\\localhost</string>",
                    '{"string":"ws:\\\\localhost"}'],
            'string with 5 backslashes' => ["<key>string</key><string>ws:\\\\\localhost</string>",
                    '{"string":"ws:\\\\\localhost"}'],
            'bool' => ["<key>bool</key><true/>", "{\"bool\":true}"],
            'array' => ["<key>array</key><array><key>arraybool</key><false/><key>arraybool2</key><true/></array>"
                    , "{\"array\":[false,true]}"],
            'empty array' => ["<key>bool</key><true/><key>array</key><array/>"
                    , "{\"array\":[],\"bool\":true}"],
            'dict' => ["<key>dict</key><dict><key>dictbool</key><false/><key>dictbool2</key><true/></dict>"
                    , "{\"dict\":{\"dictbool\":false,\"dictbool2\":true}}"],
            'empty dict' => ["<key>bool</key><true/><key>emptydict</key><dict/>", "{\"bool\":true}"],
            'unordered elements' => ["<key>testKey</key>"
                    . "<string>testValue</string>"
                    . "<key>allowWLAN</key>"
                    . "<string>testValue2</string>"
                    . "<key>allowWlan</key>"
                    . "<string>testValue3</string>"
                    , "{\"allowWlan\":\"testValue3\",\"allowWLAN\":\"testValue2\",\"testKey\":\"testValue\"}"],
            'url' => ["<key>url</key><string>http://test.com</string>", "{\"url\":\"http://test.com\"}"],
            'assoc dict' => ["<key>dict</key><dict><key>banana</key><false/><key>apple</key><true/></dict>",
                    "{\"dict\":{\"apple\":true,\"banana\":false}}"],
            'seq array' => ["<key>array</key><array><key>1</key><false/><key>2</key><true/>
<key>3</key><true/><key>4</key><true/><key>5</key><true/><key>6</key><true/>
<key>7</key><true/><key>8</key><true/><key>9</key><true/><key>10</key><true/></array>",
                    "{\"array\":[false,true,true,true,true,true,true,true,true,true]}"],
        ];
    }
}
