<?php

namespace OpenSpout\Reader\Wrapper;

use OpenSpout\Reader\Exception\XMLProcessingException;

/**
 * Trait XMLInternalErrorsHelper.
 */
trait XMLInternalErrorsHelper
{
    /** @var bool Stores whether XML errors were initially stored internally - used to reset */
    protected $initialUseInternalErrorsValue;

    /**
     * To avoid displaying lots of warning/error messages on screen,
     * stores errors internally instead.
     */
    protected function useXMLInternalErrors()
    {
        libxml_clear_errors();
        $this->initialUseInternalErrorsValue = libxml_use_internal_errors(true);
    }

    /**
     * Throws an XMLProcessingException if an error occured.
     * It also always resets the "libxml_use_internal_errors" setting back to its initial value.
     *
     * @throws \OpenSpout\Reader\Exception\XMLProcessingException
     */
    protected function resetXMLInternalErrorsSettingAndThrowIfXMLErrorOccured()
    {
        if ($this->hasXMLErrorOccured()) {
            $this->resetXMLInternalErrorsSetting();

            throw new XMLProcessingException($this->getLastXMLErrorMessage());
        }

        $this->resetXMLInternalErrorsSetting();
    }

    protected function resetXMLInternalErrorsSetting()
    {
        libxml_use_internal_errors($this->initialUseInternalErrorsValue);
    }

    /**
     * Returns whether the a XML error has occured since the last time errors were cleared.
     *
     * @return bool TRUE if an error occured, FALSE otherwise
     */
    private function hasXMLErrorOccured()
    {
        return false !== libxml_get_last_error();
    }

    /**
     * Returns the error message for the last XML error that occured.
     *
     * @see libxml_get_last_error
     *
     * @return null|string Last XML error message or null if no error
     */
    private function getLastXMLErrorMessage()
    {
        $errorMessage = null;
        $error = libxml_get_last_error();

        if (false !== $error) {
            $errorMessage = trim($error->message);
        }

        return $errorMessage;
    }
}
