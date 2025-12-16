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

namespace Google\Service\DataManager;

class WarningCount extends \Google\Model
{
  /**
   * The processing warning reason is unknown.
   */
  public const REASON_PROCESSING_WARNING_REASON_UNSPECIFIED = 'PROCESSING_WARNING_REASON_UNSPECIFIED';
  /**
   * The system did not have the permissions needed to access the KEK.
   */
  public const REASON_PROCESSING_WARNING_REASON_KEK_PERMISSION_DENIED = 'PROCESSING_WARNING_REASON_KEK_PERMISSION_DENIED';
  /**
   * The DEK failed to be decrypted.
   */
  public const REASON_PROCESSING_WARNING_REASON_DEK_DECRYPTION_ERROR = 'PROCESSING_WARNING_REASON_DEK_DECRYPTION_ERROR';
  /**
   * The event has a decryption error.
   */
  public const REASON_PROCESSING_WARNING_REASON_DECRYPTION_ERROR = 'PROCESSING_WARNING_REASON_DECRYPTION_ERROR';
  /**
   * The WIP could not be used because it was rejected by its attestation
   * condition.
   */
  public const REASON_PROCESSING_WARNING_REASON_WIP_AUTH_FAILED = 'PROCESSING_WARNING_REASON_WIP_AUTH_FAILED';
  /**
   * The WIP is formatted incorrectly or the WIP does not exist.
   */
  public const REASON_PROCESSING_WARNING_REASON_INVALID_WIP = 'PROCESSING_WARNING_REASON_INVALID_WIP';
  /**
   * The KEK cannot decrypt data because it is the wrong KEK, or it does not
   * exist.
   */
  public const REASON_PROCESSING_WARNING_REASON_INVALID_KEK = 'PROCESSING_WARNING_REASON_INVALID_KEK';
  /**
   * Failed to decrypt th UserIdentifier data using the DEK.
   */
  public const REASON_PROCESSING_WARNING_REASON_USER_IDENTIFIER_DECRYPTION_ERROR = 'PROCESSING_WARNING_REASON_USER_IDENTIFIER_DECRYPTION_ERROR';
  /**
   * Internal error.
   */
  public const REASON_PROCESSING_WARNING_REASON_INTERNAL_ERROR = 'PROCESSING_WARNING_REASON_INTERNAL_ERROR';
  /**
   * The system failed to authenticate with AWS.
   */
  public const REASON_PROCESSING_WARNING_REASON_AWS_AUTH_FAILED = 'PROCESSING_WARNING_REASON_AWS_AUTH_FAILED';
  /**
   * The warning reason.
   *
   * @var string
   */
  public $reason;
  /**
   * The count of records that have a warning.
   *
   * @var string
   */
  public $recordCount;

  /**
   * The warning reason.
   *
   * Accepted values: PROCESSING_WARNING_REASON_UNSPECIFIED,
   * PROCESSING_WARNING_REASON_KEK_PERMISSION_DENIED,
   * PROCESSING_WARNING_REASON_DEK_DECRYPTION_ERROR,
   * PROCESSING_WARNING_REASON_DECRYPTION_ERROR,
   * PROCESSING_WARNING_REASON_WIP_AUTH_FAILED,
   * PROCESSING_WARNING_REASON_INVALID_WIP,
   * PROCESSING_WARNING_REASON_INVALID_KEK,
   * PROCESSING_WARNING_REASON_USER_IDENTIFIER_DECRYPTION_ERROR,
   * PROCESSING_WARNING_REASON_INTERNAL_ERROR,
   * PROCESSING_WARNING_REASON_AWS_AUTH_FAILED
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * The count of records that have a warning.
   *
   * @param string $recordCount
   */
  public function setRecordCount($recordCount)
  {
    $this->recordCount = $recordCount;
  }
  /**
   * @return string
   */
  public function getRecordCount()
  {
    return $this->recordCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(WarningCount::class, 'Google_Service_DataManager_WarningCount');
