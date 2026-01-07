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

namespace Google\Service\Integrations;

class EnterpriseCrmEventbusProtoErrorDetail extends \Google\Model
{
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  public const SEVERITY_ERROR = 'ERROR';
  public const SEVERITY_WARN = 'WARN';
  public const SEVERITY_INFO = 'INFO';
  protected $errorCodeType = CrmlogErrorCode::class;
  protected $errorCodeDataType = '';
  /**
   * The full text of the error message, including any parameters that were
   * thrown along with the exception.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * The severity of the error: ERROR|WARN|INFO.
   *
   * @var string
   */
  public $severity;
  /**
   * The task try-number, in which, the error occurred. If zero, the error
   * happened at the event level.
   *
   * @var int
   */
  public $taskNumber;

  /**
   * The associated error-code, which can be a common or internal code.
   *
   * @param CrmlogErrorCode $errorCode
   */
  public function setErrorCode(CrmlogErrorCode $errorCode)
  {
    $this->errorCode = $errorCode;
  }
  /**
   * @return CrmlogErrorCode
   */
  public function getErrorCode()
  {
    return $this->errorCode;
  }
  /**
   * The full text of the error message, including any parameters that were
   * thrown along with the exception.
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
   * The severity of the error: ERROR|WARN|INFO.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, ERROR, WARN, INFO
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * The task try-number, in which, the error occurred. If zero, the error
   * happened at the event level.
   *
   * @param int $taskNumber
   */
  public function setTaskNumber($taskNumber)
  {
    $this->taskNumber = $taskNumber;
  }
  /**
   * @return int
   */
  public function getTaskNumber()
  {
    return $this->taskNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EnterpriseCrmEventbusProtoErrorDetail::class, 'Google_Service_Integrations_EnterpriseCrmEventbusProtoErrorDetail');
