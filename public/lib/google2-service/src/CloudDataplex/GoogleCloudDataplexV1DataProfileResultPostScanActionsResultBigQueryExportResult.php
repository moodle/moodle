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

namespace Google\Service\CloudDataplex;

class GoogleCloudDataplexV1DataProfileResultPostScanActionsResultBigQueryExportResult extends \Google\Model
{
  /**
   * The exporting state is unspecified.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The exporting completed successfully.
   */
  public const STATE_SUCCEEDED = 'SUCCEEDED';
  /**
   * The exporting is no longer running due to an error.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The exporting is skipped due to no valid scan result to export (usually
   * caused by scan failed).
   */
  public const STATE_SKIPPED = 'SKIPPED';
  /**
   * Output only. Additional information about the BigQuery exporting.
   *
   * @var string
   */
  public $message;
  /**
   * Output only. Execution state for the BigQuery exporting.
   *
   * @var string
   */
  public $state;

  /**
   * Output only. Additional information about the BigQuery exporting.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Output only. Execution state for the BigQuery exporting.
   *
   * Accepted values: STATE_UNSPECIFIED, SUCCEEDED, FAILED, SKIPPED
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
class_alias(GoogleCloudDataplexV1DataProfileResultPostScanActionsResultBigQueryExportResult::class, 'Google_Service_CloudDataplex_GoogleCloudDataplexV1DataProfileResultPostScanActionsResultBigQueryExportResult');
