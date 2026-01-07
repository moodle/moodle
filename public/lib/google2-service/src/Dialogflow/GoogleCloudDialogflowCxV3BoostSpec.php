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

class GoogleCloudDialogflowCxV3BoostSpec extends \Google\Collection
{
  protected $collection_key = 'conditionBoostSpecs';
  protected $conditionBoostSpecsType = GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpec::class;
  protected $conditionBoostSpecsDataType = 'array';

  /**
   * Optional. Condition boost specifications. If a document matches multiple
   * conditions in the specifications, boost scores from these specifications
   * are all applied and combined in a non-linear way. Maximum number of
   * specifications is 20.
   *
   * @param GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpec[] $conditionBoostSpecs
   */
  public function setConditionBoostSpecs($conditionBoostSpecs)
  {
    $this->conditionBoostSpecs = $conditionBoostSpecs;
  }
  /**
   * @return GoogleCloudDialogflowCxV3BoostSpecConditionBoostSpec[]
   */
  public function getConditionBoostSpecs()
  {
    return $this->conditionBoostSpecs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3BoostSpec::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3BoostSpec');
