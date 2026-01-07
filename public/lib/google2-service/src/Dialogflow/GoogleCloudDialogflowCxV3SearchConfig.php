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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3SearchConfig extends \Google\Collection
{
  protected $collection_key = 'filterSpecs';
  protected $boostSpecsType = GoogleCloudDialogflowCxV3BoostSpecs::class;
  protected $boostSpecsDataType = 'array';
  protected $filterSpecsType = GoogleCloudDialogflowCxV3FilterSpecs::class;
  protected $filterSpecsDataType = 'array';

  /**
   * Optional. Boosting configuration for the datastores. Maps from datastore
   * name to their boost configuration. Do not specify more than one BoostSpecs
   * for each datastore name. If multiple BoostSpecs are provided for the same
   * datastore name, the behavior is undefined.
   *
   * @param GoogleCloudDialogflowCxV3BoostSpecs[] $boostSpecs
   */
  public function setBoostSpecs($boostSpecs)
  {
    $this->boostSpecs = $boostSpecs;
  }
  /**
   * @return GoogleCloudDialogflowCxV3BoostSpecs[]
   */
  public function getBoostSpecs()
  {
    return $this->boostSpecs;
  }
  /**
   * Optional. Filter configuration for the datastores. Maps from datastore name
   * to the filter expression for that datastore. Do not specify more than one
   * FilterSpecs for each datastore name. If multiple FilterSpecs are provided
   * for the same datastore name, the behavior is undefined.
   *
   * @param GoogleCloudDialogflowCxV3FilterSpecs[] $filterSpecs
   */
  public function setFilterSpecs($filterSpecs)
  {
    $this->filterSpecs = $filterSpecs;
  }
  /**
   * @return GoogleCloudDialogflowCxV3FilterSpecs[]
   */
  public function getFilterSpecs()
  {
    return $this->filterSpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3SearchConfig::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3SearchConfig');
