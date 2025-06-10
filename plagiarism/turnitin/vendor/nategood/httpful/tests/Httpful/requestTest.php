<?php
/**
 * @author nick fox <quixand gmail com>
 */
namespace Httpful\Test;

class requestTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @author Nick Fox
     */
    public function testGet_InvalidURL()
    {
        // Silence the default logger via whenError override
        $caught = false;
        try
        {
            \Httpful\Request::get('unavailable.url')->whenError(function($error) {})->send();
        }
        catch (\Httpful\Exception\ConnectionErrorException $e)
        {
            $caught = true;
        }
        $this->assertTrue($caught);
    }

}
