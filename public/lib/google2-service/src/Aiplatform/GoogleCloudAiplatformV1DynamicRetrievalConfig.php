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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DynamicRetrievalConfig extends \Google\Model
{
  /**
   * Always trigger retrieval.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Run retrieval only when system decides it is necessary.
   */
  public const MODE_MODE_DYNAMIC = 'MODE_DYNAMIC';
  /**
   * Optional. The threshold to be used in dynamic retrieval. If not set, a
   * system default value is used.
   *
   * @var float
   */
  public $dynamicThreshold;
  /**
   * The mode of the predictor to be used in dynamic retrieval.
   *
   * @var string
   */
  public $mode;

  /**
   * Optional. The threshold to be used in dynamic retrieval. If not set, a
   * system default value is used.
   *
   * @param float $dynamicThreshold
   */
  public function setDynamicThreshold($dynamicThreshold)
  {
    $this->dynamicThreshold = $dynamicThreshold;
  }
  /**
   * @return float
   */
  public function getDynamicThreshold()
  {
    return $this->dynamicThreshold;
  }
  /**
   * The mode of the predictor to be used in dynamic retrieval.
   *
   * Accepted values: MODE_UNSPECIFIED, MODE_DYNAMIC
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DynamicRetrievalConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DynamicRetrievalConfig');
