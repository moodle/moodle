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

class GooglePrivacyDlpV2DeidentifyDataSourceStats extends \Google\Model
{
  /**
   * Number of successfully applied transformations.
   *
   * @var string
   */
  public $transformationCount;
  /**
   * Number of errors encountered while trying to apply transformations.
   *
   * @var string
   */
  public $transformationErrorCount;
  /**
   * Total size in bytes that were transformed in some way.
   *
   * @var string
   */
  public $transformedBytes;

  /**
   * Number of successfully applied transformations.
   *
   * @param string $transformationCount
   */
  public function setTransformationCount($transformationCount)
  {
    $this->transformationCount = $transformationCount;
  }
  /**
   * @return string
   */
  public function getTransformationCount()
  {
    return $this->transformationCount;
  }
  /**
   * Number of errors encountered while trying to apply transformations.
   *
   * @param string $transformationErrorCount
   */
  public function setTransformationErrorCount($transformationErrorCount)
  {
    $this->transformationErrorCount = $transformationErrorCount;
  }
  /**
   * @return string
   */
  public function getTransformationErrorCount()
  {
    return $this->transformationErrorCount;
  }
  /**
   * Total size in bytes that were transformed in some way.
   *
   * @param string $transformedBytes
   */
  public function setTransformedBytes($transformedBytes)
  {
    $this->transformedBytes = $transformedBytes;
  }
  /**
   * @return string
   */
  public function getTransformedBytes()
  {
    return $this->transformedBytes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2DeidentifyDataSourceStats::class, 'Google_Service_DLP_GooglePrivacyDlpV2DeidentifyDataSourceStats');
