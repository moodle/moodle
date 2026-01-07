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

namespace Google\Service\Eventarc;

class GoogleCloudEventarcV1PipelineRetryPolicy extends \Google\Model
{
  /**
   * Optional. The maximum number of delivery attempts for any message. The
   * value must be between 1 and 100. The default value for this field is 5.
   *
   * @var int
   */
  public $maxAttempts;
  /**
   * Optional. The maximum amount of seconds to wait between retry attempts. The
   * value must be between 1 and 600. The default value for this field is 60.
   *
   * @var string
   */
  public $maxRetryDelay;
  /**
   * Optional. The minimum amount of seconds to wait between retry attempts. The
   * value must be between 1 and 600. The default value for this field is 5.
   *
   * @var string
   */
  public $minRetryDelay;

  /**
   * Optional. The maximum number of delivery attempts for any message. The
   * value must be between 1 and 100. The default value for this field is 5.
   *
   * @param int $maxAttempts
   */
  public function setMaxAttempts($maxAttempts)
  {
    $this->maxAttempts = $maxAttempts;
  }
  /**
   * @return int
   */
  public function getMaxAttempts()
  {
    return $this->maxAttempts;
  }
  /**
   * Optional. The maximum amount of seconds to wait between retry attempts. The
   * value must be between 1 and 600. The default value for this field is 60.
   *
   * @param string $maxRetryDelay
   */
  public function setMaxRetryDelay($maxRetryDelay)
  {
    $this->maxRetryDelay = $maxRetryDelay;
  }
  /**
   * @return string
   */
  public function getMaxRetryDelay()
  {
    return $this->maxRetryDelay;
  }
  /**
   * Optional. The minimum amount of seconds to wait between retry attempts. The
   * value must be between 1 and 600. The default value for this field is 5.
   *
   * @param string $minRetryDelay
   */
  public function setMinRetryDelay($minRetryDelay)
  {
    $this->minRetryDelay = $minRetryDelay;
  }
  /**
   * @return string
   */
  public function getMinRetryDelay()
  {
    return $this->minRetryDelay;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudEventarcV1PipelineRetryPolicy::class, 'Google_Service_Eventarc_GoogleCloudEventarcV1PipelineRetryPolicy');
