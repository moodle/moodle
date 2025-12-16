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

class FloodlightActivityGroup extends \Google\Model
{
  public const TYPE_COUNTER = 'COUNTER';
  public const TYPE_SALE = 'SALE';
  /**
   * Account ID of this floodlight activity group. This is a read-only field
   * that can be left blank.
   *
   * @var string
   */
  public $accountId;
  /**
   * Advertiser ID of this floodlight activity group. If this field is left
   * blank, the value will be copied over either from the floodlight
   * configuration's advertiser or from the existing activity group's
   * advertiser.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Floodlight configuration ID of this floodlight activity group. This is a
   * required field.
   *
   * @var string
   */
  public $floodlightConfigurationId;
  protected $floodlightConfigurationIdDimensionValueType = DimensionValue::class;
  protected $floodlightConfigurationIdDimensionValueDataType = '';
  /**
   * ID of this floodlight activity group. This is a read-only, auto-generated
   * field.
   *
   * @var string
   */
  public $id;
  protected $idDimensionValueType = DimensionValue::class;
  protected $idDimensionValueDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightActivityGroup".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this floodlight activity group. This is a required field. Must be
   * less than 65 characters long and cannot contain quotes.
   *
   * @var string
   */
  public $name;
  /**
   * Subaccount ID of this floodlight activity group. This is a read-only field
   * that can be left blank.
   *
   * @var string
   */
  public $subaccountId;
  /**
   * Value of the type= parameter in the floodlight tag, which the ad servers
   * use to identify the activity group that the activity belongs to. This is
   * optional: if empty, a new tag string will be generated for you. This string
   * must be 1 to 8 characters long, with valid characters being a-z0-9[ _ ].
   * This tag string must also be unique among activity groups of the same
   * floodlight configuration. This field is read-only after insertion.
   *
   * @var string
   */
  public $tagString;
  /**
   * Type of the floodlight activity group. This is a required field that is
   * read-only after insertion.
   *
   * @var string
   */
  public $type;

  /**
   * Account ID of this floodlight activity group. This is a read-only field
   * that can be left blank.
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
   * Advertiser ID of this floodlight activity group. If this field is left
   * blank, the value will be copied over either from the floodlight
   * configuration's advertiser or from the existing activity group's
   * advertiser.
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
   * Floodlight configuration ID of this floodlight activity group. This is a
   * required field.
   *
   * @param string $floodlightConfigurationId
   */
  public function setFloodlightConfigurationId($floodlightConfigurationId)
  {
    $this->floodlightConfigurationId = $floodlightConfigurationId;
  }
  /**
   * @return string
   */
  public function getFloodlightConfigurationId()
  {
    return $this->floodlightConfigurationId;
  }
  /**
   * Dimension value for the ID of the floodlight configuration. This is a read-
   * only, auto-generated field.
   *
   * @param DimensionValue $floodlightConfigurationIdDimensionValue
   */
  public function setFloodlightConfigurationIdDimensionValue(DimensionValue $floodlightConfigurationIdDimensionValue)
  {
    $this->floodlightConfigurationIdDimensionValue = $floodlightConfigurationIdDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getFloodlightConfigurationIdDimensionValue()
  {
    return $this->floodlightConfigurationIdDimensionValue;
  }
  /**
   * ID of this floodlight activity group. This is a read-only, auto-generated
   * field.
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
   * Dimension value for the ID of this floodlight activity group. This is a
   * read-only, auto-generated field.
   *
   * @param DimensionValue $idDimensionValue
   */
  public function setIdDimensionValue(DimensionValue $idDimensionValue)
  {
    $this->idDimensionValue = $idDimensionValue;
  }
  /**
   * @return DimensionValue
   */
  public function getIdDimensionValue()
  {
    return $this->idDimensionValue;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#floodlightActivityGroup".
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
   * Name of this floodlight activity group. This is a required field. Must be
   * less than 65 characters long and cannot contain quotes.
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
   * Subaccount ID of this floodlight activity group. This is a read-only field
   * that can be left blank.
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
  /**
   * Value of the type= parameter in the floodlight tag, which the ad servers
   * use to identify the activity group that the activity belongs to. This is
   * optional: if empty, a new tag string will be generated for you. This string
   * must be 1 to 8 characters long, with valid characters being a-z0-9[ _ ].
   * This tag string must also be unique among activity groups of the same
   * floodlight configuration. This field is read-only after insertion.
   *
   * @param string $tagString
   */
  public function setTagString($tagString)
  {
    $this->tagString = $tagString;
  }
  /**
   * @return string
   */
  public function getTagString()
  {
    return $this->tagString;
  }
  /**
   * Type of the floodlight activity group. This is a required field that is
   * read-only after insertion.
   *
   * Accepted values: COUNTER, SALE
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FloodlightActivityGroup::class, 'Google_Service_Dfareporting_FloodlightActivityGroup');
