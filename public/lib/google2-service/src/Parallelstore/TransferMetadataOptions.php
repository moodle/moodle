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

namespace Google\Service\Parallelstore;

class TransferMetadataOptions extends \Google\Model
{
  /**
   * default is GID_NUMBER_PRESERVE.
   */
  public const GID_GID_UNSPECIFIED = 'GID_UNSPECIFIED';
  /**
   * Do not preserve GID during a transfer job.
   */
  public const GID_GID_SKIP = 'GID_SKIP';
  /**
   * Preserve GID that is in number format during a transfer job.
   */
  public const GID_GID_NUMBER_PRESERVE = 'GID_NUMBER_PRESERVE';
  /**
   * default is MODE_PRESERVE.
   */
  public const MODE_MODE_UNSPECIFIED = 'MODE_UNSPECIFIED';
  /**
   * Do not preserve mode during a transfer job.
   */
  public const MODE_MODE_SKIP = 'MODE_SKIP';
  /**
   * Preserve mode during a transfer job.
   */
  public const MODE_MODE_PRESERVE = 'MODE_PRESERVE';
  /**
   * default is UID_NUMBER_PRESERVE.
   */
  public const UID_UID_UNSPECIFIED = 'UID_UNSPECIFIED';
  /**
   * Do not preserve UID during a transfer job.
   */
  public const UID_UID_SKIP = 'UID_SKIP';
  /**
   * Preserve UID that is in number format during a transfer job.
   */
  public const UID_UID_NUMBER_PRESERVE = 'UID_NUMBER_PRESERVE';
  /**
   * Optional. The GID preservation behavior.
   *
   * @var string
   */
  public $gid;
  /**
   * Optional. The mode preservation behavior.
   *
   * @var string
   */
  public $mode;
  /**
   * Optional. The UID preservation behavior.
   *
   * @var string
   */
  public $uid;

  /**
   * Optional. The GID preservation behavior.
   *
   * Accepted values: GID_UNSPECIFIED, GID_SKIP, GID_NUMBER_PRESERVE
   *
   * @param self::GID_* $gid
   */
  public function setGid($gid)
  {
    $this->gid = $gid;
  }
  /**
   * @return self::GID_*
   */
  public function getGid()
  {
    return $this->gid;
  }
  /**
   * Optional. The mode preservation behavior.
   *
   * Accepted values: MODE_UNSPECIFIED, MODE_SKIP, MODE_PRESERVE
   *
   * @param self::MODE_* $mode
   */
  public function setMode($mode)
  {
    $this->mode = $mode;
  }
  /**
   * @return self::MODE_*
   */
  public function getMode()
  {
    return $this->mode;
  }
  /**
   * Optional. The UID preservation behavior.
   *
   * Accepted values: UID_UNSPECIFIED, UID_SKIP, UID_NUMBER_PRESERVE
   *
   * @param self::UID_* $uid
   */
  public function setUid($uid)
  {
    $this->uid = $uid;
  }
  /**
   * @return self::UID_*
   */
  public function getUid()
  {
    return $this->uid;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TransferMetadataOptions::class, 'Google_Service_Parallelstore_TransferMetadataOptions');
