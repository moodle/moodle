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

class AndroidModel extends \Google\Collection
{
  /**
   * Do not use. For proto versioning only.
   */
  public const FORM_DEVICE_FORM_UNSPECIFIED = 'DEVICE_FORM_UNSPECIFIED';
  /**
   * Android virtual device using Compute Engine native virtualization. Firebase
   * Test Lab only.
   */
  public const FORM_VIRTUAL = 'VIRTUAL';
  /**
   * Actual hardware.
   */
  public const FORM_PHYSICAL = 'PHYSICAL';
  /**
   * Android virtual device using emulator in nested virtualization. Equivalent
   * to Android Studio.
   */
  public const FORM_EMULATOR = 'EMULATOR';
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
   * Reasons for access denial. This model is accessible if this list is empty,
   * otherwise the model is viewable only.
   *
   * @var string[]
   */
  public $accessDeniedReasons;
  /**
   * The company that this device is branded with. Example: "Google", "Samsung".
   *
   * @var string
   */
  public $brand;
  /**
   * The name of the industrial design. This corresponds to
   * android.os.Build.DEVICE.
   *
   * @var string
   */
  public $codename;
  /**
   * Whether this device is virtual or physical.
   *
   * @var string
   */
  public $form;
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
  protected $labInfoType = LabInfo::class;
  protected $labInfoDataType = '';
  /**
   * True if and only if tests with this model are recorded by stitching
   * together screenshots. See use_low_spec_video_recording in device config.
   *
   * @var bool
   */
  public $lowFpsVideoRecording;
  /**
   * The manufacturer of this device.
   *
   * @var string
   */
  public $manufacturer;
  /**
   * The human-readable marketing name for this device model. Examples: "Nexus
   * 5", "Galaxy S5".
   *
   * @var string
   */
  public $name;
  protected $perVersionInfoType = PerAndroidVersionInfo::class;
  protected $perVersionInfoDataType = 'array';
  /**
   * Screen density in DPI. This corresponds to ro.sf.lcd_density
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
   * The list of supported ABIs for this device. This corresponds to either
   * android.os.Build.SUPPORTED_ABIS (for API level 21 and above) or
   * android.os.Build.CPU_ABI/CPU_ABI2. The most preferred ABI is the first
   * element in the list. Elements are optionally prefixed by "version_id:"
   * (where version_id is the id of an AndroidVersion), denoting an ABI that is
   * supported only on a particular version.
   *
   * @var string[]
   */
  public $supportedAbis;
  /**
   * The set of Android versions this device supports.
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
   * URL of a thumbnail image (photo) of the device.
   *
   * @var string
   */
  public $thumbnailUrl;

  /**
   * Reasons for access denial. This model is accessible if this list is empty,
   * otherwise the model is viewable only.
   *
   * @param string[] $accessDeniedReasons
   */
  public function setAccessDeniedReasons($accessDeniedReasons)
  {
    $this->accessDeniedReasons = $accessDeniedReasons;
  }
  /**
   * @return string[]
   */
  public function getAccessDeniedReasons()
  {
    return $this->accessDeniedReasons;
  }
  /**
   * The company that this device is branded with. Example: "Google", "Samsung".
   *
   * @param string $brand
   */
  public function setBrand($brand)
  {
    $this->brand = $brand;
  }
  /**
   * @return string
   */
  public function getBrand()
  {
    return $this->brand;
  }
  /**
   * The name of the industrial design. This corresponds to
   * android.os.Build.DEVICE.
   *
   * @param string $codename
   */
  public function setCodename($codename)
  {
    $this->codename = $codename;
  }
  /**
   * @return string
   */
  public function getCodename()
  {
    return $this->codename;
  }
  /**
   * Whether this device is virtual or physical.
   *
   * Accepted values: DEVICE_FORM_UNSPECIFIED, VIRTUAL, PHYSICAL, EMULATOR
   *
   * @param self::FORM_* $form
   */
  public function setForm($form)
  {
    $this->form = $form;
  }
  /**
   * @return self::FORM_*
   */
  public function getForm()
  {
    return $this->form;
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
   * Output only. Lab info of this device.
   *
   * @param LabInfo $labInfo
   */
  public function setLabInfo(LabInfo $labInfo)
  {
    $this->labInfo = $labInfo;
  }
  /**
   * @return LabInfo
   */
  public function getLabInfo()
  {
    return $this->labInfo;
  }
  /**
   * True if and only if tests with this model are recorded by stitching
   * together screenshots. See use_low_spec_video_recording in device config.
   *
   * @param bool $lowFpsVideoRecording
   */
  public function setLowFpsVideoRecording($lowFpsVideoRecording)
  {
    $this->lowFpsVideoRecording = $lowFpsVideoRecording;
  }
  /**
   * @return bool
   */
  public function getLowFpsVideoRecording()
  {
    return $this->lowFpsVideoRecording;
  }
  /**
   * The manufacturer of this device.
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
   * The human-readable marketing name for this device model. Examples: "Nexus
   * 5", "Galaxy S5".
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
   * Version-specific information of an Android model.
   *
   * @param PerAndroidVersionInfo[] $perVersionInfo
   */
  public function setPerVersionInfo($perVersionInfo)
  {
    $this->perVersionInfo = $perVersionInfo;
  }
  /**
   * @return PerAndroidVersionInfo[]
   */
  public function getPerVersionInfo()
  {
    return $this->perVersionInfo;
  }
  /**
   * Screen density in DPI. This corresponds to ro.sf.lcd_density
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
   * The list of supported ABIs for this device. This corresponds to either
   * android.os.Build.SUPPORTED_ABIS (for API level 21 and above) or
   * android.os.Build.CPU_ABI/CPU_ABI2. The most preferred ABI is the first
   * element in the list. Elements are optionally prefixed by "version_id:"
   * (where version_id is the id of an AndroidVersion), denoting an ABI that is
   * supported only on a particular version.
   *
   * @param string[] $supportedAbis
   */
  public function setSupportedAbis($supportedAbis)
  {
    $this->supportedAbis = $supportedAbis;
  }
  /**
   * @return string[]
   */
  public function getSupportedAbis()
  {
    return $this->supportedAbis;
  }
  /**
   * The set of Android versions this device supports.
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
  /**
   * URL of a thumbnail image (photo) of the device.
   *
   * @param string $thumbnailUrl
   */
  public function setThumbnailUrl($thumbnailUrl)
  {
    $this->thumbnailUrl = $thumbnailUrl;
  }
  /**
   * @return string
   */
  public function getThumbnailUrl()
  {
    return $this->thumbnailUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AndroidModel::class, 'Google_Service_Testing_AndroidModel');
