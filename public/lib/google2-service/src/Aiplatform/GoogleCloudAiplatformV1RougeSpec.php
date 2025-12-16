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

class GoogleCloudAiplatformV1RougeSpec extends \Google\Model
{
  /**
   * Optional. Supported rouge types are rougen[1-9], rougeL, and rougeLsum.
   *
   * @var string
   */
  public $rougeType;
  /**
   * Optional. Whether to split summaries while using rougeLsum.
   *
   * @var bool
   */
  public $splitSummaries;
  /**
   * Optional. Whether to use stemmer to compute rouge score.
   *
   * @var bool
   */
  public $useStemmer;

  /**
   * Optional. Supported rouge types are rougen[1-9], rougeL, and rougeLsum.
   *
   * @param string $rougeType
   */
  public function setRougeType($rougeType)
  {
    $this->rougeType = $rougeType;
  }
  /**
   * @return string
   */
  public function getRougeType()
  {
    return $this->rougeType;
  }
  /**
   * Optional. Whether to split summaries while using rougeLsum.
   *
   * @param bool $splitSummaries
   */
  public function setSplitSummaries($splitSummaries)
  {
    $this->splitSummaries = $splitSummaries;
  }
  /**
   * @return bool
   */
  public function getSplitSummaries()
  {
    return $this->splitSummaries;
  }
  /**
   * Optional. Whether to use stemmer to compute rouge score.
   *
   * @param bool $useStemmer
   */
  public function setUseStemmer($useStemmer)
  {
    $this->useStemmer = $useStemmer;
  }
  /**
   * @return bool
   */
  public function getUseStemmer()
  {
    return $this->useStemmer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RougeSpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RougeSpec');
