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

namespace Google\Service\Dataflow;

class DataSamplingConfig extends \Google\Collection
{
  protected $collection_key = 'behaviors';
  /**
   * List of given sampling behaviors to enable. For example, specifying
   * behaviors = [ALWAYS_ON] samples in-flight elements but does not sample
   * exceptions. Can be used to specify multiple behaviors like, behaviors =
   * [ALWAYS_ON, EXCEPTIONS] for specifying periodic sampling and exception
   * sampling. If DISABLED is in the list, then sampling will be disabled and
   * ignore the other given behaviors. Ordering does not matter.
   *
   * @var string[]
   */
  public $behaviors;

  /**
   * List of given sampling behaviors to enable. For example, specifying
   * behaviors = [ALWAYS_ON] samples in-flight elements but does not sample
   * exceptions. Can be used to specify multiple behaviors like, behaviors =
   * [ALWAYS_ON, EXCEPTIONS] for specifying periodic sampling and exception
   * sampling. If DISABLED is in the list, then sampling will be disabled and
   * ignore the other given behaviors. Ordering does not matter.
   *
   * @param string[] $behaviors
   */
  public function setBehaviors($behaviors)
  {
    $this->behaviors = $behaviors;
  }
  /**
   * @return string[]
   */
  public function getBehaviors()
  {
    return $this->behaviors;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataSamplingConfig::class, 'Google_Service_Dataflow_DataSamplingConfig');
