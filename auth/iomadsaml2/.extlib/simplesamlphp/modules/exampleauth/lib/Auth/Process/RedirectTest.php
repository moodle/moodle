<?php

declare(strict_types=1);

namespace SimpleSAML\Module\exampleauth\Auth\Process;

use SimpleSAML\Auth;
use SimpleSAML\Module;
use SimpleSAML\Utils;

/**
 * A simple processing filter for testing that redirection works as it should.
 *
 */
class RedirectTest extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Initialize processing of the redirect test.
     *
     * @param array &$state  The state we should update.
     * @return void
     */
    public function process(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('Attributes', $state));

        // To check whether the state is saved correctly
        $state['Attributes']['RedirectTest1'] = ['OK'];

        // Save state and redirect
        $id = Auth\State::saveState($state, 'exampleauth:redirectfilter-test');
        $url = Module::getModuleURL('exampleauth/redirecttest.php');
        Utils\HTTP::redirectTrustedURL($url, ['StateId' => $id]);
    }
}
