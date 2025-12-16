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

class GoogleCloudAiplatformV1Examples extends \Google\Model
{
  protected $exampleGcsSourceType = GoogleCloudAiplatformV1ExamplesExampleGcsSource::class;
  protected $exampleGcsSourceDataType = '';
  /**
   * The full configuration for the generated index, the semantics are the same
   * as metadata and should match
   * [NearestNeighborSearchConfig](https://cloud.google.com/vertex-
   * ai/docs/explainable-ai/configuring-explanations-example-based#nearest-
   * neighbor-search-config).
   *
   * @var array
   */
  public $nearestNeighborSearchConfig;
  /**
   * The number of neighbors to return when querying for examples.
   *
   * @var int
   */
  public $neighborCount;
  protected $presetsType = GoogleCloudAiplatformV1Presets::class;
  protected $presetsDataType = '';

  /**
   * The Cloud Storage input instances.
   *
   * @param GoogleCloudAiplatformV1ExamplesExampleGcsSource $exampleGcsSource
   */
  public function setExampleGcsSource(GoogleCloudAiplatformV1ExamplesExampleGcsSource $exampleGcsSource)
  {
    $this->exampleGcsSource = $exampleGcsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1ExamplesExampleGcsSource
   */
  public function getExampleGcsSource()
  {
    return $this->exampleGcsSource;
  }
  /**
   * The full configuration for the generated index, the semantics are the same
   * as metadata and should match
   * [NearestNeighborSearchConfig](https://cloud.google.com/vertex-
   * ai/docs/explainable-ai/configuring-explanations-example-based#nearest-
   * neighbor-search-config).
   *
   * @param array $nearestNeighborSearchConfig
   */
  public function setNearestNeighborSearchConfig($nearestNeighborSearchConfig)
  {
    $this->nearestNeighborSearchConfig = $nearestNeighborSearchConfig;
  }
  /**
   * @return array
   */
  public function getNearestNeighborSearchConfig()
  {
    return $this->nearestNeighborSearchConfig;
  }
  /**
   * The number of neighbors to return when querying for examples.
   *
   * @param int $neighborCount
   */
  public function setNeighborCount($neighborCount)
  {
    $this->neighborCount = $neighborCount;
  }
  /**
   * @return int
   */
  public function getNeighborCount()
  {
    return $this->neighborCount;
  }
  /**
   * Simplified preset configuration, which automatically sets configuration
   * values based on the desired query speed-precision trade-off and modality.
   *
   * @param GoogleCloudAiplatformV1Presets $presets
   */
  public function setPresets(GoogleCloudAiplatformV1Presets $presets)
  {
    $this->presets = $presets;
  }
  /**
   * @return GoogleCloudAiplatformV1Presets
   */
  public function getPresets()
  {
    return $this->presets;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1Examples::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1Examples');
