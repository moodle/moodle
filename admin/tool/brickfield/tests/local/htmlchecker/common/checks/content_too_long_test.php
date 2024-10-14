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
 * tool_brickfield check test.
 *
 * @package    tool_brickfield
 * @copyright  2020 onward: Brickfield Education Labs, https://www.brickfield.ie
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace tool_brickfield\local\htmlchecker\common\checks;

defined('MOODLE_INTERNAL') || die();

require_once('all_checks.php');

/**
 * Class content_too_long_testcase
 */
final class content_too_long_test extends all_checks {
    /** @var string Check type */
    protected $checktype = 'content_too_long';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
    <p><span>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Praesent accumsan, ante varius viverra aliquam, dolor risus
    scelerisque massa, ut lacinia ipsum felis id est. Nullam convallis odio ante, in commodo elit fermentum sed. Vivamus ullamcorper
    tincidunt sagittis. Sed et semper sapien. Quisque malesuada lacus nec libero cursus, aliquam malesuada neque ultricies. Cras sit
    amet enim vel orci tristique porttitor a vitae urna. Suspendisse mi leo, hendrerit et eleifend a, mollis at ex. Maecenas eget
    magna nec sem dignissim pharetra vel nec ex. Donec in porta lectus. Aenean porttitor euismod lectus, sodales eleifend ex egestas
    in. Donec sed metus sodales, lobortis velit quis, dictum arcu.</span></p>
    <p><span>Praesent mollis urna eget odio cursus, sit amet sollicitudin ante aliquam. Integer nec massa nec ipsum tincidunt
    laoreet in vitae metus.
    Integer massa lacus, elementum quis dui sed, eleifend fringilla turpis. In hac habitasse platea dictumst. Phasellus
    efficitur quis felis non eleifend. Sed et mauris vel lorem ultrices porta. Mauris commodo condimentum felis, vel dictum ex
    laoreet sit amet. Duis venenatis ut lacus non ultrices. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per
    inceptos himenaeos. Nam nunc magna, semper feugiat feugiat a, pellentesque vel nulla.
    Sed lacinia nunc lobortis, vestibulum nisi dictum, pulvinar tellus. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed
    sodales, mauris vitae vulputate porttitor, urna tellus tempor turpis, sed hendrerit metus turpis at est. Etiam augue purus,
    blandit eget elit sit amet, suscipit mollis ligula. Suspendisse rutrum sem ex, eu commodo nisi aliquam sit amet. Fusce ut felis
    justo. Sed a quam at lectus consectetur vulputate. Proin elementum dui nisi, in condimentum diam porttitor eget. Donec vehicula
    condimentum velit vel semper. Mauris vehicula tortor lectus, quis convallis erat aliquet vel. In dictum nunc ac posuere porta.
    Sed vel leo aliquam, volutpat ligula ac, blandit diam. Donec nec ligula lacus.</span></p>
    <p><span>Mauris ac libero vel ex fringilla fringilla. Ut vehicula justo eu nunc imperdiet ultricies. Sed interdum ligula at nisi
    rhoncus auctor.
    Sed tempus tellus eget risus placerat, et viverra dolor gravida. Sed ultricies neque id ex tempor viverra. Ut imperdiet
    pharetra magna sed tristique. Pellentesque blandit elit ac neque lacinia finibus. Lorem ipsum dolor sit amet, consectetur
    adipiscing elit. Donec vel auctor dolor. Morbi id elit mollis ante mattis semper eu ac lectus. Integer elit turpis, facilisis
    vel metus eget, blandit tempus arcu. Pellentesque eget magna eu ex eleifend tincidunt. Curabitur sit amet congue nisi.
    Cras mauris risus, malesuada egestas dapibus et, pharetra in ante. Aenean sit amet augue non ligula tempor scelerisque eget ac
    turpis. Aenean tincidunt tristique dui, pretium lacinia felis posuere vel. Donec massa ligula, luctus vitae enim nec, sagittis
    hendrerit lorem. In consequat sodales metus vel porttitor. Aenean fringilla fringilla risus, vitae interdum turpis egestas quis.
    Aenean volutpat arcu leo, ut dictum purus consectetur id. Cras enim ipsum, tincidunt vitae mi vel, varius convallis ex. Fusce
    pretium porttitor tempus.</span></p>
    <p>Morbi laoreet dapibus lectus ut efficitur. Donec at hendrerit nunc. Vivamus venenatis augue non nulla finibus vestibulum. Nam
    nunc magna, hendrerit a ipsum nec, pulvinar imperdiet augue. Fusce vel metus maximus, mattis magna at, egestas enim. Suspendisse
    et nisl at enim mollis scelerisque. Duis ut ipsum vel turpis eleifend aliquet a a ante. Nam lacinia purus vulputate purus
    tincidunt, aliquet sagittis nisi sagittis. Pellentesque efficitur massa non ex sodales pretium. Cras convallis vitae ex et
    dignissim. Nunc suscipit bibendum aliquam. Maecenas interdum tellus varius, laoreet velit sed, ornare arcu. Nunc pulvinar
    elementum sem eget scelerisque. Duis volutpat tellus ut risus finibus, nec molestie erat fermentum
    </p>
EOD;

    /** @var string Multibyte html falure */
    private $htmlfail2 = <<<EOD
    <p><span>ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル ブルース カンベッル
    </span></p>
EOD;

    /** @var string Html pass */
    private $htmlpass = <<<EOD
    <p>Nice and short text</p>
EOD;

    /**
     * Test for checking the length of the content
     */
    public function test_check(): void {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertTrue($results[0]->message == '<p id=\'wc\'>Word Count: 578</p>');

        $results = $this->get_checker_results($this->htmlfail2);
        $this->assertTrue($results[0]->message == '<p id=\'wc\'>Word Count: 504</p>');

        $results = $this->get_checker_results($this->htmlpass);
        $this->assertEmpty($results);
    }
}
