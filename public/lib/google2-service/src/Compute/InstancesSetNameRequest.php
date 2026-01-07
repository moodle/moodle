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

namespace Google\Service\Compute;

class InstancesSetNameRequest extends \Google\Model
{
  /**
   * The current name of this resource, used to prevent conflicts. Provide the
   * latest name when making a request to change name.
   *
   * @var string
   */
  public $currentName;
  /**
   * The name to be applied to the instance. Needs to be RFC 1035 compliant.
   *
   * @var string
   */
  public $name;

  /**
   * The current name of this resource, used to prevent conflicts. Provide the
   * latest name when making a request to change name.
   *
   * @param string $currentName
   */
  public function setCurrentName($currentName)
  {
    $this->currentName = $currentName;
  }
  /**
   * @return string
   */
  public function getCurrentName()
  {
    return $this->currentName;
  }
  /**
   * The name to be applied to the instance. Needs to be RFC 1035 compliant.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InstancesSetNameRequest::class, 'Google_Service_Compute_InstancesSetNameRequest');
