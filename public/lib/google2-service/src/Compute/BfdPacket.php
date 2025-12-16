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

class BfdPacket extends \Google\Model
{
  public const DIAGNOSTIC_ADMINISTRATIVELY_DOWN = 'ADMINISTRATIVELY_DOWN';
  public const DIAGNOSTIC_CONCATENATED_PATH_DOWN = 'CONCATENATED_PATH_DOWN';
  public const DIAGNOSTIC_CONTROL_DETECTION_TIME_EXPIRED = 'CONTROL_DETECTION_TIME_EXPIRED';
  public const DIAGNOSTIC_DIAGNOSTIC_UNSPECIFIED = 'DIAGNOSTIC_UNSPECIFIED';
  public const DIAGNOSTIC_ECHO_FUNCTION_FAILED = 'ECHO_FUNCTION_FAILED';
  public const DIAGNOSTIC_FORWARDING_PLANE_RESET = 'FORWARDING_PLANE_RESET';
  public const DIAGNOSTIC_NEIGHBOR_SIGNALED_SESSION_DOWN = 'NEIGHBOR_SIGNALED_SESSION_DOWN';
  public const DIAGNOSTIC_NO_DIAGNOSTIC = 'NO_DIAGNOSTIC';
  public const DIAGNOSTIC_PATH_DOWN = 'PATH_DOWN';
  public const DIAGNOSTIC_REVERSE_CONCATENATED_PATH_DOWN = 'REVERSE_CONCATENATED_PATH_DOWN';
  public const STATE_ADMIN_DOWN = 'ADMIN_DOWN';
  public const STATE_DOWN = 'DOWN';
  public const STATE_INIT = 'INIT';
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  public const STATE_UP = 'UP';
  /**
   * The Authentication Present bit of the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @var bool
   */
  public $authenticationPresent;
  /**
   * The Control Plane Independent bit of the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @var bool
   */
  public $controlPlaneIndependent;
  /**
   * The demand bit of the BFD packet. This is specified in section 4.1
   * ofRFC5880
   *
   * @var bool
   */
  public $demand;
  /**
   * The diagnostic code specifies the local system's reason for the last change
   * in session state. This allows remote systems to determine the reason that
   * the previous session failed, for example. These diagnostic codes are
   * specified in section 4.1 ofRFC5880
   *
   * @var string
   */
  public $diagnostic;
  /**
   * The Final bit of the BFD packet. This is specified in section 4.1 ofRFC5880
   *
   * @var bool
   */
  public $final;
  /**
   * The length of the BFD Control packet in bytes. This is specified in section
   * 4.1 ofRFC5880
   *
   * @var string
   */
  public $length;
  /**
   * The Required Min Echo RX Interval value in the BFD packet. This is
   * specified in section 4.1 ofRFC5880
   *
   * @var string
   */
  public $minEchoRxIntervalMs;
  /**
   * The Required Min RX Interval value in the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @var string
   */
  public $minRxIntervalMs;
  /**
   * The Desired Min TX Interval value in the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @var string
   */
  public $minTxIntervalMs;
  /**
   * The detection time multiplier of the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @var string
   */
  public $multiplier;
  /**
   * The multipoint bit of the BFD packet. This is specified in section 4.1
   * ofRFC5880
   *
   * @var bool
   */
  public $multipoint;
  /**
   * The My Discriminator value in the BFD packet. This is specified in section
   * 4.1 ofRFC5880
   *
   * @var string
   */
  public $myDiscriminator;
  /**
   * The Poll bit of the BFD packet. This is specified in section 4.1 ofRFC5880
   *
   * @var bool
   */
  public $poll;
  /**
   * The current BFD session state as seen by the transmitting system. These
   * states are specified in section 4.1 ofRFC5880
   *
   * @var string
   */
  public $state;
  /**
   * The version number of the BFD protocol, as specified in section 4.1
   * ofRFC5880.
   *
   * @var string
   */
  public $version;
  /**
   * The Your Discriminator value in the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @var string
   */
  public $yourDiscriminator;

  /**
   * The Authentication Present bit of the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @param bool $authenticationPresent
   */
  public function setAuthenticationPresent($authenticationPresent)
  {
    $this->authenticationPresent = $authenticationPresent;
  }
  /**
   * @return bool
   */
  public function getAuthenticationPresent()
  {
    return $this->authenticationPresent;
  }
  /**
   * The Control Plane Independent bit of the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @param bool $controlPlaneIndependent
   */
  public function setControlPlaneIndependent($controlPlaneIndependent)
  {
    $this->controlPlaneIndependent = $controlPlaneIndependent;
  }
  /**
   * @return bool
   */
  public function getControlPlaneIndependent()
  {
    return $this->controlPlaneIndependent;
  }
  /**
   * The demand bit of the BFD packet. This is specified in section 4.1
   * ofRFC5880
   *
   * @param bool $demand
   */
  public function setDemand($demand)
  {
    $this->demand = $demand;
  }
  /**
   * @return bool
   */
  public function getDemand()
  {
    return $this->demand;
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
   * @param self::DIAGNOSTIC_* $diagnostic
   */
  public function setDiagnostic($diagnostic)
  {
    $this->diagnostic = $diagnostic;
  }
  /**
   * @return self::DIAGNOSTIC_*
   */
  public function getDiagnostic()
  {
    return $this->diagnostic;
  }
  /**
   * The Final bit of the BFD packet. This is specified in section 4.1 ofRFC5880
   *
   * @param bool $final
   */
  public function setFinal($final)
  {
    $this->final = $final;
  }
  /**
   * @return bool
   */
  public function getFinal()
  {
    return $this->final;
  }
  /**
   * The length of the BFD Control packet in bytes. This is specified in section
   * 4.1 ofRFC5880
   *
   * @param string $length
   */
  public function setLength($length)
  {
    $this->length = $length;
  }
  /**
   * @return string
   */
  public function getLength()
  {
    return $this->length;
  }
  /**
   * The Required Min Echo RX Interval value in the BFD packet. This is
   * specified in section 4.1 ofRFC5880
   *
   * @param string $minEchoRxIntervalMs
   */
  public function setMinEchoRxIntervalMs($minEchoRxIntervalMs)
  {
    $this->minEchoRxIntervalMs = $minEchoRxIntervalMs;
  }
  /**
   * @return string
   */
  public function getMinEchoRxIntervalMs()
  {
    return $this->minEchoRxIntervalMs;
  }
  /**
   * The Required Min RX Interval value in the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @param string $minRxIntervalMs
   */
  public function setMinRxIntervalMs($minRxIntervalMs)
  {
    $this->minRxIntervalMs = $minRxIntervalMs;
  }
  /**
   * @return string
   */
  public function getMinRxIntervalMs()
  {
    return $this->minRxIntervalMs;
  }
  /**
   * The Desired Min TX Interval value in the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @param string $minTxIntervalMs
   */
  public function setMinTxIntervalMs($minTxIntervalMs)
  {
    $this->minTxIntervalMs = $minTxIntervalMs;
  }
  /**
   * @return string
   */
  public function getMinTxIntervalMs()
  {
    return $this->minTxIntervalMs;
  }
  /**
   * The detection time multiplier of the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @param string $multiplier
   */
  public function setMultiplier($multiplier)
  {
    $this->multiplier = $multiplier;
  }
  /**
   * @return string
   */
  public function getMultiplier()
  {
    return $this->multiplier;
  }
  /**
   * The multipoint bit of the BFD packet. This is specified in section 4.1
   * ofRFC5880
   *
   * @param bool $multipoint
   */
  public function setMultipoint($multipoint)
  {
    $this->multipoint = $multipoint;
  }
  /**
   * @return bool
   */
  public function getMultipoint()
  {
    return $this->multipoint;
  }
  /**
   * The My Discriminator value in the BFD packet. This is specified in section
   * 4.1 ofRFC5880
   *
   * @param string $myDiscriminator
   */
  public function setMyDiscriminator($myDiscriminator)
  {
    $this->myDiscriminator = $myDiscriminator;
  }
  /**
   * @return string
   */
  public function getMyDiscriminator()
  {
    return $this->myDiscriminator;
  }
  /**
   * The Poll bit of the BFD packet. This is specified in section 4.1 ofRFC5880
   *
   * @param bool $poll
   */
  public function setPoll($poll)
  {
    $this->poll = $poll;
  }
  /**
   * @return bool
   */
  public function getPoll()
  {
    return $this->poll;
  }
  /**
   * The current BFD session state as seen by the transmitting system. These
   * states are specified in section 4.1 ofRFC5880
   *
   * Accepted values: ADMIN_DOWN, DOWN, INIT, STATE_UNSPECIFIED, UP
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
   * The version number of the BFD protocol, as specified in section 4.1
   * ofRFC5880.
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
  /**
   * The Your Discriminator value in the BFD packet. This is specified in
   * section 4.1 ofRFC5880
   *
   * @param string $yourDiscriminator
   */
  public function setYourDiscriminator($yourDiscriminator)
  {
    $this->yourDiscriminator = $yourDiscriminator;
  }
  /**
   * @return string
   */
  public function getYourDiscriminator()
  {
    return $this->yourDiscriminator;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BfdPacket::class, 'Google_Service_Compute_BfdPacket');
