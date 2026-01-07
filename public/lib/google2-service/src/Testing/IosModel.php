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

namespace Google\Service\Testing;

class IosModel extends \Google\Collection
{
  /**
   * Do not use. For proto versioning only.
   */
  public const FORM_FACTOR_DEVICE_FORM_FACTOR_UNSPECIFIED = 'DEVICE_FORM_FACTOR_UNSPECIFIED';
  /**
   * This device has the shape of a phone.
   */
  public const FORM_FACTOR_PHONE = 'PHONE';
  /**
   * This device has the shape of a tablet.
   */
  public const FORM_FACTOR_TABLET = 'TABLET';
  /**
   * This device has the shape of a watch or other wearable.
   */
  public const FORM_FACTOR_WEARABLE = 'WEARABLE';
  /**
   * This device has a television form factor.
   */
  public const FORM_FACTOR_TV = 'TV';
  /**
   * This device has an automotive form factor.
   */
  public const FORM_FACTOR_AUTOMOTIVE = 'AUTOMOTIVE';
  /**
   * This device has a desktop form factor.
   */
  public const FORM_FACTOR_DESKTOP = 'DESKTOP';
  /**
   * This device has an Extended Reality form factor.
   */
  public const FORM_FACTOR_XR = 'XR';
  protected $collection_key = 'tags';
  /**
   * Device capabilities. Copied from https://developer.apple.com/library/archiv
   * e/documentation/DeviceInformation/Reference/iOSDeviceCompatibility/DeviceCo
   * mpatibilityMatrix/DeviceCompatibilityMatrix.html
   *
   * @var string[]
   */
  public $deviceCapabilities;
  /**
   * Whether this device is a phone, tablet, wearable, etc.
   *
   * @var string
   */
  public $formFactor;
  /**
   * The unique opaque id for this model. Use this for invoking the
   * TestExecutionService.
   *
   * @var string
   */
  public $id;
  /**
   * The human-readable name for this device model. Examples: "iPhone 4s", "iPad
   * Mini 2".
   *
   * @var string
   */
  public $name;
  protected $perVersionInfoType = PerIosVersionInfo::class;
  protected $perVersionInfoDataType = 'array';
  /**
   * Screen density in DPI.
   *
   * @var int
   */
  public $screenDensity;
  /**
   * Screen size in the horizontal (X) dimension measured in pixels.
   *
   * @var int
   */
  public $screenX;
  /**
   * Screen size in the vertical (Y) dimension measured in pixels.
   *
   * @var int
   */
  public $screenY;
  /**
   * The set of iOS major software versions this device supports.
   *
   * @var string[]
   */
  public $supportedVersionIds;
  /**
   * Tags for this dimension. Examples: "default", "preview", "deprecated".
   *
   * @var string[]
   */
  public $tags;

  /**
   * Device capabilities. Copied from https://developer.apple.com/library/archiv
   * e/documentation/DeviceInformation/Reference/iOSDeviceCompatibility/DeviceCo
   * mpatibilityMatrix/DeviceCompatibilityMatrix.html
   *
   * @param string[] $deviceCapabilities
   */
  public function setDeviceCapabilities($deviceCapabilities)
  {
    $this->deviceCapabilities = $deviceCapabilities;
  }
  /**
   * @return string[]
   */
  public function getDeviceCapabilities()
  {
    return $this->deviceCapabilities;
  }
  /**
   * Whether this device is a phone, tablet, wearable, etc.
   *
   * Accepted values: DEVICE_FORM_FACTOR_UNSPECIFIED, PHONE, TABLET, WEARABLE,
   * TV, AUTOMOTIVE, DESKTOP, XR
   *
   * @param self::FORM_FACTOR_* $formFactor
   */
  public function setFormFactor($formFactor)
  {
    $this->formFactor = $formFactor;
  }
  /**
   * @return self::FORM_FACTOR_*
   */
  public function getFormFactor()
  {
    return $this->formFactor;
  }
  /**
   * The unique opaque id for this model. Use this for invoking the
   * TestExecutionService.
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
   * The human-readable name for this device model. Examples: "iPhone 4s", "iPad
   * Mini 2".
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
   * Version-specific information of an iOS model.
   *
   * @param PerIosVersionInfo[] $perVersionInfo
   */
  public function setPerVersionInfo($perVersionInfo)
  {
    $this->perVersionInfo = $perVersionInfo;
  }
  /**
   * @return PerIosVersionInfo[]
   */
  public function getPerVersionInfo()
  {
    return $this->perVersionInfo;
  }
  /**
   * Screen density in DPI.
   *
   * @param int $screenDensity
   */
  public function setScreenDensity($screenDensity)
  {
    $this->screenDensity = $screenDensity;
  }
  /**
   * @return int
   */
  public function getScreenDensity()
  {
    return $this->screenDensity;
  }
  /**
   * Screen size in the horizontal (X) dimension measured in pixels.
   *
   * @param int $screenX
   */
  public function setScreenX($screenX)
  {
    $this->screenX = $screenX;
  }
  /**
   * @return int
   */
  public function getScreenX()
  {
    return $this->screenX;
  }
  /**
   * Screen size in the vertical (Y) dimension measured in pixels.
   *
   * @param int $screenY
   */
  public function setScreenY($screenY)
  {
    $this->screenY = $screenY;
  }
  /**
   * @return int
   */
  public function getScreenY()
  {
    return $this->screenY;
  }
  /**
   * The set of iOS major software versions this device supports.
   *
   * @param string[] $supportedVersionIds
   */
  public function setSupportedVersionIds($supportedVersionIds)
  {
    $this->supportedVersionIds = $supportedVersionIds;
  }
  /**
   * @return string[]
   */
  public function getSupportedVersionIds()
  {
    return $this->supportedVersionIds;
  }
  /**
   * Tags for this dimension. Examples: "default", "preview", "deprecated".
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(IosModel::class, 'Google_Service_Testing_IosModel');
