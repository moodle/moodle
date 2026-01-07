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

namespace Google\Service\TPU;

class Guaranteed extends \Google\Model
{
  /**
   * Optional. Defines the minimum duration of the guarantee. If specified, the
   * requested resources will only be provisioned if they can be allocated for
   * at least the given duration.
   *
   * @var string
   */
  public $minDuration;

  /**
   * Optional. Defines the minimum duration of the guarantee. If specified, the
   * requested resources will only be provisioned if they can be allocated for
   * at least the given duration.
   *
   * @param string $minDuration
   */
  public function setMinDuration($minDuration)
  {
    $this->minDuration = $minDuration;
  }
  /**
   * @return string
   */
  public function getMinDuration()
  {
    return $this->minDuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Guaranteed::class, 'Google_Service_TPU_Guaranteed');
