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

namespace Google\Service\Css;

class Account extends \Google\Collection
{
  /**
   * Unknown account type.
   */
  public const ACCOUNT_TYPE_ACCOUNT_TYPE_UNSPECIFIED = 'ACCOUNT_TYPE_UNSPECIFIED';
  /**
   * CSS group account.
   */
  public const ACCOUNT_TYPE_CSS_GROUP = 'CSS_GROUP';
  /**
   * CSS domain account.
   */
  public const ACCOUNT_TYPE_CSS_DOMAIN = 'CSS_DOMAIN';
  /**
   * MC Primary CSS MCA account.
   */
  public const ACCOUNT_TYPE_MC_PRIMARY_CSS_MCA = 'MC_PRIMARY_CSS_MCA';
  /**
   * MC CSS MCA account.
   */
  public const ACCOUNT_TYPE_MC_CSS_MCA = 'MC_CSS_MCA';
  /**
   * MC Marketplace MCA account.
   */
  public const ACCOUNT_TYPE_MC_MARKETPLACE_MCA = 'MC_MARKETPLACE_MCA';
  /**
   * MC Other MCA account.
   */
  public const ACCOUNT_TYPE_MC_OTHER_MCA = 'MC_OTHER_MCA';
  /**
   * MC Standalone account.
   */
  public const ACCOUNT_TYPE_MC_STANDALONE = 'MC_STANDALONE';
  /**
   * MC MCA sub-account.
   */
  public const ACCOUNT_TYPE_MC_MCA_SUBACCOUNT = 'MC_MCA_SUBACCOUNT';
  protected $collection_key = 'labelIds';
  /**
   * Output only. The type of this account.
   *
   * @var string
   */
  public $accountType;
  /**
   * Automatically created label IDs assigned to the MC account by CSS Center.
   *
   * @var string[]
   */
  public $automaticLabelIds;
  /**
   * The CSS/MC account's short display name.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. Immutable. The CSS/MC account's full name.
   *
   * @var string
   */
  public $fullName;
  /**
   * Output only. Immutable. The CSS/MC account's homepage.
   *
   * @var string
   */
  public $homepageUri;
  /**
   * Manually created label IDs assigned to the CSS/MC account by a CSS parent
   * account.
   *
   * @var string[]
   */
  public $labelIds;
  /**
   * The label resource name. Format: accounts/{account}
   *
   * @var string
   */
  public $name;
  /**
   * The CSS/MC account's parent resource. CSS group for CSS domains; CSS domain
   * for MC accounts. Returned only if the user has access to the parent
   * account. Note: For MC sub-accounts, this is also the CSS domain that is the
   * parent resource of the MCA account, since we are effectively flattening the
   * hierarchy."
   *
   * @var string
   */
  public $parent;

  /**
   * Output only. The type of this account.
   *
   * Accepted values: ACCOUNT_TYPE_UNSPECIFIED, CSS_GROUP, CSS_DOMAIN,
   * MC_PRIMARY_CSS_MCA, MC_CSS_MCA, MC_MARKETPLACE_MCA, MC_OTHER_MCA,
   * MC_STANDALONE, MC_MCA_SUBACCOUNT
   *
   * @param self::ACCOUNT_TYPE_* $accountType
   */
  public function setAccountType($accountType)
  {
    $this->accountType = $accountType;
  }
  /**
   * @return self::ACCOUNT_TYPE_*
   */
  public function getAccountType()
  {
    return $this->accountType;
  }
  /**
   * Automatically created label IDs assigned to the MC account by CSS Center.
   *
   * @param string[] $automaticLabelIds
   */
  public function setAutomaticLabelIds($automaticLabelIds)
  {
    $this->automaticLabelIds = $automaticLabelIds;
  }
  /**
   * @return string[]
   */
  public function getAutomaticLabelIds()
  {
    return $this->automaticLabelIds;
  }
  /**
   * The CSS/MC account's short display name.
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
   * Output only. Immutable. The CSS/MC account's full name.
   *
   * @param string $fullName
   */
  public function setFullName($fullName)
  {
    $this->fullName = $fullName;
  }
  /**
   * @return string
   */
  public function getFullName()
  {
    return $this->fullName;
  }
  /**
   * Output only. Immutable. The CSS/MC account's homepage.
   *
   * @param string $homepageUri
   */
  public function setHomepageUri($homepageUri)
  {
    $this->homepageUri = $homepageUri;
  }
  /**
   * @return string
   */
  public function getHomepageUri()
  {
    return $this->homepageUri;
  }
  /**
   * Manually created label IDs assigned to the CSS/MC account by a CSS parent
   * account.
   *
   * @param string[] $labelIds
   */
  public function setLabelIds($labelIds)
  {
    $this->labelIds = $labelIds;
  }
  /**
   * @return string[]
   */
  public function getLabelIds()
  {
    return $this->labelIds;
  }
  /**
   * The label resource name. Format: accounts/{account}
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
   * The CSS/MC account's parent resource. CSS group for CSS domains; CSS domain
   * for MC accounts. Returned only if the user has access to the parent
   * account. Note: For MC sub-accounts, this is also the CSS domain that is the
   * parent resource of the MCA account, since we are effectively flattening the
   * hierarchy."
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_Css_Account');
