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

class AccountTax extends \Google\Collection
{
  protected $collection_key = 'rules';
  /**
   * Required. The ID of the account to which these account tax settings belong.
   *
   * @var string
   */
  public $accountId;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#accountTax`".
   *
   * @var string
   */
  public $kind;
  protected $rulesType = AccountTaxTaxRule::class;
  protected $rulesDataType = 'array';

  /**
   * Required. The ID of the account to which these account tax settings belong.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "`content#accountTax`".
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
   * Tax rules. Updating the tax rules will enable "US" taxes (not reversible).
   * Defining no rules is equivalent to not charging tax at all.
   *
   * @param AccountTaxTaxRule[] $rules
   */
  public function setRules($rules)
  {
    $this->rules = $rules;
  }
  /**
   * @return AccountTaxTaxRule[]
   */
  public function getRules()
  {
    return $this->rules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountTax::class, 'Google_Service_ShoppingContent_AccountTax');
