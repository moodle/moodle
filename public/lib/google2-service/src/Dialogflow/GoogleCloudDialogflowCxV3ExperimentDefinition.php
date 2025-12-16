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

class GoogleCloudDialogflowCxV3ExperimentDefinition extends \Google\Model
{
  /**
   * The condition defines which subset of sessions are selected for this
   * experiment. If not specified, all sessions are eligible. E.g.
   * "query_input.language_code=en" See the [conditions reference](https://cloud
   * .google.com/dialogflow/cx/docs/reference/condition).
   *
   * @var string
   */
  public $condition;
  protected $versionVariantsType = GoogleCloudDialogflowCxV3VersionVariants::class;
  protected $versionVariantsDataType = '';

  /**
   * The condition defines which subset of sessions are selected for this
   * experiment. If not specified, all sessions are eligible. E.g.
   * "query_input.language_code=en" See the [conditions reference](https://cloud
   * .google.com/dialogflow/cx/docs/reference/condition).
   *
   * @param string $condition
   */
  public function setCondition($condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return string
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * The flow versions as the variants of this experiment.
   *
   * @param GoogleCloudDialogflowCxV3VersionVariants $versionVariants
   */
  public function setVersionVariants(GoogleCloudDialogflowCxV3VersionVariants $versionVariants)
  {
    $this->versionVariants = $versionVariants;
  }
  /**
   * @return GoogleCloudDialogflowCxV3VersionVariants
   */
  public function getVersionVariants()
  {
    return $this->versionVariants;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ExperimentDefinition::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ExperimentDefinition');
