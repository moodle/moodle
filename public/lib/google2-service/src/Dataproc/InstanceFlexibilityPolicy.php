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

namespace Google\Service\Dataproc;

class InstanceFlexibilityPolicy extends \Google\Collection
{
  protected $collection_key = 'instanceSelectionResults';
  protected $instanceSelectionListType = InstanceSelection::class;
  protected $instanceSelectionListDataType = 'array';
  protected $instanceSelectionResultsType = InstanceSelectionResult::class;
  protected $instanceSelectionResultsDataType = 'array';
  protected $provisioningModelMixType = ProvisioningModelMix::class;
  protected $provisioningModelMixDataType = '';

  /**
   * Optional. List of instance selection options that the group will use when
   * creating new VMs.
   *
   * @param InstanceSelection[] $instanceSelectionList
   */
  public function setInstanceSelectionList($instanceSelectionList)
  {
    $this->instanceSelectionList = $instanceSelectionList;
  }
  /**
   * @return InstanceSelection[]
   */
  public function getInstanceSelectionList()
  {
    return $this->instanceSelectionList;
  }
  /**
   * Output only. A list of instance selection results in the group.
   *
   * @param InstanceSelectionResult[] $instanceSelectionResults
   */
  public function setInstanceSelectionResults($instanceSelectionResults)
  {
    $this->instanceSelectionResults = $instanceSelectionResults;
  }
  /**
   * @return InstanceSelectionResult[]
   */
  public function getInstanceSelectionResults()
  {
    return $this->instanceSelectionResults;
  }
  /**
   * Optional. Defines how the Group selects the provisioning model to ensure
   * required reliability.
   *
   * @param ProvisioningModelMix $provisioningModelMix
   */
  public function setProvisioningModelMix(ProvisioningModelMix $provisioningModelMix)
  {
    $this->provisioningModelMix = $provisioningModelMix;
  }
  /**
   * @return ProvisioningModelMix
   */
  public function getProvisioningModelMix()
  {
    return $this->provisioningModelMix;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstanceFlexibilityPolicy::class, 'Google_Service_Dataproc_InstanceFlexibilityPolicy');
