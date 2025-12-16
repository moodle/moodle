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

namespace Google\Service\CloudNaturalLanguage;

class XPSImageModelServingSpec extends \Google\Collection
{
  protected $collection_key = 'modelThroughputEstimation';
  protected $modelThroughputEstimationType = XPSImageModelServingSpecModelThroughputEstimation::class;
  protected $modelThroughputEstimationDataType = 'array';
  /**
   * An estimated value of how much traffic a node can serve. Populated for
   * AutoMl request only.
   *
   * @var 
   */
  public $nodeQps;
  /**
   * ## The fields below are only populated under uCAIP request scope.
   * https://cloud.google.com/ml-engine/docs/runtime-version-list
   *
   * @var string
   */
  public $tfRuntimeVersion;

  /**
   * Populate under uCAIP request scope.
   *
   * @param XPSImageModelServingSpecModelThroughputEstimation[] $modelThroughputEstimation
   */
  public function setModelThroughputEstimation($modelThroughputEstimation)
  {
    $this->modelThroughputEstimation = $modelThroughputEstimation;
  }
  /**
   * @return XPSImageModelServingSpecModelThroughputEstimation[]
   */
  public function getModelThroughputEstimation()
  {
    return $this->modelThroughputEstimation;
  }
  public function setNodeQps($nodeQps)
  {
    $this->nodeQps = $nodeQps;
  }
  public function getNodeQps()
  {
    return $this->nodeQps;
  }
  /**
   * ## The fields below are only populated under uCAIP request scope.
   * https://cloud.google.com/ml-engine/docs/runtime-version-list
   *
   * @param string $tfRuntimeVersion
   */
  public function setTfRuntimeVersion($tfRuntimeVersion)
  {
    $this->tfRuntimeVersion = $tfRuntimeVersion;
  }
  /**
   * @return string
   */
  public function getTfRuntimeVersion()
  {
    return $this->tfRuntimeVersion;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSImageModelServingSpec::class, 'Google_Service_CloudNaturalLanguage_XPSImageModelServingSpec');
