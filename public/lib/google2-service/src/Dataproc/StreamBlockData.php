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

namespace Google\Service\Dataproc;

class StreamBlockData extends \Google\Model
{
  /**
   * @var bool
   */
  public $deserialized;
  /**
   * @var string
   */
  public $diskSize;
  /**
   * @var string
   */
  public $executorId;
  /**
   * @var string
   */
  public $hostPort;
  /**
   * @var string
   */
  public $memSize;
  /**
   * @var string
   */
  public $name;
  /**
   * @var string
   */
  public $storageLevel;
  /**
   * @var bool
   */
  public $useDisk;
  /**
   * @var bool
   */
  public $useMemory;

  /**
   * @param bool $deserialized
   */
  public function setDeserialized($deserialized)
  {
    $this->deserialized = $deserialized;
  }
  /**
   * @return bool
   */
  public function getDeserialized()
  {
    return $this->deserialized;
  }
  /**
   * @param string $diskSize
   */
  public function setDiskSize($diskSize)
  {
    $this->diskSize = $diskSize;
  }
  /**
   * @return string
   */
  public function getDiskSize()
  {
    return $this->diskSize;
  }
  /**
   * @param string $executorId
   */
  public function setExecutorId($executorId)
  {
    $this->executorId = $executorId;
  }
  /**
   * @return string
   */
  public function getExecutorId()
  {
    return $this->executorId;
  }
  /**
   * @param string $hostPort
   */
  public function setHostPort($hostPort)
  {
    $this->hostPort = $hostPort;
  }
  /**
   * @return string
   */
  public function getHostPort()
  {
    return $this->hostPort;
  }
  /**
   * @param string $memSize
   */
  public function setMemSize($memSize)
  {
    $this->memSize = $memSize;
  }
  /**
   * @return string
   */
  public function getMemSize()
  {
    return $this->memSize;
  }
  /**
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
   * @param string $storageLevel
   */
  public function setStorageLevel($storageLevel)
  {
    $this->storageLevel = $storageLevel;
  }
  /**
   * @return string
   */
  public function getStorageLevel()
  {
    return $this->storageLevel;
  }
  /**
   * @param bool $useDisk
   */
  public function setUseDisk($useDisk)
  {
    $this->useDisk = $useDisk;
  }
  /**
   * @return bool
   */
  public function getUseDisk()
  {
    return $this->useDisk;
  }
  /**
   * @param bool $useMemory
   */
  public function setUseMemory($useMemory)
  {
    $this->useMemory = $useMemory;
  }
  /**
   * @return bool
   */
  public function getUseMemory()
  {
    return $this->useMemory;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StreamBlockData::class, 'Google_Service_Dataproc_StreamBlockData');
