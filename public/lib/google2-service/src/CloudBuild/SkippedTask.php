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

class SkippedTask extends \Google\Collection
{
  protected $collection_key = 'whenExpressions';
  /**
   * Name is the Pipeline Task name
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Reason is the cause of the PipelineTask being skipped.
   *
   * @var string
   */
  public $reason;
  protected $whenExpressionsType = WhenExpression::class;
  protected $whenExpressionsDataType = 'array';

  /**
   * Name is the Pipeline Task name
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
   * Output only. Reason is the cause of the PipelineTask being skipped.
   *
   * @param string $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return string
   */
  public function getReason()
  {
    return $this->reason;
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
class_alias(SkippedTask::class, 'Google_Service_CloudBuild_SkippedTask');
