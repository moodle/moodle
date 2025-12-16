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

namespace Google\Service\Fitness;

class Device extends \Google\Model
{
  /**
   * Device type is not known.
   */
  public const TYPE_unknown = 'unknown';
  /**
   * An Android phone.
   */
  public const TYPE_phone = 'phone';
  /**
   * An Android tablet.
   */
  public const TYPE_tablet = 'tablet';
  /**
   * A watch or other wrist-mounted band.
   */
  public const TYPE_watch = 'watch';
  /**
   * A chest strap.
   */
  public const TYPE_chestStrap = 'chestStrap';
  /**
   * A scale.
   */
  public const TYPE_scale = 'scale';
  /**
   * Glass or other head-mounted device.
   */
  public const TYPE_headMounted = 'headMounted';
  /**
   * A smart display e.g. Nest device.
   */
  public const TYPE_smartDisplay = 'smartDisplay';
  /**
   * Manufacturer of the product/hardware.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * End-user visible model name for the device.
   *
   * @var string
   */
  public $model;
  /**
   * A constant representing the type of the device.
   *
   * @var string
   */
  public $type;
  /**
   * The serial number or other unique ID for the hardware. This field is
   * obfuscated when read by any REST or Android client that did not create the
   * data source. Only the data source creator will see the uid field in clear
   * and normal form. The obfuscation preserves equality; that is, given two
   * IDs, if id1 == id2, obfuscated(id1) == obfuscated(id2).
   *
   * @var string
   */
  public $uid;
  /**
   * Version string for the device hardware/software.
   *
   * @var string
   */
  public $version;

  /**
   * Manufacturer of the product/hardware.
   *
   * @param string $manufacturer
   */
  public function setManufacturer($manufacturer)
  {
    $this->manufacturer = $manufacturer;
  }
  /**
   * @return string
   */
  public function getManufacturer()
  {
    return $this->manufacturer;
  }
  /**
   * End-user visible model name for the device.
   *
   * @param string $model
   */
  public function setModel($model)
  {
    $this->model = $model;
  }
  /**
   * @return string
   */
  public function getModel()
  {
    return $this->model;
  }
  /**
   * A constant representing the type of the device.
   *
   * Accepted values: unknown, phone, tablet, watch, chestStrap, scale,
   * headMounted, smartDisplay
   *
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
  /**
   * The serial number or other unique ID for the hardware. This field is
   * obfuscated when read by any REST or Android client that did not create the
   * data source. Only the data source creator will see the uid field in clear
   * and normal form. The obfuscation preserves equality; that is, given two
   * IDs, if id1 == id2, obfuscated(id1) == obfuscated(id2).
   *
   * @param string $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return string
   */
  public function getUid()
  {
    return $this->uid;
  }
  /**
   * Version string for the device hardware/software.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Device::class, 'Google_Service_Fitness_Device');
