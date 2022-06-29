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

namespace tool_brickfield;

use tool_brickfield\local\tool\filter;
use tool_brickfield\local\tool\tool;

/**
 * Unit tests for {@tool tool_brickfield\local\tool\tool}.
 *
 * @package   tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, www.brickfield.ie
 * @author     Jay Churchward (jay.churchward@poetopensource.org)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class tool_test extends \advanced_testcase {

    /** @var string base 64 image */
    protected $base64img = <<<EOF
<img src="data:image/gif;base64,R0lGODlhPQBEAPeoAJosM//AwO/AwHVYZ/z595kzAP/s7P+goOXMv8+fhw/v739/f+8PD98fH/
8mJl+fn/9ZWb8/PzWlwv///6wWGbImAPgTEMImIN9gUFCEm/gDALULDN8PAD6atYdCTX9gUNKlj8wZAKUsAOzZz+UMAOsJAP/Z2ccMDA8PD/95eX5N
WvsJCOVNQPtfX/8zM8+QePLl38MGBr8JCP+zs9myn/8GBqwpAP/GxgwJCPny78lzYLgjAJ8vAP9fX/+MjMUcAN8zM/9wcM8ZGcATEL+QePdZWf/29u
c/P9cmJu9MTDImIN+/r7+/vz8/P8VNQGNugV8AAF9fX8swMNgTAFlDOICAgPNSUnNWSMQ5MBAQEJE3QPIGAM9AQMqGcG9vb6MhJsEdGM8vLx8fH98A
ANIWAMuQeL8fABkTEPPQ0OM5OSYdGFl5jo+Pj/+pqcsTE78wMFNGQLYmID4dGPvd3UBAQJmTkP+8vH9QUK+vr8ZWSHpzcJMmILdwcLOGcHRQUHxwcK9
PT9DQ0O/v70w5MLypoG8wKOuwsP/g4P/Q0IcwKEswKMl8aJ9fX2xjdOtGRs/Pz+Dg4GImIP8gIH0sKEAwKKmTiKZ8aB/f39Wsl+LFt8dgUE9PT5x5aH
BwcP+AgP+WltdgYMyZfyywz78AAAAAAAD///8AAP9mZv///wAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAA
AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAACH5BAEAAKgALAAAAAA9AEQAAAj/AFEJHEiwoMGDCBMqXMiwocAbBww4nEhxoYkUpz
JGrMixogkfGUNqlNixJEIDB0SqHGmyJSojM1bKZOmyop0gM3Oe2liTISKMOoPy7GnwY9CjIYcSRYm0aVKSLmE6nfq05QycVLPuhDrxBlCtYJUqNAq2b
NWEBj6ZXRuyxZyDRtqwnXvkhACDV+euTeJm1Ki7A73qNWtFiF+/gA95Gly2CJLDhwEHMOUAAuOpLYDEgBxZ4GRTlC1fDnpkM+fOqD6DDj1aZpITp0dt
GCDhr+fVuCu3zlg49ijaokTZTo27uG7Gjn2P+hI8+PDPERoUB318bWbfAJ5sUNFcuGRTYUqV/3ogfXp1rWlMc6awJjiAAd2fm4ogXjz56aypOoIde4O
E5u/F9x199dlXnnGiHZWEYbGpsAEA3QXYnHwEFliKAgswgJ8LPeiUXGwedCAKABACCN+EA1pYIIYaFlcDhytd51sGAJbo3onOpajiihlO92KHGaUXGw
WjUBChjSPiWJuOO/LYIm4v1tXfE6J4gCSJEZ7YgRYUNrkji9P55sF/ogxw5ZkSqIDaZBV6aSGYq/lGZplndkckZ98xoICbTcIJGQAZcNmdmUc210hs3
5nCyJ58fgmIKX5RQGOZowxaZwYA+JaoKQwswGijBV4C6SiTUmpphMspJx9unX4KaimjDv9aaXOEBteBqmuuxgEHoLX6Kqx+yXqqBANsgCtit4FWQAEk
rNbpq7HSOmtwag5w57GrmlJBASEU18ADjUYb3ADTinIttsgSB1oJFfA63bduimuqKB1keqwUhoCSK374wbujvOSu4QG6UvxBRydcpKsav++Ca6G8A6P
r1x2kVMyHwsVxUALDq/krnrhPSOzXG1lUTIoffqGR7Goi2MAxbv6O2kEG56I7CSlRsEFKFVyovDJoIRTg7sugNRDGqCJzJgcKE0ywc0ELm6KBCCJo8D
IPFeCWNGcyqNFE06ToAfV0HBRgxsvLThHn1oddQMrXj5DyAQgjEHSAJMWZwS3HPxT/QMbabI/iBCliMLEJKX2EEkomBAUCxRi42VDADxyTYDVogV+wS
ChqmKxEKCDAYFDFj4OmwbY7bDGdBhtrnTQYOigeChUmc1K3QTnAUfEgGFgAWt88hKA6aCRIXhxnQ1yg3BCayK44EWdkUQcBByEQChFXfCB776aQsG0B
IlQgQgE8qO26X1h8cEUep8ngRBnOy74E9QgRgEAC8SvOfQkh7FDBDmS43PmGoIiKUUEGkMEC/PJHgxw0xH74yx/3XnaYRJgMB8obxQW6kL9QYEJ0FIF
gByfIL7/IQAlvQwEpnAC7DtLNJCKUoO/w45c44GwCXiAFB/OXAATQryUxdN4LfFiwgjCNYg+kYMIEFkCKDs6PKAIJouyGWMS1FSKJOMRB/BoIxYJIUX
FUxNwoIkEKPAgCBZSQHQ1A2EWDfDEUVLyADj5AChSIQW6gu10bE/JG2VnCZGfo4R4d0sdQoBAHhPjhIB94v/wRoRKQWGRHgrhGSQJxCS+0pCZbEhAAO
w==" alt="This is a bus." />';
EOF;


    public function test_build_all_accessibilitytools() {
        $tools = tool::build_all_accessibilitytools();

        $this->assertEquals($tools['errors']::toolshortname(), 'Error list');
        $this->assertEquals($tools['activityresults']::toolshortname(), 'Activity breakdown');
        $this->assertEquals($tools['checktyperesults']::toolshortname(), 'Content types');
        $this->assertEquals($tools['printable']::toolshortname(), 'Summary report');
        $this->assertEquals($tools['advanced']::toolshortname(), 'Advanced');
    }

    public function test_data_is_valid() {
        $object = $this->getMockForAbstractClass(tool::class);
        $object->set_filter(new filter());
        $output = $object->data_is_valid();
        $this->assertFalse($output);
    }

    public function test_can_access() {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        $filter = new filter(1, $category->id, 'tab', 3, 4);

        $tool = $this->getMockForAbstractClass(tool::class);

        $output = $tool->can_access($filter);
        $this->assertFalse($output);
    }

    public function test_get_error_message() {
        $tool = $this->getMockForAbstractClass(tool::class);

        $output = $tool->get_error_message();
        $this->assertEquals($output, '');
    }

    public function test_get_module_label() {
        $output = tool::get_module_label('core_course');
        $this->assertEquals($output, 'Course');

        $output = tool::get_module_label('mod_book');
        $this->assertEquals($output, 'Book');
    }

    public function test_toplevel_arguments() {
        $this->resetAfterTest();
        $category = $this->getDataGenerator()->create_category();
        $filter = new filter(1, $category->id, 'tab', 3, 4);

        $output = tool::toplevel_arguments();
        $this->assertEmpty($output);

        $output = tool::toplevel_arguments($filter);
        $this->assertEquals($output['courseid'], 1);
        $this->assertEquals($output['categoryid'], $category->id);
    }

    /**
     * Base64 image provider.
     * @return array
     */
    public function base64_img_provider(): array {
        $img = '<img src="myimage.jpg" />';
        return [
            'Image tag alone (base64)' => [
                $this->base64img,
                true,
            ],
            'Image tag alone (link)' => [
                $img,
                false,
            ],
            'Image tag in string (base64)' => [
                "This is my image {$this->base64img}.",
                true,
            ],
            'Image tag in string (link)' => [
                "This is my image {$img}.",
                false,
            ],
            'Image tag with string base64 in alt' => [
                "<img src='myimage.jpg' alt='base64'/>",
                false,
            ],
            'base64 string in text' => [
                "An example base 64 format string is 'data:image/gif;base64'./>",
                false,
            ],
        ];
    }

    /**
     * Test base 64 image provider.
     * @dataProvider base64_img_provider
     * @param string $content
     * @param bool $expectation
     */
    public function test_base64_img_detected(string $content, bool $expectation) {
        $this->assertEquals(
            $expectation,
            tool::base64_img_detected($content)
        );
    }

    public function test_truncate_base64() {
        $truncated = tool::truncate_base64($this->base64img);
        $this->assertStringContainsString('<img src="data:image/gif;base64..."', $truncated);
    }
}
