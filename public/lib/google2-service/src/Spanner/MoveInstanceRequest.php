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

namespace Google\Service\Spanner;

class MoveInstanceRequest extends \Google\Collection
{
  protected $collection_key = 'targetDatabaseMoveConfigs';
  /**
   * Required. The target instance configuration where to move the instance.
   * Values are of the form `projects//instanceConfigs/`.
   *
   * @var string
   */
  public $targetConfig;
  protected $targetDatabaseMoveConfigsType = DatabaseMoveConfig::class;
  protected $targetDatabaseMoveConfigsDataType = 'array';

  /**
   * Required. The target instance configuration where to move the instance.
   * Values are of the form `projects//instanceConfigs/`.
   *
   * @param string $targetConfig
   */
  public function setTargetConfig($targetConfig)
  {
    $this->targetConfig = $targetConfig;
  }
  /**
   * @return string
   */
  public function getTargetConfig()
  {
    return $this->targetConfig;
  }
  /**
   * Optional. The configuration for each database in the target instance
   * configuration.
   *
   * @param DatabaseMoveConfig[] $targetDatabaseMoveConfigs
   */
  public function setTargetDatabaseMoveConfigs($targetDatabaseMoveConfigs)
  {
    $this->targetDatabaseMoveConfigs = $targetDatabaseMoveConfigs;
  }
  /**
   * @return DatabaseMoveConfig[]
   */
  public function getTargetDatabaseMoveConfigs()
  {
    return $this->targetDatabaseMoveConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MoveInstanceRequest::class, 'Google_Service_Spanner_MoveInstanceRequest');
