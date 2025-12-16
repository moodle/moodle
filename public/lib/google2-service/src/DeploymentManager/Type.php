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

namespace Google\Service\DeploymentManager;

class Type extends \Google\Model
{
  /**
   * @var string
   */
  public $id;
  /**
   * Output only. Creation timestamp in RFC3339 text format.
   *
   * @var string
   */
  public $insertTime;
  /**
   * Name of the type.
   *
   * @var string
   */
  public $name;
  protected $operationType = Operation::class;
  protected $operationDataType = '';
  /**
   * Output only. Server defined URL for the resource.
   *
   * @var string
   */
  public $selfLink;

  /**
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
   * Output only. Creation timestamp in RFC3339 text format.
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * Name of the type.
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
   * Output only. The Operation that most recently ran, or is currently running,
   * on this type.
   *
   * @param Operation $operation
   */
  public function setOperation(Operation $operation)
  {
    $this->operation = $operation;
  }
  /**
   * @return Operation
   */
  public function getOperation()
  {
    return $this->operation;
  }
  /**
   * Output only. Server defined URL for the resource.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Type::class, 'Google_Service_DeploymentManager_Type');
