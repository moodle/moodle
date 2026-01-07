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

class GooglePrivacyDlpV2SummaryResult extends \Google\Model
{
  /**
   * Unused
   */
  public const CODE_TRANSFORMATION_RESULT_CODE_UNSPECIFIED = 'TRANSFORMATION_RESULT_CODE_UNSPECIFIED';
  /**
   * Transformation completed without an error.
   */
  public const CODE_SUCCESS = 'SUCCESS';
  /**
   * Transformation had an error.
   */
  public const CODE_ERROR = 'ERROR';
  /**
   * Outcome of the transformation.
   *
   * @var string
   */
  public $code;
  /**
   * Number of transformations counted by this result.
   *
   * @var string
   */
  public $count;
  /**
   * A place for warnings or errors to show up if a transformation didn't work
   * as expected.
   *
   * @var string
   */
  public $details;

  /**
   * Outcome of the transformation.
   *
   * Accepted values: TRANSFORMATION_RESULT_CODE_UNSPECIFIED, SUCCESS, ERROR
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * Number of transformations counted by this result.
   *
   * @param string $count
   */
  public function setCount($count)
  {
    $this->count = $count;
  }
  /**
   * @return string
   */
  public function getCount()
  {
    return $this->count;
  }
  /**
   * A place for warnings or errors to show up if a transformation didn't work
   * as expected.
   *
   * @param string $details
   */
  public function setDetails($details)
  {
    $this->details = $details;
  }
  /**
   * @return string
   */
  public function getDetails()
  {
    return $this->details;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2SummaryResult::class, 'Google_Service_DLP_GooglePrivacyDlpV2SummaryResult');
