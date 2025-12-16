<?php
/*
 * Copyright 2014 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

namespace Google\Service\Acceleratedmobilepageurl;

class AmpUrlError extends \Google\Model
{
  /**
   * Not specified error.
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * Indicates the requested URL is not found in the index, possibly because
   * it's unable to be found, not able to be accessed by Googlebot, or some
   * other error.
   */
  public const ERROR_CODE_INPUT_URL_NOT_FOUND = 'INPUT_URL_NOT_FOUND';
  /**
   * Indicates no AMP URL has been found that corresponds to the requested URL.
   */
  public const ERROR_CODE_NO_AMP_URL = 'NO_AMP_URL';
  /**
   * Indicates some kind of application error occurred at the server. Client
   * advised to retry.
   */
  public const ERROR_CODE_APPLICATION_ERROR = 'APPLICATION_ERROR';
  /**
   * DEPRECATED: Indicates the requested URL is a valid AMP URL. This is a non-
   * error state, should not be relied upon as a sign of success or failure. It
   * will be removed in future versions of the API.
   *
   * @deprecated
   */
  public const ERROR_CODE_URL_IS_VALID_AMP = 'URL_IS_VALID_AMP';
  /**
   * Indicates that an AMP URL has been found that corresponds to the request
   * URL, but it is not valid AMP HTML.
   */
  public const ERROR_CODE_URL_IS_INVALID_AMP = 'URL_IS_INVALID_AMP';
  /**
   * The error code of an API call.
   *
   * @var string
   */
  public $errorCode;
  /**
   * An optional descriptive error message.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The original non-AMP URL.
   *
   * @var string
   */
  public $originalUrl;

  /**
   * The error code of an API call.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, INPUT_URL_NOT_FOUND, NO_AMP_URL,
   * APPLICATION_ERROR, URL_IS_VALID_AMP, URL_IS_INVALID_AMP
   *
   * @param self::ERROR_CODE_* $errorCode
   */
  public function setErrorCode($errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return self::ERROR_CODE_*
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * An optional descriptive error message.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * The original non-AMP URL.
   *
   * @param string $originalUrl
   */
  public function setOriginalUrl($originalUrl)
  {
    $this->originalUrl = $originalUrl;
  }
  /**
   * @return string
   */
  public function getOriginalUrl()
  {
    return $this->originalUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AmpUrlError::class, 'Google_Service_Acceleratedmobilepageurl_AmpUrlError');
