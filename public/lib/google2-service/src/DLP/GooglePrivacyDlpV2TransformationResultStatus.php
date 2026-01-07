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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2TransformationResultStatus extends \Google\Model
{
  /**
   * Unused.
   */
  public const RESULT_STATUS_TYPE_STATE_TYPE_UNSPECIFIED = 'STATE_TYPE_UNSPECIFIED';
  /**
   * This will be set when a finding could not be transformed (i.e. outside user
   * set bucket range).
   */
  public const RESULT_STATUS_TYPE_INVALID_TRANSFORM = 'INVALID_TRANSFORM';
  /**
   * This will be set when a BigQuery transformation was successful but could
   * not be stored back in BigQuery because the transformed row exceeds
   * BigQuery's max row size.
   */
  public const RESULT_STATUS_TYPE_BIGQUERY_MAX_ROW_SIZE_EXCEEDED = 'BIGQUERY_MAX_ROW_SIZE_EXCEEDED';
  /**
   * This will be set when there is a finding in the custom metadata of a file,
   * but at the write time of the transformed file, this key / value pair is
   * unretrievable.
   */
  public const RESULT_STATUS_TYPE_METADATA_UNRETRIEVABLE = 'METADATA_UNRETRIEVABLE';
  /**
   * This will be set when the transformation and storing of it is successful.
   */
  public const RESULT_STATUS_TYPE_SUCCESS = 'SUCCESS';
  protected $detailsType = GoogleRpcStatus::class;
  protected $detailsDataType = '';
  /**
   * Transformation result status type, this will be either SUCCESS, or it will
   * be the reason for why the transformation was not completely successful.
   *
   * @var string
   */
  public $resultStatusType;

  /**
   * Detailed error codes and messages
   *
   * @param GoogleRpcStatus $details
   */
  public function setDetails(GoogleRpcStatus $details)
  {
    $this->details = $details;
  }
  /**
   * @return GoogleRpcStatus
   */
  public function getDetails()
  {
    return $this->details;
  }
  /**
   * Transformation result status type, this will be either SUCCESS, or it will
   * be the reason for why the transformation was not completely successful.
   *
   * Accepted values: STATE_TYPE_UNSPECIFIED, INVALID_TRANSFORM,
   * BIGQUERY_MAX_ROW_SIZE_EXCEEDED, METADATA_UNRETRIEVABLE, SUCCESS
   *
   * @param self::RESULT_STATUS_TYPE_* $resultStatusType
   */
  public function setResultStatusType($resultStatusType)
  {
    $this->resultStatusType = $resultStatusType;
  }
  /**
   * @return self::RESULT_STATUS_TYPE_*
   */
  public function getResultStatusType()
  {
    return $this->resultStatusType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2TransformationResultStatus::class, 'Google_Service_DLP_GooglePrivacyDlpV2TransformationResultStatus');
