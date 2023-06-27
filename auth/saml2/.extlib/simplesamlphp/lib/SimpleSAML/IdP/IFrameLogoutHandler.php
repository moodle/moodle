<?php

declare(strict_types=1);

namespace SimpleSAML\IdP;

use SimpleSAML\Auth;
use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\IdP;
use SimpleSAML\Module;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;

/**
 * Class that handles iframe logout.
 *
 * @package SimpleSAMLphp
 */

class IFrameLogoutHandler implements LogoutHandlerInterface
{
    /**
     * The IdP we are logging out from.
     *
     * @var \SimpleSAML\IdP
     */
    private $idp;

    /**
     * LogoutIFrame constructor.
     *
     * @param \SimpleSAML\IdP $idp The IdP to log out from.
     */
    public function __construct(IdP $idp)
    {
        $this->idp = $idp;
    }

    /**
     * Start the logout operation.
     *
     * @param array &$state The logout state.
     * @param string|null $assocId The SP we are logging out from.
     * @return void
     */
    public function startLogout(array &$state, $assocId)
    {
        assert(is_string($assocId) || $assocId === null);

        $associations = $this->idp->getAssociations();

        if (count($associations) === 0) {
            $this->idp->finishLogout($state);
        }

        foreach ($associations as $id => &$association) {
            $idp = IdP::getByState($association);
            $association['core:Logout-IFrame:Name'] = $idp->getSPName($id);
            $association['core:Logout-IFrame:State'] = 'onhold';
        }
        $state['core:Logout-IFrame:Associations'] = $associations;

        if (!is_null($assocId)) {
            $spName = $this->idp->getSPName($assocId);
            if ($spName === null) {
                $spName = ['en' => $assocId];
            }

            $state['core:Logout-IFrame:From'] = $spName;
        } else {
            $state['core:Logout-IFrame:From'] = null;
        }

        $params = [
            'id' => Auth\State::saveState($state, 'core:Logout-IFrame'),
        ];
        if (isset($state['core:Logout-IFrame:InitType'])) {
            $params['type'] = $state['core:Logout-IFrame:InitType'];
        }

        $url = Module::getModuleURL('core/idp/logout-iframe.php', $params);
        Utils\HTTP::redirectTrustedURL($url);
    }


    /**
     * Continue the logout operation.
     *
     * This function will never return.
     *
     * @param string $assocId The association that is terminated.
     * @param string|null $relayState The RelayState from the start of the logout.
     * @param \SimpleSAML\Error\Exception|null $error The error that occurred during session termination (if any).
     * @return void
     */
    public function onResponse($assocId, $relayState, Error\Exception $error = null)
    {
        assert(is_string($assocId));

        $this->idp->terminateAssociation($assocId);

        $config = Configuration::getInstance();

        $t = new Template($config, 'IFrameLogoutHandler.tpl.php');
        $t->data['assocId'] = var_export($assocId, true);
        $t->data['spId'] = sha1($assocId);
        if (!is_null($error)) {
            $t->data['errorMsg'] = $error->getMessage();
        }

        $t->show();
    }
}
