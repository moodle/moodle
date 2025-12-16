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

namespace Google\Service\StreetViewPublish;

class Level extends \Google\Model
{
  /**
   * Required. A name assigned to this Level, restricted to 3 characters.
   * Consider how the elevator buttons would be labeled for this level if there
   * was an elevator.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Floor number, used for ordering. 0 indicates the ground level, 1
   * indicates the first level above ground level, -1 indicates the first level
   * under ground level. Non-integer values are OK.
   *
   * @var 
   */
  public $number;

  /**
   * Required. A name assigned to this Level, restricted to 3 characters.
   * Consider how the elevator buttons would be labeled for this level if there
   * was an elevator.
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
  public function setNumber($number)
  {
    $this->number = $number;
  }
  public function getNumber()
  {
    return $this->number;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Level::class, 'Google_Service_StreetViewPublish_Level');
