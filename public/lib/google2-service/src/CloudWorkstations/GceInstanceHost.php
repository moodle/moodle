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

namespace Google\Service\CloudWorkstations;

class GceInstanceHost extends \Google\Model
{
  /**
   * Optional. Output only. The ID of the Compute Engine instance.
   *
   * @var string
   */
  public $id;
  /**
   * Optional. Output only. The name of the Compute Engine instance.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Output only. The zone of the Compute Engine instance.
   *
   * @var string
   */
  public $zone;

  /**
   * Optional. Output only. The ID of the Compute Engine instance.
   *
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
   * Optional. Output only. The name of the Compute Engine instance.
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
   * Optional. Output only. The zone of the Compute Engine instance.
   *
   * @param string $zone
   */
  public function setZone($zone)
  {
    $this->zone = $zone;
  }
  /**
   * @return string
   */
  public function getZone()
  {
    return $this->zone;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GceInstanceHost::class, 'Google_Service_CloudWorkstations_GceInstanceHost');
