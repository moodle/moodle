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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1UsbPeripheralReport extends \Google\Collection
{
  protected $collection_key = 'categories';
  /**
   * Output only. Categories the device belongs to https://www.usb.org/defined-
   * class-codes
   *
   * @var string[]
   */
  public $categories;
  /**
   * Output only. Class ID https://www.usb.org/defined-class-codes
   *
   * @var int
   */
  public $classId;
  /**
   * Output only. Firmware version
   *
   * @var string
   */
  public $firmwareVersion;
  /**
   * Output only. Device name, model name, or product name
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Product ID
   *
   * @var int
   */
  public $pid;
  /**
   * Output only. Subclass ID https://www.usb.org/defined-class-codes
   *
   * @var int
   */
  public $subclassId;
  /**
   * Output only. Vendor name
   *
   * @var string
   */
  public $vendor;
  /**
   * Output only. Vendor ID
   *
   * @var int
   */
  public $vid;

  /**
   * Output only. Categories the device belongs to https://www.usb.org/defined-
   * class-codes
   *
   * @param string[] $categories
   */
  public function setCategories($categories)
  {
    $this->categories = $categories;
  }
  /**
   * @return string[]
   */
  public function getCategories()
  {
    return $this->categories;
  }
  /**
   * Output only. Class ID https://www.usb.org/defined-class-codes
   *
   * @param int $classId
   */
  public function setClassId($classId)
  {
    $this->classId = $classId;
  }
  /**
   * @return int
   */
  public function getClassId()
  {
    return $this->classId;
  }
  /**
   * Output only. Firmware version
   *
   * @param string $firmwareVersion
   */
  public function setFirmwareVersion($firmwareVersion)
  {
    $this->firmwareVersion = $firmwareVersion;
  }
  /**
   * @return string
   */
  public function getFirmwareVersion()
  {
    return $this->firmwareVersion;
  }
  /**
   * Output only. Device name, model name, or product name
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
   * Output only. Product ID
   *
   * @param int $pid
   */
  public function setPid($pid)
  {
    $this->pid = $pid;
  }
  /**
   * @return int
   */
  public function getPid()
  {
    return $this->pid;
  }
  /**
   * Output only. Subclass ID https://www.usb.org/defined-class-codes
   *
   * @param int $subclassId
   */
  public function setSubclassId($subclassId)
  {
    $this->subclassId = $subclassId;
  }
  /**
   * @return int
   */
  public function getSubclassId()
  {
    return $this->subclassId;
  }
  /**
   * Output only. Vendor name
   *
   * @param string $vendor
   */
  public function setVendor($vendor)
  {
    $this->vendor = $vendor;
  }
  /**
   * @return string
   */
  public function getVendor()
  {
    return $this->vendor;
  }
  /**
   * Output only. Vendor ID
   *
   * @param int $vid
   */
  public function setVid($vid)
  {
    $this->vid = $vid;
  }
  /**
   * @return int
   */
  public function getVid()
  {
    return $this->vid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1UsbPeripheralReport::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1UsbPeripheralReport');
