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

namespace Google\Service\SQLAdmin;

class ApiWarning extends \Google\Model
{
  /**
   * An unknown or unset warning type from Cloud SQL API.
   */
  public const CODE_SQL_API_WARNING_CODE_UNSPECIFIED = 'SQL_API_WARNING_CODE_UNSPECIFIED';
  /**
   * Warning when one or more regions are not reachable. The returned result set
   * may be incomplete.
   */
  public const CODE_REGION_UNREACHABLE = 'REGION_UNREACHABLE';
  /**
   * Warning when user provided maxResults parameter exceeds the limit. The
   * returned result set may be incomplete.
   */
  public const CODE_MAX_RESULTS_EXCEEDS_LIMIT = 'MAX_RESULTS_EXCEEDS_LIMIT';
  /**
   * Warning when user tries to create/update a user with credentials that have
   * previously been compromised by a public data breach.
   */
  public const CODE_COMPROMISED_CREDENTIALS = 'COMPROMISED_CREDENTIALS';
  /**
   * Warning when the operation succeeds but some non-critical workflow state
   * failed.
   */
  public const CODE_INTERNAL_STATE_FAILURE = 'INTERNAL_STATE_FAILURE';
  /**
   * Code to uniquely identify the warning type.
   *
   * @var string
   */
  public $code;
  /**
   * The warning message.
   *
   * @var string
   */
  public $message;
  /**
   * The region name for REGION_UNREACHABLE warning.
   *
   * @var string
   */
  public $region;

  /**
   * Code to uniquely identify the warning type.
   *
   * Accepted values: SQL_API_WARNING_CODE_UNSPECIFIED, REGION_UNREACHABLE,
   * MAX_RESULTS_EXCEEDS_LIMIT, COMPROMISED_CREDENTIALS, INTERNAL_STATE_FAILURE
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
   * The warning message.
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
   * The region name for REGION_UNREACHABLE warning.
   *
   * @param string $region
   */
  public function setRegion($region)
  {
    $this->region = $region;
  }
  /**
   * @return string
   */
  public function getRegion()
  {
    return $this->region;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApiWarning::class, 'Google_Service_SQLAdmin_ApiWarning');
