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

namespace Google\Service\CloudRun;

class GoogleCloudRunV2NodeSelector extends \Google\Model
{
  /**
   * Required. GPU accelerator type to attach to an instance.
   *
   * @var string
   */
  public $accelerator;

  /**
   * Required. GPU accelerator type to attach to an instance.
   *
   * @param string $accelerator
   */
  public function setAccelerator($accelerator)
  {
    $this->accelerator = $accelerator;
  }
  /**
   * @return string
   */
  public function getAccelerator()
  {
    return $this->accelerator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRunV2NodeSelector::class, 'Google_Service_CloudRun_GoogleCloudRunV2NodeSelector');
