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

class TargetingTemplate extends \Google\Model
{
  /**
   * Account ID of this targeting template. This field, if left unset, will be
   * auto-generated on insert and is read-only after insert.
   *
   * @var string
   */
  public $accountId;
  /**
   * Advertiser ID of this targeting template. This is a required field on
   * insert and is read-only after insert.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  protected $contextualKeywordTargetingType = ContextualKeywordTargeting::class;
  protected $contextualKeywordTargetingDataType = '';
  protected $dayPartTargetingType = DayPartTargeting::class;
  protected $dayPartTargetingDataType = '';
  protected $geoTargetingType = GeoTargeting::class;
  protected $geoTargetingDataType = '';
  /**
   * ID of this targeting template. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  protected $keyValueTargetingExpressionType = KeyValueTargetingExpression::class;
  protected $keyValueTargetingExpressionDataType = '';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#targetingTemplate".
   *
   * @var string
   */
  public $kind;
  protected $languageTargetingType = LanguageTargeting::class;
  protected $languageTargetingDataType = '';
  protected $listTargetingExpressionType = ListTargetingExpression::class;
  protected $listTargetingExpressionDataType = '';
  /**
   * Name of this targeting template. This field is required. It must be less
   * than 256 characters long and unique within an advertiser.
   *
   * @var string
   */
  public $name;
  /**
   * Subaccount ID of this targeting template. This field, if left unset, will
   * be auto-generated on insert and is read-only after insert.
   *
   * @var string
   */
  public $subaccountId;
  protected $technologyTargetingType = TechnologyTargeting::class;
  protected $technologyTargetingDataType = '';

  /**
   * Account ID of this targeting template. This field, if left unset, will be
   * auto-generated on insert and is read-only after insert.
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
   * Advertiser ID of this targeting template. This is a required field on
   * insert and is read-only after insert.
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
   * Optional. Contextual keyword targeting criteria.
   *
   * @param ContextualKeywordTargeting $contextualKeywordTargeting
   */
  public function setContextualKeywordTargeting(ContextualKeywordTargeting $contextualKeywordTargeting)
  {
    $this->contextualKeywordTargeting = $contextualKeywordTargeting;
  }
  /**
   * @return ContextualKeywordTargeting
   */
  public function getContextualKeywordTargeting()
  {
    return $this->contextualKeywordTargeting;
  }
  /**
   * Time and day targeting criteria.
   *
   * @param DayPartTargeting $dayPartTargeting
   */
  public function setDayPartTargeting(DayPartTargeting $dayPartTargeting)
  {
    $this->dayPartTargeting = $dayPartTargeting;
  }
  /**
   * @return DayPartTargeting
   */
  public function getDayPartTargeting()
  {
    return $this->dayPartTargeting;
  }
  /**
   * Geographical targeting criteria.
   *
   * @param GeoTargeting $geoTargeting
   */
  public function setGeoTargeting(GeoTargeting $geoTargeting)
  {
    $this->geoTargeting = $geoTargeting;
  }
  /**
   * @return GeoTargeting
   */
  public function getGeoTargeting()
  {
    return $this->geoTargeting;
  }
  /**
   * ID of this targeting template. This is a read-only, auto-generated field.
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
   * Key-value targeting criteria.
   *
   * @param KeyValueTargetingExpression $keyValueTargetingExpression
   */
  public function setKeyValueTargetingExpression(KeyValueTargetingExpression $keyValueTargetingExpression)
  {
    $this->keyValueTargetingExpression = $keyValueTargetingExpression;
  }
  /**
   * @return KeyValueTargetingExpression
   */
  public function getKeyValueTargetingExpression()
  {
    return $this->keyValueTargetingExpression;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#targetingTemplate".
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
   * Language targeting criteria.
   *
   * @param LanguageTargeting $languageTargeting
   */
  public function setLanguageTargeting(LanguageTargeting $languageTargeting)
  {
    $this->languageTargeting = $languageTargeting;
  }
  /**
   * @return LanguageTargeting
   */
  public function getLanguageTargeting()
  {
    return $this->languageTargeting;
  }
  /**
   * Remarketing list targeting criteria.
   *
   * @param ListTargetingExpression $listTargetingExpression
   */
  public function setListTargetingExpression(ListTargetingExpression $listTargetingExpression)
  {
    $this->listTargetingExpression = $listTargetingExpression;
  }
  /**
   * @return ListTargetingExpression
   */
  public function getListTargetingExpression()
  {
    return $this->listTargetingExpression;
  }
  /**
   * Name of this targeting template. This field is required. It must be less
   * than 256 characters long and unique within an advertiser.
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
   * Subaccount ID of this targeting template. This field, if left unset, will
   * be auto-generated on insert and is read-only after insert.
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
   * Technology platform targeting criteria.
   *
   * @param TechnologyTargeting $technologyTargeting
   */
  public function setTechnologyTargeting(TechnologyTargeting $technologyTargeting)
  {
    $this->technologyTargeting = $technologyTargeting;
  }
  /**
   * @return TechnologyTargeting
   */
  public function getTechnologyTargeting()
  {
    return $this->technologyTargeting;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TargetingTemplate::class, 'Google_Service_Dfareporting_TargetingTemplate');
