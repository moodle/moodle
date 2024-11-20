<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require_once __DIR__.'/../../../../lib/Twig/Extensions/Extension/Text.php';

class Twig_Tests_Extension_TextTest extends \PHPUnit\Framework\TestCase
{
    /** @var TwigEnvironment */
    private $env;

    public function setUp()
    {
        $this->env = $this->getMockBuilder('Twig_Environment')->disableOriginalConstructor()->getMock();
        $this->env
            ->expects($this->any())
            ->method('getCharset')
            ->will($this->returnValue('utf-8'))
        ;
    }

    /**
     * @dataProvider getTruncateTestData
     */
    public function testTruncate($input, $length, $preserve, $separator, $expectedOutput)
    {
        $output = twig_truncate_filter($this->env, $input, $length, $preserve, $separator);
        $this->assertEquals($expectedOutput, $output);
    }

    public function getTruncateTestData()
    {
        return array(
            array('This is a very long sentence.', 2, false, '...', 'Th...'),
            array('This is a very long sentence.', 6, false, '...', 'This i...'),
            array('This is a very long sentence.', 2, true, '...', 'This...'),
            array('This is a very long sentence.', 2, true, '[...]', 'This[...]'),
            array('This is a very long sentence.', 23, false, '...', 'This is a very long sen...'),
            array('This is a very long sentence.', 23, true, '...', 'This is a very long sentence.'),
        );
    }
}
