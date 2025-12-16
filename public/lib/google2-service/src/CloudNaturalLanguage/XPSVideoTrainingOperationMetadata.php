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

class XPSVideoTrainingOperationMetadata extends \Google\Model
{
  /**
   * This is an estimation of the node hours necessary for training a model,
   * expressed in milli node hours (i.e. 1,000 value in this field means 1 node
   * hour). A node hour represents the time a virtual machine spends running
   * your training job. The cost of one node running for one hour is a node
   * hour.
   *
   * @var string
   */
  public $trainCostMilliNodeHour;

  /**
   * This is an estimation of the node hours necessary for training a model,
   * expressed in milli node hours (i.e. 1,000 value in this field means 1 node
   * hour). A node hour represents the time a virtual machine spends running
   * your training job. The cost of one node running for one hour is a node
   * hour.
   *
   * @param string $trainCostMilliNodeHour
   */
  public function setTrainCostMilliNodeHour($trainCostMilliNodeHour)
  {
    $this->trainCostMilliNodeHour = $trainCostMilliNodeHour;
  }
  /**
   * @return string
   */
  public function getTrainCostMilliNodeHour()
  {
    return $this->trainCostMilliNodeHour;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(XPSVideoTrainingOperationMetadata::class, 'Google_Service_CloudNaturalLanguage_XPSVideoTrainingOperationMetadata');
