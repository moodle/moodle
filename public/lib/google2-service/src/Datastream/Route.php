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

namespace Google\Service\Datastream;

class Route extends \Google\Model
{
  /**
   * Output only. The create time of the resource.
   *
   * @var string
   */
  public $createTime;
  /**
   * Required. Destination address for connection
   *
   * @var string
   */
  public $destinationAddress;
  /**
   * Destination port for connection
   *
   * @var int
   */
  public $destinationPort;
  /**
   * Required. Display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Labels.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. Identifier. The resource's name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The update time of the resource.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The create time of the resource.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Required. Destination address for connection
   *
   * @param string $destinationAddress
   */
  public function setDestinationAddress($destinationAddress)
  {
    $this->destinationAddress = $destinationAddress;
  }
  /**
   * @return string
   */
  public function getDestinationAddress()
  {
    return $this->destinationAddress;
  }
  /**
   * Destination port for connection
   *
   * @param int $destinationPort
   */
  public function setDestinationPort($destinationPort)
  {
    $this->destinationPort = $destinationPort;
  }
  /**
   * @return int
   */
  public function getDestinationPort()
  {
    return $this->destinationPort;
  }
  /**
   * Required. Display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Labels.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. Identifier. The resource's name.
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
   * Output only. The update time of the resource.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Route::class, 'Google_Service_Datastream_Route');
