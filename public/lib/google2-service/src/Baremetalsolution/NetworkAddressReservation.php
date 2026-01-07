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

namespace Google\Service\Baremetalsolution;

class NetworkAddressReservation extends \Google\Model
{
  /**
   * The last address of this reservation block, inclusive. I.e., for cases when
   * reservations are only single addresses, end_address and start_address will
   * be the same. Must be specified as a single IPv4 address, e.g. 10.1.2.2.
   *
   * @var string
   */
  public $endAddress;
  /**
   * A note about this reservation, intended for human consumption.
   *
   * @var string
   */
  public $note;
  /**
   * The first address of this reservation block. Must be specified as a single
   * IPv4 address, e.g. 10.1.2.2.
   *
   * @var string
   */
  public $startAddress;

  /**
   * The last address of this reservation block, inclusive. I.e., for cases when
   * reservations are only single addresses, end_address and start_address will
   * be the same. Must be specified as a single IPv4 address, e.g. 10.1.2.2.
   *
   * @param string $endAddress
   */
  public function setEndAddress($endAddress)
  {
    $this->endAddress = $endAddress;
  }
  /**
   * @return string
   */
  public function getEndAddress()
  {
    return $this->endAddress;
  }
  /**
   * A note about this reservation, intended for human consumption.
   *
   * @param string $note
   */
  public function setNote($note)
  {
    $this->note = $note;
  }
  /**
   * @return string
   */
  public function getNote()
  {
    return $this->note;
  }
  /**
   * The first address of this reservation block. Must be specified as a single
   * IPv4 address, e.g. 10.1.2.2.
   *
   * @param string $startAddress
   */
  public function setStartAddress($startAddress)
  {
    $this->startAddress = $startAddress;
  }
  /**
   * @return string
   */
  public function getStartAddress()
  {
    return $this->startAddress;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(NetworkAddressReservation::class, 'Google_Service_Baremetalsolution_NetworkAddressReservation');
