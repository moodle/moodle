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

namespace Google\Service\ShoppingContent;

class AccountBusinessIdentity extends \Google\Model
{
  protected $blackOwnedType = AccountIdentityType::class;
  protected $blackOwnedDataType = '';
  /**
   * Required. By setting this field, your business may be included in
   * promotions for all the selected attributes. If you clear this option, it
   * won't affect your identification with any of the attributes. For this field
   * to be set, the merchant must self identify with at least one of the
   * `AccountIdentityType`. If none are included, the request will be considered
   * invalid.
   *
   * @var bool
   */
  public $includeForPromotions;
  protected $latinoOwnedType = AccountIdentityType::class;
  protected $latinoOwnedDataType = '';
  protected $smallBusinessType = AccountIdentityType::class;
  protected $smallBusinessDataType = '';
  protected $veteranOwnedType = AccountIdentityType::class;
  protected $veteranOwnedDataType = '';
  protected $womenOwnedType = AccountIdentityType::class;
  protected $womenOwnedDataType = '';

  /**
   * Specifies whether the business identifies itself as being black-owned. This
   * optional field is only available for merchants with a business country set
   * to "US". This field is not allowed for marketplaces or marketplace sellers.
   *
   * @param AccountIdentityType $blackOwned
   */
  public function setBlackOwned(AccountIdentityType $blackOwned)
  {
    $this->blackOwned = $blackOwned;
  }
  /**
   * @return AccountIdentityType
   */
  public function getBlackOwned()
  {
    return $this->blackOwned;
  }
  /**
   * Required. By setting this field, your business may be included in
   * promotions for all the selected attributes. If you clear this option, it
   * won't affect your identification with any of the attributes. For this field
   * to be set, the merchant must self identify with at least one of the
   * `AccountIdentityType`. If none are included, the request will be considered
   * invalid.
   *
   * @param bool $includeForPromotions
   */
  public function setIncludeForPromotions($includeForPromotions)
  {
    $this->includeForPromotions = $includeForPromotions;
  }
  /**
   * @return bool
   */
  public function getIncludeForPromotions()
  {
    return $this->includeForPromotions;
  }
  /**
   * Specifies whether the business identifies itself as being latino-owned.
   * This optional field is only available for merchants with a business country
   * set to "US". This field is not allowed for marketplaces or marketplace
   * sellers.
   *
   * @param AccountIdentityType $latinoOwned
   */
  public function setLatinoOwned(AccountIdentityType $latinoOwned)
  {
    $this->latinoOwned = $latinoOwned;
  }
  /**
   * @return AccountIdentityType
   */
  public function getLatinoOwned()
  {
    return $this->latinoOwned;
  }
  /**
   * Specifies whether the business identifies itself as a small business. This
   * optional field is only available for merchants with a business country set
   * to "US". It is also not allowed for marketplaces, but it is allowed to
   * marketplace sellers.
   *
   * @param AccountIdentityType $smallBusiness
   */
  public function setSmallBusiness(AccountIdentityType $smallBusiness)
  {
    $this->smallBusiness = $smallBusiness;
  }
  /**
   * @return AccountIdentityType
   */
  public function getSmallBusiness()
  {
    return $this->smallBusiness;
  }
  /**
   * Specifies whether the business identifies itself as being veteran-owned.
   * This optional field is only available for merchants with a business country
   * set to "US". This field is not allowed for marketplaces or marketplace
   * sellers.
   *
   * @param AccountIdentityType $veteranOwned
   */
  public function setVeteranOwned(AccountIdentityType $veteranOwned)
  {
    $this->veteranOwned = $veteranOwned;
  }
  /**
   * @return AccountIdentityType
   */
  public function getVeteranOwned()
  {
    return $this->veteranOwned;
  }
  /**
   * Specifies whether the business identifies itself as being women-owned. This
   * optional field is only available for merchants with a business country set
   * to "US". This field is not allowed for marketplaces or marketplace sellers.
   *
   * @param AccountIdentityType $womenOwned
   */
  public function setWomenOwned(AccountIdentityType $womenOwned)
  {
    $this->womenOwned = $womenOwned;
  }
  /**
   * @return AccountIdentityType
   */
  public function getWomenOwned()
  {
    return $this->womenOwned;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountBusinessIdentity::class, 'Google_Service_ShoppingContent_AccountBusinessIdentity');
