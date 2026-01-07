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

class GoogleDevtoolsArtifactregistryV1Rule extends \Google\Model
{
  /**
   * Action not specified.
   */
  public const ACTION_ACTION_UNSPECIFIED = 'ACTION_UNSPECIFIED';
  /**
   * Allow the operation.
   */
  public const ACTION_ALLOW = 'ALLOW';
  /**
   * Deny the operation.
   */
  public const ACTION_DENY = 'DENY';
  /**
   * Operation not specified.
   */
  public const OPERATION_OPERATION_UNSPECIFIED = 'OPERATION_UNSPECIFIED';
  /**
   * Download operation.
   */
  public const OPERATION_DOWNLOAD = 'DOWNLOAD';
  /**
   * The action this rule takes.
   *
   * @var string
   */
  public $action;
  protected $conditionType = Expr::class;
  protected $conditionDataType = '';
  /**
   * The name of the rule, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/rules/rule1`.
   *
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $operation;
  /**
   * The package ID the rule applies to. If empty, this rule applies to all
   * packages inside the repository.
   *
   * @var string
   */
  public $packageId;

  /**
   * The action this rule takes.
   *
   * Accepted values: ACTION_UNSPECIFIED, ALLOW, DENY
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
   * Optional. A CEL expression for conditions that must be met in order for the
   * rule to apply. If not provided, the rule matches all objects.
   *
   * @param Expr $condition
   */
  public function setCondition(Expr $condition)
  {
    $this->condition = $condition;
  }
  /**
   * @return Expr
   */
  public function getCondition()
  {
    return $this->condition;
  }
  /**
   * The name of the rule, for example: `projects/p1/locations/us-
   * central1/repositories/repo1/rules/rule1`.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * @param self::OPERATION_* $operation
   */
  public function setOperation($operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return self::OPERATION_*
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * The package ID the rule applies to. If empty, this rule applies to all
   * packages inside the repository.
   *
   * @param string $packageId
   */
  public function setPackageId($packageId)
  {
    $this->packageId = $packageId;
  }
  /**
   * @return string
   */
  public function getPackageId()
  {
    return $this->packageId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleDevtoolsArtifactregistryV1Rule::class, 'Google_Service_ArtifactRegistry_GoogleDevtoolsArtifactregistryV1Rule');
