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

class GoogleCloudRetailV2ExperimentInfo extends \Google\Model
{
  /**
   * The fully qualified resource name of the experiment that provides the
   * serving config under test, should an active experiment exist. For example:
   * `projects/locations/global/catalogs/default_catalog/experiments/experiment_
   * id`
   *
   * @var string
   */
  public $experiment;
  protected $servingConfigExperimentType = GoogleCloudRetailV2ExperimentInfoServingConfigExperiment::class;
  protected $servingConfigExperimentDataType = '';

  /**
   * The fully qualified resource name of the experiment that provides the
   * serving config under test, should an active experiment exist. For example:
   * `projects/locations/global/catalogs/default_catalog/experiments/experiment_
   * id`
   *
   * @param string $experiment
   */
  public function setExperiment($experiment)
  {
    $this->experiment = $experiment;
  }
  /**
   * @return string
   */
  public function getExperiment()
  {
    return $this->experiment;
  }
  /**
   * A/B test between existing Cloud Retail Search ServingConfigs.
   *
   * @param GoogleCloudRetailV2ExperimentInfoServingConfigExperiment $servingConfigExperiment
   */
  public function setServingConfigExperiment(GoogleCloudRetailV2ExperimentInfoServingConfigExperiment $servingConfigExperiment)
  {
    $this->servingConfigExperiment = $servingConfigExperiment;
  }
  /**
   * @return GoogleCloudRetailV2ExperimentInfoServingConfigExperiment
   */
  public function getServingConfigExperiment()
  {
    return $this->servingConfigExperiment;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ExperimentInfo::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ExperimentInfo');
