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

namespace Google\Service\Appengine;

class ErrorHandler extends \Google\Model
{
  /**
   * Not specified. ERROR_CODE_DEFAULT is assumed.
   */
  public const ERROR_CODE_ERROR_CODE_UNSPECIFIED = 'ERROR_CODE_UNSPECIFIED';
  /**
   * All other error types.
   */
  public const ERROR_CODE_ERROR_CODE_DEFAULT = 'ERROR_CODE_DEFAULT';
  /**
   * Application has exceeded a resource quota.
   */
  public const ERROR_CODE_ERROR_CODE_OVER_QUOTA = 'ERROR_CODE_OVER_QUOTA';
  /**
   * Client blocked by the application's Denial of Service protection
   * configuration.
   */
  public const ERROR_CODE_ERROR_CODE_DOS_API_DENIAL = 'ERROR_CODE_DOS_API_DENIAL';
  /**
   * Deadline reached before the application responds.
   */
  public const ERROR_CODE_ERROR_CODE_TIMEOUT = 'ERROR_CODE_TIMEOUT';
  /**
   * Error condition this handler applies to.
   *
   * @var string
   */
  public $errorCode;
  /**
   * MIME type of file. Defaults to text/html.
   *
   * @var string
   */
  public $mimeType;
  /**
   * Static file content to be served for this error.
   *
   * @var string
   */
  public $staticFile;

  /**
   * Error condition this handler applies to.
   *
   * Accepted values: ERROR_CODE_UNSPECIFIED, ERROR_CODE_DEFAULT,
   * ERROR_CODE_OVER_QUOTA, ERROR_CODE_DOS_API_DENIAL, ERROR_CODE_TIMEOUT
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
   * MIME type of file. Defaults to text/html.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * Static file content to be served for this error.
   *
   * @param string $staticFile
   */
  public function setStaticFile($staticFile)
  {
    $this->staticFile = $staticFile;
  }
  /**
   * @return string
   */
  public function getStaticFile()
  {
    return $this->staticFile;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorHandler::class, 'Google_Service_Appengine_ErrorHandler');
