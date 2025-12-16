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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAccountSummary extends \Google\Collection
{
  protected $collection_key = 'propertySummaries';
  /**
   * Resource name of account referred to by this account summary Format:
   * accounts/{account_id} Example: "accounts/1000"
   *
   * @var string
   */
  public $account;
  /**
   * Display name for the account referred to in this account summary.
   *
   * @var string
   */
  public $displayName;
  /**
   * Resource name for this account summary. Format:
   * accountSummaries/{account_id} Example: "accountSummaries/1000"
   *
   * @var string
   */
  public $name;
  protected $propertySummariesType = GoogleAnalyticsAdminV1betaPropertySummary::class;
  protected $propertySummariesDataType = 'array';

  /**
   * Resource name of account referred to by this account summary Format:
   * accounts/{account_id} Example: "accounts/1000"
   *
   * @param string $account
   */
  public function setAccount($account)
  {
    $this->account = $account;
  }
  /**
   * @return string
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Display name for the account referred to in this account summary.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Resource name for this account summary. Format:
   * accountSummaries/{account_id} Example: "accountSummaries/1000"
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
   * List of summaries for child accounts of this account.
   *
   * @param GoogleAnalyticsAdminV1betaPropertySummary[] $propertySummaries
   */
  public function setPropertySummaries($propertySummaries)
  {
    $this->propertySummaries = $propertySummaries;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaPropertySummary[]
   */
  public function getPropertySummaries()
  {
    return $this->propertySummaries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAccountSummary::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAccountSummary');
