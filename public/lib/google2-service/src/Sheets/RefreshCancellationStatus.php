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

namespace Google\Service\Sheets;

class RefreshCancellationStatus extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const ERROR_CODE_REFRESH_CANCELLATION_ERROR_CODE_UNSPECIFIED = 'REFRESH_CANCELLATION_ERROR_CODE_UNSPECIFIED';
  /**
   * Execution to be cancelled not found in the query engine or in Sheets.
   */
  public const ERROR_CODE_EXECUTION_NOT_FOUND = 'EXECUTION_NOT_FOUND';
  /**
   * The user does not have permission to cancel the query.
   */
  public const ERROR_CODE_CANCEL_PERMISSION_DENIED = 'CANCEL_PERMISSION_DENIED';
  /**
   * The query execution has already completed and thus could not be cancelled.
   */
  public const ERROR_CODE_QUERY_EXECUTION_COMPLETED = 'QUERY_EXECUTION_COMPLETED';
  /**
   * There is already another cancellation in process.
   */
  public const ERROR_CODE_CONCURRENT_CANCELLATION = 'CONCURRENT_CANCELLATION';
  /**
   * All other errors.
   */
  public const ERROR_CODE_CANCEL_OTHER_ERROR = 'CANCEL_OTHER_ERROR';
  /**
   * Default value, do not use.
   */
  public const STATE_REFRESH_CANCELLATION_STATE_UNSPECIFIED = 'REFRESH_CANCELLATION_STATE_UNSPECIFIED';
  /**
   * The API call to Sheets to cancel a refresh has succeeded. This does not
   * mean that the cancel happened successfully, but that the call has been made
   * successfully.
   */
  public const STATE_CANCEL_SUCCEEDED = 'CANCEL_SUCCEEDED';
  /**
   * The API call to Sheets to cancel a refresh has failed.
   */
  public const STATE_CANCEL_FAILED = 'CANCEL_FAILED';
  /**
   * The error code.
   *
   * @var string
   */
  public $errorCode;
  /**
   * The state of a call to cancel a refresh in Sheets.
   *
   * @var string
   */
  public $state;

  /**
   * The error code.
   *
   * Accepted values: REFRESH_CANCELLATION_ERROR_CODE_UNSPECIFIED,
   * EXECUTION_NOT_FOUND, CANCEL_PERMISSION_DENIED, QUERY_EXECUTION_COMPLETED,
   * CONCURRENT_CANCELLATION, CANCEL_OTHER_ERROR
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
   * The state of a call to cancel a refresh in Sheets.
   *
   * Accepted values: REFRESH_CANCELLATION_STATE_UNSPECIFIED, CANCEL_SUCCEEDED,
   * CANCEL_FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RefreshCancellationStatus::class, 'Google_Service_Sheets_RefreshCancellationStatus');
