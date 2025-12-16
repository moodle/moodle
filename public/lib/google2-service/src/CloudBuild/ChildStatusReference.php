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

namespace Google\Service\CloudBuild;

class ChildStatusReference extends \Google\Collection
{
  /**
   * Default enum type; should not be used.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * TaskRun.
   */
  public const TYPE_TASK_RUN = 'TASK_RUN';
  protected $collection_key = 'whenExpressions';
  /**
   * Name is the name of the TaskRun or Run this is referencing.
   *
   * @var string
   */
  public $name;
  /**
   * PipelineTaskName is the name of the PipelineTask this is referencing.
   *
   * @var string
   */
  public $pipelineTaskName;
  /**
   * Output only. Type of the child reference.
   *
   * @var string
   */
  public $type;
  protected $whenExpressionsType = WhenExpression::class;
  protected $whenExpressionsDataType = 'array';

  /**
   * Name is the name of the TaskRun or Run this is referencing.
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
   * PipelineTaskName is the name of the PipelineTask this is referencing.
   *
   * @param string $pipelineTaskName
   */
  public function setPipelineTaskName($pipelineTaskName)
  {
    $this->pipelineTaskName = $pipelineTaskName;
  }
  /**
   * @return string
   */
  public function getPipelineTaskName()
  {
    return $this->pipelineTaskName;
  }
  /**
   * Output only. Type of the child reference.
   *
   * Accepted values: TYPE_UNSPECIFIED, TASK_RUN
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * WhenExpressions is the list of checks guarding the execution of the
   * PipelineTask
   *
   * @param WhenExpression[] $whenExpressions
   */
  public function setWhenExpressions($whenExpressions)
  {
    $this->whenExpressions = $whenExpressions;
  }
  /**
   * @return WhenExpression[]
   */
  public function getWhenExpressions()
  {
    return $this->whenExpressions;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ChildStatusReference::class, 'Google_Service_CloudBuild_ChildStatusReference');
