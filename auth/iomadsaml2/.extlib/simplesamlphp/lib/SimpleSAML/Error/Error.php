<?php

declare(strict_types=1);

namespace SimpleSAML\Error;

use SimpleSAML\Configuration;
use SimpleSAML\Logger;
use SimpleSAML\Session;
use SimpleSAML\Utils;
use SimpleSAML\XHTML\Template;
use Throwable;

/**
 * Class that wraps SimpleSAMLphp errors in exceptions.
 *
 * @author Olav Morken, UNINETT AS.
 * @package SimpleSAMLphp
 */

class Error extends Exception
{
    /**
     * The error code.
     *
     * @var string
     */
    private $errorCode;

    /**
     * The http code.
     *
     * @var integer
     */
    protected $httpCode = 500;

    /**
     * The error title tag in dictionary.
     *
     * @var string
     */
    private $dictTitle;

    /**
     * The error description tag in dictionary.
     *
     * @var string
     */
    private $dictDescr;

    /**
     * The name of module that threw the error.
     *
     * @var string|null
     */
    private $module = null;

    /**
     * The parameters for the error.
     *
     * @var array
     */
    private $parameters;

    /**
     * Name of custom include template for the error.
     *
     * @var string|null
     */
    protected $includeTemplate = null;

    /**
     * Constructor for this error.
     *
     * The error can either be given as a string, or as an array. If it is an array, the first element in the array
     * (with index 0), is the error code, while the other elements are replacements for the error text.
     *
     * @param mixed      $errorCode One of the error codes defined in the errors dictionary.
     * @param \Throwable $cause The exception which caused this fatal error (if any). Optional.
     * @param int|null   $httpCode The HTTP response code to use. Optional.
     */
    public function __construct($errorCode, Throwable $cause = null, ?int $httpCode = null)
    {
        assert(is_string($errorCode) || is_array($errorCode));

        if (is_array($errorCode)) {
            $this->parameters = $errorCode;
            unset($this->parameters[0]);
            $this->errorCode = $errorCode[0];
        } else {
            $this->parameters = [];
            $this->errorCode = $errorCode;
        }

        if (isset($httpCode)) {
            $this->httpCode = $httpCode;
        }

        $this->dictTitle = ErrorCodes::getErrorCodeTitle($this->errorCode);
        $this->dictDescr = ErrorCodes::getErrorCodeDescription($this->errorCode);

        if (!empty($this->parameters)) {
            $msg = $this->errorCode . '(';
            foreach ($this->parameters as $k => $v) {
                if ($k === 0) {
                    continue;
                }

                $msg .= var_export($k, true) . ' => ' . var_export($v, true) . ', ';
            }
            $msg = substr($msg, 0, -2) . ')';
        } else {
            $msg = $this->errorCode;
        }
        parent::__construct($msg, -1, $cause);
    }


    /**
     * Retrieve the error code given when throwing this error.
     *
     * @return string  The error code.
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }


    /**
     * Retrieve the error parameters given when throwing this error.
     *
     * @return array  The parameters.
     */
    public function getParameters()
    {
        return $this->parameters;
    }


    /**
     * Retrieve the error title tag in dictionary.
     *
     * @return string  The error title tag.
     */
    public function getDictTitle()
    {
        return $this->dictTitle;
    }


    /**
     * Retrieve the error description tag in dictionary.
     *
     * @return string  The error description tag.
     */
    public function getDictDescr()
    {
        return $this->dictDescr;
    }


    /**
     * Set the HTTP return code for this error.
     *
     * This should be overridden by subclasses who want a different return code than 500 Internal Server Error.
     * @return void
     */
    protected function setHTTPCode()
    {
        http_response_code($this->httpCode);
    }


    /**
     * Save an error report.
     *
     * @return array  The array with the error report data.
     */
    protected function saveError()
    {
        $data = $this->format(true);
        $emsg = array_shift($data);
        $etrace = implode("\n", $data);

        $reportId = bin2hex(openssl_random_pseudo_bytes(4));
        Logger::error('Error report with id ' . $reportId . ' generated.');

        $config = Configuration::getInstance();
        $session = Session::getSessionFromRequest();

        if (isset($_SERVER['HTTP_REFERER'])) {
            $referer = $_SERVER['HTTP_REFERER'];
            // remove anything after the first '?' or ';', just in case it contains any sensitive data
            $referer = explode('?', $referer, 2);
            $referer = $referer[0];
            $referer = explode(';', $referer, 2);
            $referer = $referer[0];
        } else {
            $referer = 'unknown';
        }
        $errorData = [
            'exceptionMsg'   => $emsg,
            'exceptionTrace' => $etrace,
            'reportId'       => $reportId,
            'trackId'        => $session->getTrackID(),
            'url'            => Utils\HTTP::getSelfURLNoQuery(),
            'version'        => $config->getVersion(),
            'referer'        => $referer,
        ];
        $session->setData('core:errorreport', $reportId, $errorData);

        return $errorData;
    }


    /**
     * Display this error.
     *
     * This method displays a standard SimpleSAMLphp error page and exits.
     * @return void
     */
    public function show()
    {
        // log the error message
        $this->logError();

        $errorData = $this->saveError();
        $config = Configuration::getInstance();

        $data = [];
        $data['showerrors'] = $config->getBoolean('showerrors', true);
        $data['error'] = $errorData;
        $data['errorCode'] = $this->errorCode;
        $data['parameters'] = $this->parameters;
        $data['module'] = $this->module;
        $data['dictTitle'] = $this->dictTitle;
        $data['dictDescr'] = $this->dictDescr;
        $data['includeTemplate'] = $this->includeTemplate;
        $data['clipboard.js'] = true;

        // check if there is a valid technical contact email address
        if (
            $config->getBoolean('errorreporting', true)
            && $config->getString('technicalcontact_email', 'na@example.org') !== 'na@example.org'
        ) {
            // enable error reporting
            $baseurl = Utils\HTTP::getBaseURL();
            $data['errorReportAddress'] = $baseurl . 'errorreport.php';
        }

        $data['email'] = '';
        $session = Session::getSessionFromRequest();
        $authorities = $session->getAuthorities();
        foreach ($authorities as $authority) {
            $attributes = $session->getAuthData($authority, 'Attributes');
            if ($attributes !== null && array_key_exists('mail', $attributes) && count($attributes['mail']) > 0) {
                $data['email'] = $attributes['mail'][0];
                break; // enough, don't need to get all available mails, if more than one
            }
        }

        $show_function = $config->getArray('errors.show_function', null);
        if (isset($show_function)) {
            assert(is_callable($show_function));
            $this->setHTTPCode();
            call_user_func($show_function, $config, $data);
            assert(false);
        } else {
            $t = new Template($config, 'error.php', 'errors');
            $t->setStatusCode($this->httpCode);
            $t->data = array_merge($t->data, $data);
            $t->show();
        }

        exit;
    }
}
