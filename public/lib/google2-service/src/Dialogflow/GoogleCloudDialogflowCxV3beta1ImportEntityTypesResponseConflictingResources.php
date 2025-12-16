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

class GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources extends \Google\Collection
{
  protected $collection_key = 'entityTypeDisplayNames';
  /**
   * Display names of conflicting entities.
   *
   * @var string[]
   */
  public $entityDisplayNames;
  /**
   * Display names of conflicting entity types.
   *
   * @var string[]
   */
  public $entityTypeDisplayNames;

  /**
   * Display names of conflicting entities.
   *
   * @param string[] $entityDisplayNames
   */
  public function setEntityDisplayNames($entityDisplayNames)
  {
    $this->entityDisplayNames = $entityDisplayNames;
  }
  /**
   * @return string[]
   */
  public function getEntityDisplayNames()
  {
    return $this->entityDisplayNames;
  }
  /**
   * Display names of conflicting entity types.
   *
   * @param string[] $entityTypeDisplayNames
   */
  public function setEntityTypeDisplayNames($entityTypeDisplayNames)
  {
    $this->entityTypeDisplayNames = $entityTypeDisplayNames;
  }
  /**
   * @return string[]
   */
  public function getEntityTypeDisplayNames()
  {
    return $this->entityTypeDisplayNames;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1ImportEntityTypesResponseConflictingResources');
