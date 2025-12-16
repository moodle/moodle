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

class DeviceIpBlock extends \Google\Model
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
  protected $addedDateType = Date::class;
  protected $addedDateDataType = '';
  /**
   * An IP address block in CIDR notation eg: 34.68.194.64/29
   *
   * @var string
   */
  public $block;
  /**
   * Whether this block is used by physical or virtual devices
   *
   * @var string
   */
  public $form;

  /**
   * The date this block was added to Firebase Test Lab
   *
   * @param Date $addedDate
   */
  public function setAddedDate(Date $addedDate)
  {
    $this->addedDate = $addedDate;
  }
  /**
   * @return Date
   */
  public function getAddedDate()
  {
    return $this->addedDate;
  }
  /**
   * An IP address block in CIDR notation eg: 34.68.194.64/29
   *
   * @param string $block
   */
  public function setBlock($block)
  {
    $this->block = $block;
  }
  /**
   * @return string
   */
  public function getBlock()
  {
    return $this->block;
  }
  /**
   * Whether this block is used by physical or virtual devices
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DeviceIpBlock::class, 'Google_Service_Testing_DeviceIpBlock');
