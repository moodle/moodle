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

namespace Google\Service\CloudMachineLearningEngine;

class GoogleCloudMlV1Capability extends \Google\Collection
{
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  public const TYPE_TRAINING = 'TRAINING';
  public const TYPE_BATCH_PREDICTION = 'BATCH_PREDICTION';
  public const TYPE_ONLINE_PREDICTION = 'ONLINE_PREDICTION';
  protected $collection_key = 'availableAccelerators';
  /**
   * Available accelerators for the capability.
   *
   * @var string[]
   */
  public $availableAccelerators;
  /**
   * @var string
   */
  public $type;

  /**
   * Available accelerators for the capability.
   *
   * @param string[] $availableAccelerators
   */
  public function setAvailableAccelerators($availableAccelerators)
  {
    $this->availableAccelerators = $availableAccelerators;
  }
  /**
   * @return string[]
   */
  public function getAvailableAccelerators()
  {
    return $this->availableAccelerators;
  }
  /**
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudMlV1Capability::class, 'Google_Service_CloudMachineLearningEngine_GoogleCloudMlV1Capability');
