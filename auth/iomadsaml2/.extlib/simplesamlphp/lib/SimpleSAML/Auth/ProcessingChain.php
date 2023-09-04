<?php

declare(strict_types=1);

namespace SimpleSAML\Auth;

use SimpleSAML\Configuration;
use SimpleSAML\Error;
use SimpleSAML\Logger;
use SimpleSAML\Module;
use SimpleSAML\Utils;

/**
 * Class for implementing authentication processing chains for IdPs.
 *
 * This class implements a system for additional steps which should be taken by an IdP before
 * submitting a response to a SP. Examples of additional steps can be additional authentication
 * checks, or attribute consent requirements.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

class ProcessingChain
{
    /**
     * The list of remaining filters which should be applied to the state.
     */
    public const FILTERS_INDEX = '\SimpleSAML\Auth\ProcessingChain.filters';


    /**
     * The stage we use for completed requests.
     */
    public const COMPLETED_STAGE = '\SimpleSAML\Auth\ProcessingChain.completed';


    /**
     * The request parameter we will use to pass the state identifier when we redirect after
     * having completed processing of the state.
     */
    public const AUTHPARAM = 'AuthProcId';


    /**
     * All authentication processing filters, in the order they should be applied.
     */
    private $filters;


    /**
     * Initialize an authentication processing chain for the given service provider
     * and identity provider.
     *
     * @param array $idpMetadata  The metadata for the IdP.
     * @param array $spMetadata  The metadata for the SP.
     * @param string $mode
     */
    public function __construct($idpMetadata, $spMetadata, $mode = 'idp')
    {
        assert(is_array($idpMetadata));
        assert(is_array($spMetadata));

        $this->filters = [];

        $config = Configuration::getInstance();
        $configauthproc = $config->getArray('authproc.' . $mode, null);

        if (!empty($configauthproc)) {
            $configfilters = self::parseFilterList($configauthproc);
            self::addFilters($this->filters, $configfilters);
        }

        if (array_key_exists('authproc', $idpMetadata)) {
            $idpFilters = self::parseFilterList($idpMetadata['authproc']);
            self::addFilters($this->filters, $idpFilters);
        }

        if (array_key_exists('authproc', $spMetadata)) {
            $spFilters = self::parseFilterList($spMetadata['authproc']);
            self::addFilters($this->filters, $spFilters);
        }

        Logger::debug('Filter config for ' . $idpMetadata['entityid'] . '->' .
            $spMetadata['entityid'] . ': ' . str_replace("\n", '', var_export($this->filters, true)));
    }


    /**
     * Sort & merge filter configuration
     *
     * Inserts unsorted filters into sorted filter list. This sort operation is stable.
     *
     * @param array &$target  Target filter list. This list must be sorted.
     * @param array $src  Source filters. May be unsorted.
     * @return void
     */
    private static function addFilters(array &$target, array $src): void
    {
        foreach ($src as $filter) {
            $fp = $filter->priority;

            // Find insertion position for filter
            for ($i = count($target) - 1; $i >= 0; $i--) {
                if ($target[$i]->priority <= $fp) {
                    // The new filter should be inserted after this one
                    break;
                }
            }
            /* $i now points to the filter which should preceede the current filter. */
            array_splice($target, $i + 1, 0, [$filter]);
        }
    }


    /**
     * Parse an array of authentication processing filters.
     *
     * @param array $filterSrc  Array with filter configuration.
     * @return array  Array of ProcessingFilter objects.
     */
    private static function parseFilterList(array $filterSrc): array
    {
        $parsedFilters = [];

        foreach ($filterSrc as $priority => $filter) {
            if (is_string($filter)) {
                $filter = ['class' => $filter];
            }

            if (!is_array($filter)) {
                throw new \Exception('Invalid authentication processing filter configuration: ' .
                    'One of the filters wasn\'t a string or an array.');
            }

            $parsedFilters[] = self::parseFilter($filter, $priority);
        }

        return $parsedFilters;
    }


    /**
     * Parse an authentication processing filter.
     *
     * @param array $config      Array with the authentication processing filter configuration.
     * @param int $priority      The priority of the current filter, (not included in the filter
     *                           definition.)
     * @return \SimpleSAML\Auth\ProcessingFilter  The parsed filter.
     */
    private static function parseFilter(array $config, int $priority): ProcessingFilter
    {
        if (!array_key_exists('class', $config)) {
            throw new \Exception('Authentication processing filter without name given.');
        }

        $className = Module::resolveClass(
            $config['class'],
            'Auth\Process',
            '\SimpleSAML\Auth\ProcessingFilter'
        );
        $config['%priority'] = $priority;
        unset($config['class']);

        /** @var \SimpleSAML\Auth\ProcessingFilter */
        return new $className($config, null);
    }


    /**
     * Process the given state.
     *
     * This function will only return if processing completes. If processing requires showing
     * a page to the user, we will not be able to return from this function. There are two ways
     * this can be handled:
     * - Redirect to a URL: We will redirect to the URL set in $state['ReturnURL'].
     * - Call a function: We will call the function set in $state['ReturnCall'].
     *
     * If an exception is thrown during processing, it should be handled by the caller of
     * this function. If the user has redirected to a different page, the exception will be
     * returned through the exception handler defined on the state array. See
     * State for more information.
     *
     * @see State
     * @see State::EXCEPTION_HANDLER_URL
     * @see State::EXCEPTION_HANDLER_FUNC
     *
     * @param array &$state  The state we are processing.
     * @throws \SimpleSAML\Error\Exception
     * @throws \SimpleSAML\Error\UnserializableException
     * @return void
     */
    public function processState(&$state)
    {
        assert(is_array($state));
        assert(array_key_exists('ReturnURL', $state) || array_key_exists('ReturnCall', $state));
        assert(!array_key_exists('ReturnURL', $state) || !array_key_exists('ReturnCall', $state));

        $state[self::FILTERS_INDEX] = $this->filters;

        try {
            // TODO: remove this in SSP 2.0
            if (!array_key_exists('UserID', $state)) {
                // No unique user ID present. Attempt to add one.
                self::addUserID($state);
            }

            while (count($state[self::FILTERS_INDEX]) > 0) {
                $filter = array_shift($state[self::FILTERS_INDEX]);
                $filter->process($state);
            }
        } catch (Error\Exception $e) {
            // No need to convert the exception
            throw $e;
        } catch (\Exception $e) {
            /*
             * To be consistent with the exception we return after an redirect,
             * we convert this exception before returning it.
             */
            throw new Error\UnserializableException($e);
        }

        // Completed
    }


    /**
     * Continues processing of the state.
     *
     * This function is used to resume processing by filters which for example needed to show
     * a page to the user.
     *
     * This function will never return. Exceptions thrown during processing will be passed
     * to whatever exception handler is defined in the state array.
     *
     * @param array $state  The state we are processing.
     * @return void
     */
    public static function resumeProcessing($state)
    {
        assert(is_array($state));

        while (count($state[self::FILTERS_INDEX]) > 0) {
            $filter = array_shift($state[self::FILTERS_INDEX]);
            try {
                $filter->process($state);
            } catch (Error\Exception $e) {
                State::throwException($state, $e);
            } catch (\Exception $e) {
                $e = new Error\UnserializableException($e);
                State::throwException($state, $e);
            }
        }

        // Completed

        assert(array_key_exists('ReturnURL', $state) || array_key_exists('ReturnCall', $state));
        assert(!array_key_exists('ReturnURL', $state) || !array_key_exists('ReturnCall', $state));


        if (array_key_exists('ReturnURL', $state)) {
            /*
             * Save state information, and redirect to the URL specified
             * in $state['ReturnURL'].
             */
            $id = State::saveState($state, self::COMPLETED_STAGE);
            Utils\HTTP::redirectTrustedURL($state['ReturnURL'], [self::AUTHPARAM => $id]);
        } else {
            /* Pass the state to the function defined in $state['ReturnCall']. */

            // We are done with the state array in the session. Delete it.
            State::deleteState($state);

            $func = $state['ReturnCall'];
            assert(is_callable($func));

            call_user_func($func, $state);
            assert(false);
        }
    }


    /**
     * Process the given state passivly.
     *
     * Modules with user interaction are expected to throw an \SimpleSAML\Module\saml\Error\NoPassive exception
     * which are silently ignored. Exceptions of other types are passed further up the call stack.
     *
     * This function will only return if processing completes.
     *
     * @param array &$state  The state we are processing.
     * @return void
     */
    public function processStatePassive(&$state)
    {
        assert(is_array($state));
        // Should not be set when calling this method
        assert(!array_key_exists('ReturnURL', $state));

        // Notify filters about passive request
        $state['isPassive'] = true;

        $state[self::FILTERS_INDEX] = $this->filters;

        // TODO: remove this in SSP 2.0
        if (!array_key_exists('UserID', $state)) {
            // No unique user ID present. Attempt to add one.
            self::addUserID($state);
        }

        while (count($state[self::FILTERS_INDEX]) > 0) {
            $filter = array_shift($state[self::FILTERS_INDEX]);
            try {
                $filter->process($state);
            } catch (Error\NoPassive $e) {
                // @deprecated will be removed in 2.0
                // Ignore \SimpleSAML\Error\NoPassive exceptions
            } catch (Module\saml\Error\NoPassive $e) {
                // Ignore \SimpleSAML\Module\saml\Error\NoPassive exceptions
            }
        }
    }

    /**
     * Retrieve a state which has finished processing.
     *
     * @param string $id The state identifier.
     * @see State::parseStateID()
     * @return array|null The state referenced by the $id parameter.
     */
    public static function fetchProcessedState($id)
    {
        assert(is_string($id));

        return State::loadState($id, self::COMPLETED_STAGE);
    }


    /**
     * @deprecated This method will be removed in SSP 2.0.
     * @param array &$state
     * @return void
     */
    private static function addUserID(array &$state): void
    {
        assert(array_key_exists('Attributes', $state));

        if (isset($state['Destination']['userid.attribute'])) {
            $attributeName = $state['Destination']['userid.attribute'];
            Logger::debug("The 'userid.attribute' option has been deprecated.");
        } elseif (isset($state['Source']['userid.attribute'])) {
            $attributeName = $state['Source']['userid.attribute'];
            Logger::debug("The 'userid.attribute' option has been deprecated.");
        } else {
            // Default attribute
            $attributeName = 'eduPersonPrincipalName';
        }

        if (!array_key_exists($attributeName, $state['Attributes'])) {
            return;
        }

        $uid = $state['Attributes'][$attributeName];
        if (count($uid) === 0) {
            Logger::warning('Empty user id attribute [' . $attributeName . '].');
            return;
        }

        if (count($uid) > 1) {
            Logger::warning('Multiple attribute values for user id attribute [' . $attributeName . '].');
            return;
        }

        // TODO: the attribute value should be trimmed
        $uid = $uid[0];

        if (empty($uid)) {
            Logger::warning('Empty value in attribute ' . $attributeName . ". on user. Cannot set UserID.");
            return;
        }
        $state['UserID'] = $uid;
    }
}
