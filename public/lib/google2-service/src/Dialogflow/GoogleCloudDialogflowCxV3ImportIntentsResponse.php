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

class GoogleCloudDialogflowCxV3ImportIntentsResponse extends \Google\Collection
{
  protected $collection_key = 'intents';
  protected $conflictingResourcesType = GoogleCloudDialogflowCxV3ImportIntentsResponseConflictingResources::class;
  protected $conflictingResourcesDataType = '';
  /**
   * The unique identifier of the imported intents. Format:
   * `projects//locations//agents//intents/`.
   *
   * @var string[]
   */
  public $intents;

  /**
   * Info which resources have conflicts when REPORT_CONFLICT merge_option is
   * set in ImportIntentsRequest.
   *
   * @param GoogleCloudDialogflowCxV3ImportIntentsResponseConflictingResources $conflictingResources
   */
  public function setConflictingResources(GoogleCloudDialogflowCxV3ImportIntentsResponseConflictingResources $conflictingResources)
  {
    $this->conflictingResources = $conflictingResources;
  }
  /**
   * @return GoogleCloudDialogflowCxV3ImportIntentsResponseConflictingResources
   */
  public function getConflictingResources()
  {
    return $this->conflictingResources;
  }
  /**
   * The unique identifier of the imported intents. Format:
   * `projects//locations//agents//intents/`.
   *
   * @param string[] $intents
   */
  public function setIntents($intents)
  {
    $this->intents = $intents;
  }
  /**
   * @return string[]
   */
  public function getIntents()
  {
    return $this->intents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3ImportIntentsResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3ImportIntentsResponse');
