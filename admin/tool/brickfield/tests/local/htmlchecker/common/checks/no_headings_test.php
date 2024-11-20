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
 * Class no_headings_test
 */
class no_headings_test extends all_checks {
    /** @var string Check type */
    public $checktype = 'no_headings';

    /** @var string Html fail */
    private $htmlfail = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc does not contain any h tag and has more than 1800 characters - fail</title>
    </head>
    <body>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /** @var string Html pass 1 */
    private $htmlpass1 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc contains more than 1800 characters within a p tag and a h1 tag - pass</title>
    </head>
    <body>
    <h1>This a h1 heading</h1>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /** @var string Html pass 2 */
    private $htmlpass2 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc contains more than 1800 characters within a p tag and a h2 tag - pass</title>
    </head>
    <body>
    <h2>This a h2 heading</h2>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /** @var string Html pass 3 */
    private $htmlpass3 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc contains more than 1800 characters within a p tag and a h3 tag - pass</title>
    </head>
    <body>
    <h3>This a h3 heading</h3>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /** @var string Html pass 4 */
    private $htmlpass4 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc contains more than 1800 characters within a p tag and a h4 tag - pass</title>
    </head>
    <body>
    <h4>This a h4 heading</h4>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /** @var string Html pass 5 */
    private $htmlpass5 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc contains more than 1800 characters within a p tag and a h5 tag - pass</title>
    </head>
    <body>
    <h5>This a h5 heading</h5>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /** @var string Html pass 6 */
    private $htmlpass6 = <<<EOD
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>Doc contains more than 1800 characters within a p tag and a h6 tag - pass</title>
    </head>
    <body>
    <h6>This a h6 heading</h6>
    <p>
        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Donec ut varius elit, vel euismod neque. Nunc vulputate elit at
        lacus tincidunt tempus eget non urna. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus.
        Duis vel ipsum commodo, egestas erat ac, finibus orci. Nunc massa est, convallis at libero et, convallis rhoncus turpis.
        Aliquam eu ipsum egestas, blandit odio quis, mattis enim. Sed libero ante, condimentum ut sodales eget, viverra vitae dolor.
        Nulla venenatis, enim ut hendrerit placerat, neque tellus ultricies erat, a congue odio elit ac lorem. Duis quis nisl
        placerat, pulvinar ipsum nec, pretium urna. Sed id hendrerit felis. Aliquam sit amet dui justo. Donec in quam sit amet
        lectus mollis sodales. Etiam turpis purus, suscipit vel luctus quis, scelerisque id nisl. Cras elit mauris, ultricies ac
        facilisis vitae, lacinia at purus.
        Fusce pellentesque, turpis non tempus malesuada, lectus risus mollis metus, a gravida urna est sit amet diam. Fusce ut
        sapien tempus, rutrum nisi in, consequat lacus. Aliquam pretium libero dignissim tempus scelerisque. Cras eget consequat
        purus. Ut ultricies est urna, non euismod sem faucibus eget. Suspendisse venenatis iaculis augue, imperdiet sollicitudin
        metus. Fusce vitae nisl arcu. Proin fermentum sollicitudin libero eu rutrum.
        Praesent consequat hendrerit aliquam. Nunc sem turpis, vehicula et dui ac, gravida consequat quam. Sed vestibulum, risus et
        sodales condimentum, purus nunc consectetur dolor, in tempor mi ex et ligula. Sed volutpat orci nisl, at scelerisque mauris
        interdum ac. Maecenas sed sodales dui. Integer sed elit cursus, tincidunt neque sed, lobortis erat. Sed feugiat id nulla
        quis auctor. Donec in rhoncus nunc. Vestibulum in sagittis sem. Aenean ut iaculis nisi.
    </p>
    </body>
</html>
EOD;

    /**
     * Test for if heading exists where doc length is above 1800
     */
    public function test_check_fail(): void {
        $results = $this->get_checker_results($this->htmlfail);
        $this->assertNotEmpty($results);

    }

    /**
     * Test for if marquee does not exist.
     */
    public function test_check_pass(): void {
        $results = $this->get_checker_results($this->htmlpass1);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass2);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass3);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass4);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass5);
        $this->assertEmpty($results);

        $results = $this->get_checker_results($this->htmlpass6);
        $this->assertEmpty($results);
    }
}
