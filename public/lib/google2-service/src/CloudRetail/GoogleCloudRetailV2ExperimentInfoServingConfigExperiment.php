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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2ExperimentInfoServingConfigExperiment extends \Google\Model
{
  /**
   * The fully qualified resource name of the serving config
   * `Experiment.VariantArm.serving_config_id` responsible for generating the
   * search response. For example: `projects/locations/catalogs/servingConfigs`.
   *
   * @var string
   */
  public $experimentServingConfig;
  /**
   * The fully qualified resource name of the original SearchRequest.placement
   * in the search request prior to reassignment by experiment API. For example:
   * `projects/locations/catalogs/servingConfigs`.
   *
   * @var string
   */
  public $originalServingConfig;

  /**
   * The fully qualified resource name of the serving config
   * `Experiment.VariantArm.serving_config_id` responsible for generating the
   * search response. For example: `projects/locations/catalogs/servingConfigs`.
   *
   * @param string $experimentServingConfig
   */
  public function setExperimentServingConfig($experimentServingConfig)
  {
    $this->experimentServingConfig = $experimentServingConfig;
  }
  /**
   * @return string
   */
  public function getExperimentServingConfig()
  {
    return $this->experimentServingConfig;
  }
  /**
   * The fully qualified resource name of the original SearchRequest.placement
   * in the search request prior to reassignment by experiment API. For example:
   * `projects/locations/catalogs/servingConfigs`.
   *
   * @param string $originalServingConfig
   */
  public function setOriginalServingConfig($originalServingConfig)
  {
    $this->originalServingConfig = $originalServingConfig;
  }
  /**
   * @return string
   */
  public function getOriginalServingConfig()
  {
    return $this->originalServingConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ExperimentInfoServingConfigExperiment::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ExperimentInfoServingConfigExperiment');
