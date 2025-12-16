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

namespace Google\Service\Compute;

class BfdStatus extends \Google\Collection
{
  public const BFD_SESSION_INITIALIZATION_MODE_ACTIVE = 'ACTIVE';
  public const BFD_SESSION_INITIALIZATION_MODE_DISABLED = 'DISABLED';
  public const BFD_SESSION_INITIALIZATION_MODE_PASSIVE = 'PASSIVE';
  public const LOCAL_DIAGNOSTIC_ADMINISTRATIVELY_DOWN = 'ADMINISTRATIVELY_DOWN';
  public const LOCAL_DIAGNOSTIC_CONCATENATED_PATH_DOWN = 'CONCATENATED_PATH_DOWN';
  public const LOCAL_DIAGNOSTIC_CONTROL_DETECTION_TIME_EXPIRED = 'CONTROL_DETECTION_TIME_EXPIRED';
  public const LOCAL_DIAGNOSTIC_DIAGNOSTIC_UNSPECIFIED = 'DIAGNOSTIC_UNSPECIFIED';
  public const LOCAL_DIAGNOSTIC_ECHO_FUNCTION_FAILED = 'ECHO_FUNCTION_FAILED';
  public const LOCAL_DIAGNOSTIC_FORWARDING_PLANE_RESET = 'FORWARDING_PLANE_RESET';
  public const LOCAL_DIAGNOSTIC_NEIGHBOR_SIGNALED_SESSION_DOWN = 'NEIGHBOR_SIGNALED_SESSION_DOWN';
  public const LOCAL_DIAGNOSTIC_NO_DIAGNOSTIC = 'NO_DIAGNOSTIC';
  public const LOCAL_DIAGNOSTIC_PATH_DOWN = 'PATH_DOWN';
  public const LOCAL_DIAGNOSTIC_REVERSE_CONCATENATED_PATH_DOWN = 'REVERSE_CONCATENATED_PATH_DOWN';
  public const LOCAL_STATE_ADMIN_DOWN = 'ADMIN_DOWN';
  public const LOCAL_STATE_DOWN = 'DOWN';
  public const LOCAL_STATE_INIT = 'INIT';
  public const LOCAL_STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  public const LOCAL_STATE_UP = 'UP';
  protected $collection_key = 'controlPacketIntervals';
  /**
   * The BFD session initialization mode for this BGP peer. If set to ACTIVE,
   * the Cloud Router will initiate the BFD session for this BGP peer. If set to
   * PASSIVE, the Cloud Router will wait for the peer router to initiate the BFD
   * session for this BGP peer. If set to DISABLED, BFD is disabled for this BGP
   * peer.
   *
   * @var string
   */
  public $bfdSessionInitializationMode;
  /**
   * Unix timestamp of the most recent config update.
   *
   * @var string
   */
  public $configUpdateTimestampMicros;
  protected $controlPacketCountsType = BfdStatusPacketCounts::class;
  protected $controlPacketCountsDataType = '';
  protected $controlPacketIntervalsType = PacketIntervals::class;
  protected $controlPacketIntervalsDataType = 'array';
  /**
   * The diagnostic code specifies the local system's reason for the last change
   * in session state. This allows remote systems to determine the reason that
   * the previous session failed, for example. These diagnostic codes are
   * specified in section 4.1 ofRFC5880
   *
   * @var string
   */
  public $localDiagnostic;
  /**
   * The current BFD session state as seen by the transmitting system. These
   * states are specified in section 4.1 ofRFC5880
   *
   * @var string
   */
  public $localState;
  /**
   * Negotiated transmit interval for control packets.
   *
   * @var string
   */
  public $negotiatedLocalControlTxIntervalMs;
  protected $rxPacketType = BfdPacket::class;
  protected $rxPacketDataType = '';
  protected $txPacketType = BfdPacket::class;
  protected $txPacketDataType = '';
  /**
   * Session uptime in milliseconds. Value will be 0 if session is not up.
   *
   * @var string
   */
  public $uptimeMs;

  /**
   * The BFD session initialization mode for this BGP peer. If set to ACTIVE,
   * the Cloud Router will initiate the BFD session for this BGP peer. If set to
   * PASSIVE, the Cloud Router will wait for the peer router to initiate the BFD
   * session for this BGP peer. If set to DISABLED, BFD is disabled for this BGP
   * peer.
   *
   * Accepted values: ACTIVE, DISABLED, PASSIVE
   *
   * @param self::BFD_SESSION_INITIALIZATION_MODE_* $bfdSessionInitializationMode
   */
  public function setBfdSessionInitializationMode($bfdSessionInitializationMode)
  {
    $this->bfdSessionInitializationMode = $bfdSessionInitializationMode;
  }
  /**
   * @return self::BFD_SESSION_INITIALIZATION_MODE_*
   */
  public function getBfdSessionInitializationMode()
  {
    return $this->bfdSessionInitializationMode;
  }
  /**
   * Unix timestamp of the most recent config update.
   *
   * @param string $configUpdateTimestampMicros
   */
  public function setConfigUpdateTimestampMicros($configUpdateTimestampMicros)
  {
    $this->configUpdateTimestampMicros = $configUpdateTimestampMicros;
  }
  /**
   * @return string
   */
  public function getConfigUpdateTimestampMicros()
  {
    return $this->configUpdateTimestampMicros;
  }
  /**
   * Control packet counts for the current BFD session.
   *
   * @param BfdStatusPacketCounts $controlPacketCounts
   */
  public function setControlPacketCounts(BfdStatusPacketCounts $controlPacketCounts)
  {
    $this->controlPacketCounts = $controlPacketCounts;
  }
  /**
   * @return BfdStatusPacketCounts
   */
  public function getControlPacketCounts()
  {
    return $this->controlPacketCounts;
  }
  /**
   * Inter-packet time interval statistics for control packets.
   *
   * @param PacketIntervals[] $controlPacketIntervals
   */
  public function setControlPacketIntervals($controlPacketIntervals)
  {
    $this->controlPacketIntervals = $controlPacketIntervals;
  }
  /**
   * @return PacketIntervals[]
   */
  public function getControlPacketIntervals()
  {
    return $this->controlPacketIntervals;
  }
  /**
   * The diagnostic code specifies the local system's reason for the last change
   * in session state. This allows remote systems to determine the reason that
   * the previous session failed, for example. These diagnostic codes are
   * specified in section 4.1 ofRFC5880
   *
   * Accepted values: ADMINISTRATIVELY_DOWN, CONCATENATED_PATH_DOWN,
   * CONTROL_DETECTION_TIME_EXPIRED, DIAGNOSTIC_UNSPECIFIED,
   * ECHO_FUNCTION_FAILED, FORWARDING_PLANE_RESET,
   * NEIGHBOR_SIGNALED_SESSION_DOWN, NO_DIAGNOSTIC, PATH_DOWN,
   * REVERSE_CONCATENATED_PATH_DOWN
   *
   * @param self::LOCAL_DIAGNOSTIC_* $localDiagnostic
   */
  public function setLocalDiagnostic($localDiagnostic)
  {
    $this->localDiagnostic = $localDiagnostic;
  }
  /**
   * @return self::LOCAL_DIAGNOSTIC_*
   */
  public function getLocalDiagnostic()
  {
    return $this->localDiagnostic;
  }
  /**
   * The current BFD session state as seen by the transmitting system. These
   * states are specified in section 4.1 ofRFC5880
   *
   * Accepted values: ADMIN_DOWN, DOWN, INIT, STATE_UNSPECIFIED, UP
   *
   * @param self::LOCAL_STATE_* $localState
   */
  public function setLocalState($localState)
  {
    $this->localState = $localState;
  }
  /**
   * @return self::LOCAL_STATE_*
   */
  public function getLocalState()
  {
    return $this->localState;
  }
  /**
   * Negotiated transmit interval for control packets.
   *
   * @param string $negotiatedLocalControlTxIntervalMs
   */
  public function setNegotiatedLocalControlTxIntervalMs($negotiatedLocalControlTxIntervalMs)
  {
    $this->negotiatedLocalControlTxIntervalMs = $negotiatedLocalControlTxIntervalMs;
  }
  /**
   * @return string
   */
  public function getNegotiatedLocalControlTxIntervalMs()
  {
    return $this->negotiatedLocalControlTxIntervalMs;
  }
  /**
   * The most recent Rx control packet for this BFD session.
   *
   * @param BfdPacket $rxPacket
   */
  public function setRxPacket(BfdPacket $rxPacket)
  {
    $this->rxPacket = $rxPacket;
  }
  /**
   * @return BfdPacket
   */
  public function getRxPacket()
  {
    return $this->rxPacket;
  }
  /**
   * The most recent Tx control packet for this BFD session.
   *
   * @param BfdPacket $txPacket
   */
  public function setTxPacket(BfdPacket $txPacket)
  {
    $this->txPacket = $txPacket;
  }
  /**
   * @return BfdPacket
   */
  public function getTxPacket()
  {
    return $this->txPacket;
  }
  /**
   * Session uptime in milliseconds. Value will be 0 if session is not up.
   *
   * @param string $uptimeMs
   */
  public function setUptimeMs($uptimeMs)
  {
    $this->uptimeMs = $uptimeMs;
  }
  /**
   * @return string
   */
  public function getUptimeMs()
  {
    return $this->uptimeMs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BfdStatus::class, 'Google_Service_Compute_BfdStatus');
