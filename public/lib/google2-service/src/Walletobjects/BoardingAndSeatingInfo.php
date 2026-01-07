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

class BoardingAndSeatingInfo extends \Google\Model
{
  public const BOARDING_DOOR_BOARDING_DOOR_UNSPECIFIED = 'BOARDING_DOOR_UNSPECIFIED';
  public const BOARDING_DOOR_FRONT = 'FRONT';
  /**
   * Legacy alias for `FRONT`. Deprecated.
   *
   * @deprecated
   */
  public const BOARDING_DOOR_front = 'front';
  public const BOARDING_DOOR_BACK = 'BACK';
  /**
   * Legacy alias for `BACK`. Deprecated.
   *
   * @deprecated
   */
  public const BOARDING_DOOR_back = 'back';
  /**
   * Set this field only if this flight boards through more than one door or
   * bridge and you want to explicitly print the door location on the boarding
   * pass. Most airlines route their passengers to the right door or bridge by
   * refering to doors/bridges by the `seatClass`. In those cases `boardingDoor`
   * should not be set.
   *
   * @var string
   */
  public $boardingDoor;
  /**
   * The value of boarding group (or zone) this passenger shall board with. eg:
   * "B" The label for this value will be determined by the `boardingPolicy`
   * field in the `flightClass` referenced by this object.
   *
   * @var string
   */
  public $boardingGroup;
  /**
   * The value of boarding position. eg: "76"
   *
   * @var string
   */
  public $boardingPosition;
  protected $boardingPrivilegeImageType = Image::class;
  protected $boardingPrivilegeImageDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#boardingAndSeatingInfo"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $seatAssignmentType = LocalizedString::class;
  protected $seatAssignmentDataType = '';
  /**
   * The value of the seat class. eg: "Economy" or "Economy Plus"
   *
   * @var string
   */
  public $seatClass;
  /**
   * The value of passenger seat. If there is no specific identifier, use
   * `seatAssignment` instead. eg: "25A"
   *
   * @var string
   */
  public $seatNumber;
  /**
   * The sequence number on the boarding pass. This usually matches the sequence
   * in which the passengers checked in. Airline might use the number for manual
   * boarding and bag tags. eg: "49"
   *
   * @var string
   */
  public $sequenceNumber;

  /**
   * Set this field only if this flight boards through more than one door or
   * bridge and you want to explicitly print the door location on the boarding
   * pass. Most airlines route their passengers to the right door or bridge by
   * refering to doors/bridges by the `seatClass`. In those cases `boardingDoor`
   * should not be set.
   *
   * Accepted values: BOARDING_DOOR_UNSPECIFIED, FRONT, front, BACK, back
   *
   * @param self::BOARDING_DOOR_* $boardingDoor
   */
  public function setBoardingDoor($boardingDoor)
  {
    $this->boardingDoor = $boardingDoor;
  }
  /**
   * @return self::BOARDING_DOOR_*
   */
  public function getBoardingDoor()
  {
    return $this->boardingDoor;
  }
  /**
   * The value of boarding group (or zone) this passenger shall board with. eg:
   * "B" The label for this value will be determined by the `boardingPolicy`
   * field in the `flightClass` referenced by this object.
   *
   * @param string $boardingGroup
   */
  public function setBoardingGroup($boardingGroup)
  {
    $this->boardingGroup = $boardingGroup;
  }
  /**
   * @return string
   */
  public function getBoardingGroup()
  {
    return $this->boardingGroup;
  }
  /**
   * The value of boarding position. eg: "76"
   *
   * @param string $boardingPosition
   */
  public function setBoardingPosition($boardingPosition)
  {
    $this->boardingPosition = $boardingPosition;
  }
  /**
   * @return string
   */
  public function getBoardingPosition()
  {
    return $this->boardingPosition;
  }
  /**
   * A small image shown above the boarding barcode. Airlines can use it to
   * communicate any special boarding privileges. In the event the security
   * program logo is also set, this image might be rendered alongside the logo
   * for that security program.
   *
   * @param Image $boardingPrivilegeImage
   */
  public function setBoardingPrivilegeImage(Image $boardingPrivilegeImage)
  {
    $this->boardingPrivilegeImage = $boardingPrivilegeImage;
  }
  /**
   * @return Image
   */
  public function getBoardingPrivilegeImage()
  {
    return $this->boardingPrivilegeImage;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#boardingAndSeatingInfo"`.
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
   * The passenger's seat assignment. To be used when there is no specific
   * identifier to use in `seatNumber`. eg: "assigned at gate"
   *
   * @param LocalizedString $seatAssignment
   */
  public function setSeatAssignment(LocalizedString $seatAssignment)
  {
    $this->seatAssignment = $seatAssignment;
  }
  /**
   * @return LocalizedString
   */
  public function getSeatAssignment()
  {
    return $this->seatAssignment;
  }
  /**
   * The value of the seat class. eg: "Economy" or "Economy Plus"
   *
   * @param string $seatClass
   */
  public function setSeatClass($seatClass)
  {
    $this->seatClass = $seatClass;
  }
  /**
   * @return string
   */
  public function getSeatClass()
  {
    return $this->seatClass;
  }
  /**
   * The value of passenger seat. If there is no specific identifier, use
   * `seatAssignment` instead. eg: "25A"
   *
   * @param string $seatNumber
   */
  public function setSeatNumber($seatNumber)
  {
    $this->seatNumber = $seatNumber;
  }
  /**
   * @return string
   */
  public function getSeatNumber()
  {
    return $this->seatNumber;
  }
  /**
   * The sequence number on the boarding pass. This usually matches the sequence
   * in which the passengers checked in. Airline might use the number for manual
   * boarding and bag tags. eg: "49"
   *
   * @param string $sequenceNumber
   */
  public function setSequenceNumber($sequenceNumber)
  {
    $this->sequenceNumber = $sequenceNumber;
  }
  /**
   * @return string
   */
  public function getSequenceNumber()
  {
    return $this->sequenceNumber;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BoardingAndSeatingInfo::class, 'Google_Service_Walletobjects_BoardingAndSeatingInfo');
