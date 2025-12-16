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

namespace Google\Service\AndroidManagement;

class OsStartupEvent extends \Google\Model
{
  /**
   * Unknown value.
   */
  public const VERIFIED_BOOT_STATE_VERIFIED_BOOT_STATE_UNSPECIFIED = 'VERIFIED_BOOT_STATE_UNSPECIFIED';
  /**
   * Indicates that there is a full chain of trust extending from the bootloader
   * to verified partitions including the bootloader, boot partition, and all
   * verified partitions.
   */
  public const VERIFIED_BOOT_STATE_GREEN = 'GREEN';
  /**
   * Indicates that the boot partition has been verified using the embedded
   * certificate and the signature is valid.
   */
  public const VERIFIED_BOOT_STATE_YELLOW = 'YELLOW';
  /**
   * Indicates that the device may be freely modified. Device integrity is left
   * to the user to verify out-of-band.
   */
  public const VERIFIED_BOOT_STATE_ORANGE = 'ORANGE';
  /**
   * Unknown value.
   */
  public const VERITY_MODE_DM_VERITY_MODE_UNSPECIFIED = 'DM_VERITY_MODE_UNSPECIFIED';
  /**
   * Indicates that the device will be restarted when corruption is detected.
   */
  public const VERITY_MODE_ENFORCING = 'ENFORCING';
  /**
   * Indicates that an I/O error will be returned for an attempt to read
   * corrupted data blocks (also known as eio boot state).
   */
  public const VERITY_MODE_IO_ERROR = 'IO_ERROR';
  /**
   * Indicates that dm-verity is disabled on device.
   */
  public const VERITY_MODE_DISABLED = 'DISABLED';
  /**
   * Verified Boot state.
   *
   * @var string
   */
  public $verifiedBootState;
  /**
   * dm-verity mode.
   *
   * @var string
   */
  public $verityMode;

  /**
   * Verified Boot state.
   *
   * Accepted values: VERIFIED_BOOT_STATE_UNSPECIFIED, GREEN, YELLOW, ORANGE
   *
   * @param self::VERIFIED_BOOT_STATE_* $verifiedBootState
   */
  public function setVerifiedBootState($verifiedBootState)
  {
    $this->verifiedBootState = $verifiedBootState;
  }
  /**
   * @return self::VERIFIED_BOOT_STATE_*
   */
  public function getVerifiedBootState()
  {
    return $this->verifiedBootState;
  }
  /**
   * dm-verity mode.
   *
   * Accepted values: DM_VERITY_MODE_UNSPECIFIED, ENFORCING, IO_ERROR, DISABLED
   *
   * @param self::VERITY_MODE_* $verityMode
   */
  public function setVerityMode($verityMode)
  {
    $this->verityMode = $verityMode;
  }
  /**
   * @return self::VERITY_MODE_*
   */
  public function getVerityMode()
  {
    return $this->verityMode;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(OsStartupEvent::class, 'Google_Service_AndroidManagement_OsStartupEvent');
