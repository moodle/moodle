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

class DataExecutionStatus extends \Google\Model
{
  /**
   * Default value, do not use.
   */
  public const ERROR_CODE_DATA_EXECUTION_ERROR_CODE_UNSPECIFIED = 'DATA_EXECUTION_ERROR_CODE_UNSPECIFIED';
  /**
   * The data execution timed out.
   */
  public const ERROR_CODE_TIMED_OUT = 'TIMED_OUT';
  /**
   * The data execution returns more rows than the limit.
   */
  public const ERROR_CODE_TOO_MANY_ROWS = 'TOO_MANY_ROWS';
  /**
   * The data execution returns more columns than the limit.
   */
  public const ERROR_CODE_TOO_MANY_COLUMNS = 'TOO_MANY_COLUMNS';
  /**
   * The data execution returns more cells than the limit.
   */
  public const ERROR_CODE_TOO_MANY_CELLS = 'TOO_MANY_CELLS';
  /**
   * Error is received from the backend data execution engine (e.g. BigQuery).
   * Check error_message for details.
   */
  public const ERROR_CODE_ENGINE = 'ENGINE';
  /**
   * One or some of the provided data source parameters are invalid.
   */
  public const ERROR_CODE_PARAMETER_INVALID = 'PARAMETER_INVALID';
  /**
   * The data execution returns an unsupported data type.
   */
  public const ERROR_CODE_UNSUPPORTED_DATA_TYPE = 'UNSUPPORTED_DATA_TYPE';
  /**
   * The data execution returns duplicate column names or aliases.
   */
  public const ERROR_CODE_DUPLICATE_COLUMN_NAMES = 'DUPLICATE_COLUMN_NAMES';
  /**
   * The data execution is interrupted. Please refresh later.
   */
  public const ERROR_CODE_INTERRUPTED = 'INTERRUPTED';
  /**
   * The data execution is currently in progress, can not be refreshed until it
   * completes.
   */
  public const ERROR_CODE_CONCURRENT_QUERY = 'CONCURRENT_QUERY';
  /**
   * Other errors.
   */
  public const ERROR_CODE_OTHER = 'OTHER';
  /**
   * The data execution returns values that exceed the maximum characters
   * allowed in a single cell.
   */
  public const ERROR_CODE_TOO_MANY_CHARS_PER_CELL = 'TOO_MANY_CHARS_PER_CELL';
  /**
   * The database referenced by the data source is not found.
   */
  public const ERROR_CODE_DATA_NOT_FOUND = 'DATA_NOT_FOUND';
  /**
   * The user does not have access to the database referenced by the data
   * source.
   */
  public const ERROR_CODE_PERMISSION_DENIED = 'PERMISSION_DENIED';
  /**
   * The data execution returns columns with missing aliases.
   */
  public const ERROR_CODE_MISSING_COLUMN_ALIAS = 'MISSING_COLUMN_ALIAS';
  /**
   * The data source object does not exist.
   */
  public const ERROR_CODE_OBJECT_NOT_FOUND = 'OBJECT_NOT_FOUND';
  /**
   * The data source object is currently in error state. To force refresh, set
   * force in RefreshDataSourceRequest.
   */
  public const ERROR_CODE_OBJECT_IN_ERROR_STATE = 'OBJECT_IN_ERROR_STATE';
  /**
   * The data source object specification is invalid.
   */
  public const ERROR_CODE_OBJECT_SPEC_INVALID = 'OBJECT_SPEC_INVALID';
  /**
   * The data execution has been cancelled.
   */
  public const ERROR_CODE_DATA_EXECUTION_CANCELLED = 'DATA_EXECUTION_CANCELLED';
  /**
   * Default value, do not use.
   */
  public const STATE_DATA_EXECUTION_STATE_UNSPECIFIED = 'DATA_EXECUTION_STATE_UNSPECIFIED';
  /**
   * The data execution has not started.
   */
  public const STATE_NOT_STARTED = 'NOT_STARTED';
  /**
   * The data execution has started and is running.
   */
  public const STATE_RUNNING = 'RUNNING';
  /**
   * The data execution is currently being cancelled.
   */
  public const STATE_CANCELLING = 'CANCELLING';
  /**
   * The data execution has completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The data execution has completed with errors.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The error code.
   *
   * @var string
   */
  public $errorCode;
  /**
   * The error message, which may be empty.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Gets the time the data last successfully refreshed.
   *
   * @var string
   */
  public $lastRefreshTime;
  /**
   * The state of the data execution.
   *
   * @var string
   */
  public $state;

  /**
   * The error code.
   *
   * Accepted values: DATA_EXECUTION_ERROR_CODE_UNSPECIFIED, TIMED_OUT,
   * TOO_MANY_ROWS, TOO_MANY_COLUMNS, TOO_MANY_CELLS, ENGINE, PARAMETER_INVALID,
   * UNSUPPORTED_DATA_TYPE, DUPLICATE_COLUMN_NAMES, INTERRUPTED,
   * CONCURRENT_QUERY, OTHER, TOO_MANY_CHARS_PER_CELL, DATA_NOT_FOUND,
   * PERMISSION_DENIED, MISSING_COLUMN_ALIAS, OBJECT_NOT_FOUND,
   * OBJECT_IN_ERROR_STATE, OBJECT_SPEC_INVALID, DATA_EXECUTION_CANCELLED
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
   * The error message, which may be empty.
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
   * Gets the time the data last successfully refreshed.
   *
   * @param string $lastRefreshTime
   */
  public function setLastRefreshTime($lastRefreshTime)
  {
    $this->lastRefreshTime = $lastRefreshTime;
  }
  /**
   * @return string
   */
  public function getLastRefreshTime()
  {
    return $this->lastRefreshTime;
  }
  /**
   * The state of the data execution.
   *
   * Accepted values: DATA_EXECUTION_STATE_UNSPECIFIED, NOT_STARTED, RUNNING,
   * CANCELLING, SUCCEEDED, FAILED
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
class_alias(DataExecutionStatus::class, 'Google_Service_Sheets_DataExecutionStatus');
