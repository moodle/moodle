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

class InterconnectDiagnosticsLinkStatus extends \Google\Collection
{
  /**
   * The interface is unable to communicate with the remote end.
   */
  public const OPERATIONAL_STATUS_LINK_OPERATIONAL_STATUS_DOWN = 'LINK_OPERATIONAL_STATUS_DOWN';
  /**
   * The interface has low level communication with the remote end.
   */
  public const OPERATIONAL_STATUS_LINK_OPERATIONAL_STATUS_UP = 'LINK_OPERATIONAL_STATUS_UP';
  protected $collection_key = 'arpCaches';
  protected $arpCachesType = InterconnectDiagnosticsARPEntry::class;
  protected $arpCachesDataType = 'array';
  /**
   * The unique ID for this link assigned during turn up by Google.
   *
   * @var string
   */
  public $circuitId;
  /**
   * The Demarc address assigned by Google and provided in the LoA.
   *
   * @var string
   */
  public $googleDemarc;
  protected $lacpStatusType = InterconnectDiagnosticsLinkLACPStatus::class;
  protected $lacpStatusDataType = '';
  protected $macsecType = InterconnectDiagnosticsMacsecStatus::class;
  protected $macsecDataType = '';
  /**
   * The operational status of the link.
   *
   * @var string
   */
  public $operationalStatus;
  protected $receivingOpticalPowerType = InterconnectDiagnosticsLinkOpticalPower::class;
  protected $receivingOpticalPowerDataType = '';
  protected $transmittingOpticalPowerType = InterconnectDiagnosticsLinkOpticalPower::class;
  protected $transmittingOpticalPowerDataType = '';

  /**
   * A list of InterconnectDiagnostics.ARPEntry objects, describing the ARP
   * neighbor entries seen on this link. This will be empty if the link is
   * bundled
   *
   * @param InterconnectDiagnosticsARPEntry[] $arpCaches
   */
  public function setArpCaches($arpCaches)
  {
    $this->arpCaches = $arpCaches;
  }
  /**
   * @return InterconnectDiagnosticsARPEntry[]
   */
  public function getArpCaches()
  {
    return $this->arpCaches;
  }
  /**
   * The unique ID for this link assigned during turn up by Google.
   *
   * @param string $circuitId
   */
  public function setCircuitId($circuitId)
  {
    $this->circuitId = $circuitId;
  }
  /**
   * @return string
   */
  public function getCircuitId()
  {
    return $this->circuitId;
  }
  /**
   * The Demarc address assigned by Google and provided in the LoA.
   *
   * @param string $googleDemarc
   */
  public function setGoogleDemarc($googleDemarc)
  {
    $this->googleDemarc = $googleDemarc;
  }
  /**
   * @return string
   */
  public function getGoogleDemarc()
  {
    return $this->googleDemarc;
  }
  /**
   * @param InterconnectDiagnosticsLinkLACPStatus $lacpStatus
   */
  public function setLacpStatus(InterconnectDiagnosticsLinkLACPStatus $lacpStatus)
  {
    $this->lacpStatus = $lacpStatus;
  }
  /**
   * @return InterconnectDiagnosticsLinkLACPStatus
   */
  public function getLacpStatus()
  {
    return $this->lacpStatus;
  }
  /**
   * Describes the status of MACsec encryption on this link.
   *
   * @param InterconnectDiagnosticsMacsecStatus $macsec
   */
  public function setMacsec(InterconnectDiagnosticsMacsecStatus $macsec)
  {
    $this->macsec = $macsec;
  }
  /**
   * @return InterconnectDiagnosticsMacsecStatus
   */
  public function getMacsec()
  {
    return $this->macsec;
  }
  /**
   * The operational status of the link.
   *
   * Accepted values: LINK_OPERATIONAL_STATUS_DOWN, LINK_OPERATIONAL_STATUS_UP
   *
   * @param self::OPERATIONAL_STATUS_* $operationalStatus
   */
  public function setOperationalStatus($operationalStatus)
  {
    $this->operationalStatus = $operationalStatus;
  }
  /**
   * @return self::OPERATIONAL_STATUS_*
   */
  public function getOperationalStatus()
  {
    return $this->operationalStatus;
  }
  /**
   * An InterconnectDiagnostics.LinkOpticalPower object, describing the current
   * value and status of the received light level.
   *
   * @param InterconnectDiagnosticsLinkOpticalPower $receivingOpticalPower
   */
  public function setReceivingOpticalPower(InterconnectDiagnosticsLinkOpticalPower $receivingOpticalPower)
  {
    $this->receivingOpticalPower = $receivingOpticalPower;
  }
  /**
   * @return InterconnectDiagnosticsLinkOpticalPower
   */
  public function getReceivingOpticalPower()
  {
    return $this->receivingOpticalPower;
  }
  /**
   * An InterconnectDiagnostics.LinkOpticalPower object, describing the current
   * value and status of the transmitted light level.
   *
   * @param InterconnectDiagnosticsLinkOpticalPower $transmittingOpticalPower
   */
  public function setTransmittingOpticalPower(InterconnectDiagnosticsLinkOpticalPower $transmittingOpticalPower)
  {
    $this->transmittingOpticalPower = $transmittingOpticalPower;
  }
  /**
   * @return InterconnectDiagnosticsLinkOpticalPower
   */
  public function getTransmittingOpticalPower()
  {
    return $this->transmittingOpticalPower;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InterconnectDiagnosticsLinkStatus::class, 'Google_Service_Compute_InterconnectDiagnosticsLinkStatus');
