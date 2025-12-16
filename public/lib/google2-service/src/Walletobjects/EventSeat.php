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

namespace Google\Service\Walletobjects;

class EventSeat extends \Google\Model
{
  protected $gateType = LocalizedString::class;
  protected $gateDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventSeat"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $rowType = LocalizedString::class;
  protected $rowDataType = '';
  protected $seatType = LocalizedString::class;
  protected $seatDataType = '';
  protected $sectionType = LocalizedString::class;
  protected $sectionDataType = '';

  /**
   * The gate the ticket holder should enter to get to their seat, such as "A"
   * or "West". This field is localizable so you may translate words or use
   * different alphabets for the characters in an identifier.
   *
   * @param LocalizedString $gate
   */
  public function setGate(LocalizedString $gate)
  {
    $this->gate = $gate;
  }
  /**
   * @return LocalizedString
   */
  public function getGate()
  {
    return $this->gate;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#eventSeat"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * The row of the seat, such as "1", E", "BB", or "A5". This field is
   * localizable so you may translate words or use different alphabets for the
   * characters in an identifier.
   *
   * @param LocalizedString $row
   */
  public function setRow(LocalizedString $row)
  {
    $this->row = $row;
  }
  /**
   * @return LocalizedString
   */
  public function getRow()
  {
    return $this->row;
  }
  /**
   * The seat number, such as "1", "2", "3", or any other seat identifier. This
   * field is localizable so you may translate words or use different alphabets
   * for the characters in an identifier.
   *
   * @param LocalizedString $seat
   */
  public function setSeat(LocalizedString $seat)
  {
    $this->seat = $seat;
  }
  /**
   * @return LocalizedString
   */
  public function getSeat()
  {
    return $this->seat;
  }
  /**
   * The section of the seat, such as "121". This field is localizable so you
   * may translate words or use different alphabets for the characters in an
   * identifier.
   *
   * @param LocalizedString $section
   */
  public function setSection(LocalizedString $section)
  {
    $this->section = $section;
  }
  /**
   * @return LocalizedString
   */
  public function getSection()
  {
    return $this->section;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(EventSeat::class, 'Google_Service_Walletobjects_EventSeat');
