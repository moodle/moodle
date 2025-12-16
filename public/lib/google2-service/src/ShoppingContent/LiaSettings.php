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

class LiaSettings extends \Google\Collection
{
  protected $collection_key = 'countrySettings';
  /**
   * The ID of the account to which these LIA settings belong. Ignored upon
   * update, always present in get request responses.
   *
   * @var string
   */
  public $accountId;
  protected $countrySettingsType = LiaCountrySettings::class;
  protected $countrySettingsDataType = 'array';
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#liaSettings`"
   *
   * @var string
   */
  public $kind;

  /**
   * The ID of the account to which these LIA settings belong. Ignored upon
   * update, always present in get request responses.
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
   * The LIA settings for each country.
   *
   * @param LiaCountrySettings[] $countrySettings
   */
  public function setCountrySettings($countrySettings)
  {
    $this->countrySettings = $countrySettings;
  }
  /**
   * @return LiaCountrySettings[]
   */
  public function getCountrySettings()
  {
    return $this->countrySettings;
  }
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#liaSettings`"
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LiaSettings::class, 'Google_Service_ShoppingContent_LiaSettings');
