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

class GoogleCloudAiplatformV1SummarizationQualitySpec extends \Google\Model
{
  /**
   * Optional. Whether to use instance.reference to compute summarization
   * quality.
   *
   * @var bool
   */
  public $useReference;
  /**
   * Optional. Which version to use for evaluation.
   *
   * @var int
   */
  public $version;

  /**
   * Optional. Whether to use instance.reference to compute summarization
   * quality.
   *
   * @param bool $useReference
   */
  public function setUseReference($useReference)
  {
    $this->useReference = $useReference;
  }
  /**
   * @return bool
   */
  public function getUseReference()
  {
    return $this->useReference;
  }
  /**
   * Optional. Which version to use for evaluation.
   *
   * @param int $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return int
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SummarizationQualitySpec::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SummarizationQualitySpec');
