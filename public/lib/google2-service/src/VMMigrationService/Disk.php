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

namespace Google\Service\VMMigrationService;

class Disk extends \Google\Model
{
  /**
   * The disk's Logical Unit Number (LUN).
   *
   * @var int
   */
  public $lun;
  /**
   * The disk name.
   *
   * @var string
   */
  public $name;
  /**
   * The disk size in GB.
   *
   * @var int
   */
  public $sizeGb;

  /**
   * The disk's Logical Unit Number (LUN).
   *
   * @param int $lun
   */
  public function setLun($lun)
  {
    $this->lun = $lun;
  }
  /**
   * @return int
   */
  public function getLun()
  {
    return $this->lun;
  }
  /**
   * The disk name.
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
   * The disk size in GB.
   *
   * @param int $sizeGb
   */
  public function setSizeGb($sizeGb)
  {
    $this->sizeGb = $sizeGb;
  }
  /**
   * @return int
   */
  public function getSizeGb()
  {
    return $this->sizeGb;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Disk::class, 'Google_Service_VMMigrationService_Disk');
