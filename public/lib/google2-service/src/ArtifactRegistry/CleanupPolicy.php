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

namespace Google\Service\ArtifactRegistry;

class CleanupPolicy extends \Google\Model
{
  /**
   * Action not specified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Delete action.
   */
  public const ACTION_DELETE = 'DELETE';
  /**
   * Keep action.
   */
  public const ACTION_KEEP = 'KEEP';
  /**
   * Policy action.
   *
   * @var string
   */
  public $action;
  protected $conditionType = CleanupPolicyCondition::class;
  protected $conditionDataType = '';
  /**
   * The user-provided ID of the cleanup policy.
   *
   * @var string
   */
  public $id;
  protected $mostRecentVersionsType = CleanupPolicyMostRecentVersions::class;
  protected $mostRecentVersionsDataType = '';

  /**
   * Policy action.
   *
   * Accepted values: ACTION_UNSPECIFIED, DELETE, KEEP
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Policy condition for matching versions.
   *
   * @param CleanupPolicyCondition $condition
   */
  public function setCondition(CleanupPolicyCondition $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return CleanupPolicyCondition
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * The user-provided ID of the cleanup policy.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Policy condition for retaining a minimum number of versions. May only be
   * specified with a Keep action.
   *
   * @param CleanupPolicyMostRecentVersions $mostRecentVersions
   */
  public function setMostRecentVersions(CleanupPolicyMostRecentVersions $mostRecentVersions)
  {
    $this->mostRecentVersions = $mostRecentVersions;
  }
  /**
   * @return CleanupPolicyMostRecentVersions
   */
  public function getMostRecentVersions()
  {
    return $this->mostRecentVersions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CleanupPolicy::class, 'Google_Service_ArtifactRegistry_CleanupPolicy');
