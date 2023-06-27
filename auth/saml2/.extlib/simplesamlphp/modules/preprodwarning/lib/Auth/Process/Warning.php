<?php

namespace SimpleSAML\Module\preprodwarning\Auth\Process;

/**
 * Give a warning that the user is accessing a test system, not a production system.
 *
 * @package SimpleSAMLphp
 */

class Warning extends \SimpleSAML\Auth\ProcessingFilter
{
    /**
     * Process a authentication response.
     *
     * This function saves the state, and redirects the user to the page where the user
     * can authorize the release of the attributes.
     *
     * @param array $state  The state of the response.
     * @return void
     */
    public function process(&$state)
    {
        assert(is_array($state));

        if (isset($state['isPassive']) && $state['isPassive'] === true) {
            // We have a passive request. Skip the warning
            return;
        }

        // Save state and redirect.
        $id = \SimpleSAML\Auth\State::saveState($state, 'warning:request');
        $url = \SimpleSAML\Module::getModuleURL('preprodwarning/showwarning.php');
        \SimpleSAML\Utils\HTTP::redirectTrustedURL($url, ['StateId' => $id]);
    }
}
