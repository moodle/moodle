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

class RemarketingList extends \Google\Model
{
  /**
   * covers sources not supported in DCM other than those listed below
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_OTHER = 'REMARKETING_LIST_SOURCE_OTHER';
  /**
   * ADX
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_ADX = 'REMARKETING_LIST_SOURCE_ADX';
  /**
   * DFP
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_DFP = 'REMARKETING_LIST_SOURCE_DFP';
  /**
   * XFP
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_XFP = 'REMARKETING_LIST_SOURCE_XFP';
  /**
   * DoubleClick Campaign Manager
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_DFA = 'REMARKETING_LIST_SOURCE_DFA';
  /**
   * Google Analytics Premium
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_GA = 'REMARKETING_LIST_SOURCE_GA';
  /**
   * Youtube
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_YOUTUBE = 'REMARKETING_LIST_SOURCE_YOUTUBE';
  /**
   * DoubleClick Bid Manager
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_DBM = 'REMARKETING_LIST_SOURCE_DBM';
  /**
   * G+
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_GPLUS = 'REMARKETING_LIST_SOURCE_GPLUS';
  /**
   * DoubleClick Audience Center
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_DMP = 'REMARKETING_LIST_SOURCE_DMP';
  /**
   * Playstore
   */
  public const LIST_SOURCE_REMARKETING_LIST_SOURCE_PLAY_STORE = 'REMARKETING_LIST_SOURCE_PLAY_STORE';
  /**
   * Account ID of this remarketing list. This is a read-only, auto-generated
   * field that is only returned in GET requests.
   *
   * @var string
   */
  public $accountId;
  /**
   * Whether this remarketing list is active.
   *
   * @var bool
   */
  public $active;
  /**
   * Dimension value for the advertiser ID that owns this remarketing list. This
   * is a required field.
   *
   * @var string
   */
  public $advertiserId;
  protected $advertiserIdDimensionValueType = DimensionValue::class;
  protected $advertiserIdDimensionValueDataType = '';
  /**
   * Remarketing list description.
   *
   * @var string
   */
  public $description;
  /**
   * Remarketing list ID. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#remarketingList".
   *
   * @var string
   */
  public $kind;
  /**
   * Number of days that a user should remain in the remarketing list without an
   * impression. Acceptable values are 1 to 540, inclusive.
   *
   * @var string
   */
  public $lifeSpan;
  protected $listPopulationRuleType = ListPopulationRule::class;
  protected $listPopulationRuleDataType = '';
  /**
   * Number of users currently in the list. This is a read-only field.
   *
   * @var string
   */
  public $listSize;
  /**
   * Product from which this remarketing list was originated.
   *
   * @var string
   */
  public $listSource;
  /**
   * Name of the remarketing list. This is a required field. Must be no greater
   * than 128 characters long.
   *
   * @var string
   */
  public $name;
  /**
   * Subaccount ID of this remarketing list. This is a read-only, auto-generated
   * field that is only returned in GET requests.
   *
   * @var string
   */
  public $subaccountId;

  /**
   * Account ID of this remarketing list. This is a read-only, auto-generated
   * field that is only returned in GET requests.
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
   * Whether this remarketing list is active.
   *
   * @param bool $active
   */
  public function setActive($active)
  {
    $this->active = $active;
  }
  /**
   * @return bool
   */
  public function getActive()
  {
    return $this->active;
  }
  /**
   * Dimension value for the advertiser ID that owns this remarketing list. This
   * is a required field.
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
   * Remarketing list description.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Remarketing list ID. This is a read-only, auto-generated field.
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
   * "dfareporting#remarketingList".
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
   * Number of days that a user should remain in the remarketing list without an
   * impression. Acceptable values are 1 to 540, inclusive.
   *
   * @param string $lifeSpan
   */
  public function setLifeSpan($lifeSpan)
  {
    $this->lifeSpan = $lifeSpan;
  }
  /**
   * @return string
   */
  public function getLifeSpan()
  {
    return $this->lifeSpan;
  }
  /**
   * Rule used to populate the remarketing list with users.
   *
   * @param ListPopulationRule $listPopulationRule
   */
  public function setListPopulationRule(ListPopulationRule $listPopulationRule)
  {
    $this->listPopulationRule = $listPopulationRule;
  }
  /**
   * @return ListPopulationRule
   */
  public function getListPopulationRule()
  {
    return $this->listPopulationRule;
  }
  /**
   * Number of users currently in the list. This is a read-only field.
   *
   * @param string $listSize
   */
  public function setListSize($listSize)
  {
    $this->listSize = $listSize;
  }
  /**
   * @return string
   */
  public function getListSize()
  {
    return $this->listSize;
  }
  /**
   * Product from which this remarketing list was originated.
   *
   * Accepted values: REMARKETING_LIST_SOURCE_OTHER,
   * REMARKETING_LIST_SOURCE_ADX, REMARKETING_LIST_SOURCE_DFP,
   * REMARKETING_LIST_SOURCE_XFP, REMARKETING_LIST_SOURCE_DFA,
   * REMARKETING_LIST_SOURCE_GA, REMARKETING_LIST_SOURCE_YOUTUBE,
   * REMARKETING_LIST_SOURCE_DBM, REMARKETING_LIST_SOURCE_GPLUS,
   * REMARKETING_LIST_SOURCE_DMP, REMARKETING_LIST_SOURCE_PLAY_STORE
   *
   * @param self::LIST_SOURCE_* $listSource
   */
  public function setListSource($listSource)
  {
    $this->listSource = $listSource;
  }
  /**
   * @return self::LIST_SOURCE_*
   */
  public function getListSource()
  {
    return $this->listSource;
  }
  /**
   * Name of the remarketing list. This is a required field. Must be no greater
   * than 128 characters long.
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
   * Subaccount ID of this remarketing list. This is a read-only, auto-generated
   * field that is only returned in GET requests.
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
class_alias(RemarketingList::class, 'Google_Service_Dfareporting_RemarketingList');
