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

namespace Google\Service\SASPortalTesting;

class SasPortalDeviceGrant extends \Google\Collection
{
  public const CHANNEL_TYPE_CHANNEL_TYPE_UNSPECIFIED = 'CHANNEL_TYPE_UNSPECIFIED';
  public const CHANNEL_TYPE_CHANNEL_TYPE_GAA = 'CHANNEL_TYPE_GAA';
  public const CHANNEL_TYPE_CHANNEL_TYPE_PAL = 'CHANNEL_TYPE_PAL';
  public const STATE_GRANT_STATE_UNSPECIFIED = 'GRANT_STATE_UNSPECIFIED';
  /**
   * The grant has been granted but the device is not heartbeating on it.
   */
  public const STATE_GRANT_STATE_GRANTED = 'GRANT_STATE_GRANTED';
  /**
   * The grant has been terminated by the SAS.
   */
  public const STATE_GRANT_STATE_TERMINATED = 'GRANT_STATE_TERMINATED';
  /**
   * The grant has been suspended by the SAS.
   */
  public const STATE_GRANT_STATE_SUSPENDED = 'GRANT_STATE_SUSPENDED';
  /**
   * The device is currently transmitting.
   */
  public const STATE_GRANT_STATE_AUTHORIZED = 'GRANT_STATE_AUTHORIZED';
  /**
   * The grant has expired.
   */
  public const STATE_GRANT_STATE_EXPIRED = 'GRANT_STATE_EXPIRED';
  protected $collection_key = 'suspensionReason';
  /**
   * Type of channel used.
   *
   * @var string
   */
  public $channelType;
  /**
   * The expiration time of the grant.
   *
   * @var string
   */
  public $expireTime;
  protected $frequencyRangeType = SasPortalFrequencyRange::class;
  protected $frequencyRangeDataType = '';
  /**
   * Grant Id.
   *
   * @var string
   */
  public $grantId;
  /**
   * The transmit expiration time of the last heartbeat.
   *
   * @var string
   */
  public $lastHeartbeatTransmitExpireTime;
  /**
   * Maximum Equivalent Isotropically Radiated Power (EIRP) permitted by the
   * grant. The maximum EIRP is in units of dBm/MHz. The value of `maxEirp`
   * represents the average (RMS) EIRP that would be measured by the procedure
   * defined in FCC part 96.41(e)(3).
   *
   * @var 
   */
  public $maxEirp;
  protected $moveListType = SasPortalDpaMoveList::class;
  protected $moveListDataType = 'array';
  /**
   * State of the grant.
   *
   * @var string
   */
  public $state;
  /**
   * If the grant is suspended, the reason(s) for suspension.
   *
   * @var string[]
   */
  public $suspensionReason;

  /**
   * Type of channel used.
   *
   * Accepted values: CHANNEL_TYPE_UNSPECIFIED, CHANNEL_TYPE_GAA,
   * CHANNEL_TYPE_PAL
   *
   * @param self::CHANNEL_TYPE_* $channelType
   */
  public function setChannelType($channelType)
  {
    $this->channelType = $channelType;
  }
  /**
   * @return self::CHANNEL_TYPE_*
   */
  public function getChannelType()
  {
    return $this->channelType;
  }
  /**
   * The expiration time of the grant.
   *
   * @param string $expireTime
   */
  public function setExpireTime($expireTime)
  {
    $this->expireTime = $expireTime;
  }
  /**
   * @return string
   */
  public function getExpireTime()
  {
    return $this->expireTime;
  }
  /**
   * The transmission frequency range.
   *
   * @param SasPortalFrequencyRange $frequencyRange
   */
  public function setFrequencyRange(SasPortalFrequencyRange $frequencyRange)
  {
    $this->frequencyRange = $frequencyRange;
  }
  /**
   * @return SasPortalFrequencyRange
   */
  public function getFrequencyRange()
  {
    return $this->frequencyRange;
  }
  /**
   * Grant Id.
   *
   * @param string $grantId
   */
  public function setGrantId($grantId)
  {
    $this->grantId = $grantId;
  }
  /**
   * @return string
   */
  public function getGrantId()
  {
    return $this->grantId;
  }
  /**
   * The transmit expiration time of the last heartbeat.
   *
   * @param string $lastHeartbeatTransmitExpireTime
   */
  public function setLastHeartbeatTransmitExpireTime($lastHeartbeatTransmitExpireTime)
  {
    $this->lastHeartbeatTransmitExpireTime = $lastHeartbeatTransmitExpireTime;
  }
  /**
   * @return string
   */
  public function getLastHeartbeatTransmitExpireTime()
  {
    return $this->lastHeartbeatTransmitExpireTime;
  }
  public function setMaxEirp($maxEirp)
  {
    $this->maxEirp = $maxEirp;
  }
  public function getMaxEirp()
  {
    return $this->maxEirp;
  }
  /**
   * The DPA move lists on which this grant appears.
   *
   * @param SasPortalDpaMoveList[] $moveList
   */
  public function setMoveList($moveList)
  {
    $this->moveList = $moveList;
  }
  /**
   * @return SasPortalDpaMoveList[]
   */
  public function getMoveList()
  {
    return $this->moveList;
  }
  /**
   * State of the grant.
   *
   * Accepted values: GRANT_STATE_UNSPECIFIED, GRANT_STATE_GRANTED,
   * GRANT_STATE_TERMINATED, GRANT_STATE_SUSPENDED, GRANT_STATE_AUTHORIZED,
   * GRANT_STATE_EXPIRED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * If the grant is suspended, the reason(s) for suspension.
   *
   * @param string[] $suspensionReason
   */
  public function setSuspensionReason($suspensionReason)
  {
    $this->suspensionReason = $suspensionReason;
  }
  /**
   * @return string[]
   */
  public function getSuspensionReason()
  {
    return $this->suspensionReason;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(SasPortalDeviceGrant::class, 'Google_Service_SASPortalTesting_SasPortalDeviceGrant');
