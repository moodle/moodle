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

namespace Google\Service\GKEOnPrem;

class ResourceStatus extends \Google\Collection
{
  protected $collection_key = 'conditions';
  protected $conditionsType = ResourceCondition::class;
  protected $conditionsDataType = 'array';
  /**
   * Human-friendly representation of the error message from controller. The
   * error message can be temporary as the controller controller creates a
   * cluster or node pool. If the error message persists for a longer period of
   * time, it can be used to surface error message to indicate real problems
   * requiring user intervention.
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Reflect current version of the resource.
   *
   * @var string
   */
  public $version;
  protected $versionsType = Versions::class;
  protected $versionsDataType = '';

  /**
   * ResourceCondition provide a standard mechanism for higher-level status
   * reporting from controller.
   *
   * @param ResourceCondition[] $conditions
   */
  public function setConditions($conditions)
  {
    $this->conditions = $conditions;
  }
  /**
   * @return ResourceCondition[]
   */
  public function getConditions()
  {
    return $this->conditions;
  }
  /**
   * Human-friendly representation of the error message from controller. The
   * error message can be temporary as the controller controller creates a
   * cluster or node pool. If the error message persists for a longer period of
   * time, it can be used to surface error message to indicate real problems
   * requiring user intervention.
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Reflect current version of the resource.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
  /**
   * Shows the mapping of a given version to the number of machines under this
   * version.
   *
   * @param Versions $versions
   */
  public function setVersions(Versions $versions)
  {
    $this->versions = $versions;
  }
  /**
   * @return Versions
   */
  public function getVersions()
  {
    return $this->versions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ResourceStatus::class, 'Google_Service_GKEOnPrem_ResourceStatus');
