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

namespace Google\Service\Dfareporting;

class CreativeGroup extends \Google\Model
{
  /**
   * Account ID of this creative group. This is a read-only field that can be
   * left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Advertiser ID of this creative group. This is a required field on
   * insertion.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Subgroup of the creative group. Assign your creative groups to a subgroup
   * in order to filter or manage them more easily. This field is required on
   * insertion and is read-only after insertion. Acceptable values are 1 to 2,
   * inclusive.
   *
   * @var int
   */
  public $groupNumber;
  /**
   * ID of this creative group. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#creativeGroup".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this creative group. This is a required field and must be less than
   * 256 characters long and unique among creative groups of the same
   * advertiser.
   *
   * @var string
   */
  public $name;
  /**
   * Subaccount ID of this creative group. This is a read-only field that can be
   * left blank.
   *
   * @var string
   */
  public $subaccountId;

  /**
   * Account ID of this creative group. This is a read-only field that can be
   * left blank.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Advertiser ID of this creative group. This is a required field on
   * insertion.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Dimension value for the ID of the advertiser. This is a read-only, auto-
   * generated field.
   *
   * @param DimensionValue $advertiserIdDimensionValue
   */
  public function setAdvertiserIdDimensionValue(DimensionValue $advertiserIdDimensionValue)
  {
    $this->advertiserIdDimensionValue = $advertiserIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getAdvertiserIdDimensionValue()
  {
    return $this->advertiserIdDimensionValue;
  }
  /**
   * Subgroup of the creative group. Assign your creative groups to a subgroup
   * in order to filter or manage them more easily. This field is required on
   * insertion and is read-only after insertion. Acceptable values are 1 to 2,
   * inclusive.
   *
   * @param int $groupNumber
   */
  public function setGroupNumber($groupNumber)
  {
    $this->groupNumber = $groupNumber;
  }
  /**
   * @return int
   */
  public function getGroupNumber()
  {
    return $this->groupNumber;
  }
  /**
   * ID of this creative group. This is a read-only, auto-generated field.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#creativeGroup".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Name of this creative group. This is a required field and must be less than
   * 256 characters long and unique among creative groups of the same
   * advertiser.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Subaccount ID of this creative group. This is a read-only field that can be
   * left blank.
   *
   * @param string $subaccountId
   */
  public function setSubaccountId($subaccountId)
  {
    $this->subaccountId = $subaccountId;
  }
  /**
   * @return string
   */
  public function getSubaccountId()
  {
    return $this->subaccountId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreativeGroup::class, 'Google_Service_Dfareporting_CreativeGroup');
