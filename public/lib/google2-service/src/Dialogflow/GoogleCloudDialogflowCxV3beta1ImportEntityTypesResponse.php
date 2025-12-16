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

class GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponse extends \Google\Collection
{
  protected $collection_key = 'entityTypes';
  protected $conflictingResourcesType = GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources::class;
  protected $conflictingResourcesDataType = '';
  /**
   * The unique identifier of the imported entity types. Format:
   * `projects//locations//agents//entity_types/`.
   *
   * @var string[]
   */
  public $entityTypes;

  /**
   * Info which resources have conflicts when REPORT_CONFLICT merge_option is
   * set in ImportEntityTypesRequest.
   *
   * @param GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources $conflictingResources
   */
  public function setConflictingResources(GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources $conflictingResources)
  {
    $this->conflictingResources = $conflictingResources;
  }
  /**
   * @return GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources
   */
  public function getConflictingResources()
  {
    return $this->conflictingResources;
  }
  /**
   * The unique identifier of the imported entity types. Format:
   * `projects//locations//agents//entity_types/`.
   *
   * @param string[] $entityTypes
   */
  public function setEntityTypes($entityTypes)
  {
    $this->entityTypes = $entityTypes;
  }
  /**
   * @return string[]
   */
  public function getEntityTypes()
  {
    return $this->entityTypes;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponse::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponse');
