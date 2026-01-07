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

namespace Google\Service\CloudBillingBudget;

class GoogleCloudBillingBudgetsV1Budget extends \Google\Collection
{
  /**
   * Unspecified ownership scope, same as ALL_USERS.
   */
  public const OWNERSHIP_SCOPE_OWNERSHIP_SCOPE_UNSPECIFIED = 'OWNERSHIP_SCOPE_UNSPECIFIED';
  /**
   * Both billing account-level users and project-level users have full access
   * to the budget, if the users have the required IAM permissions.
   */
  public const OWNERSHIP_SCOPE_ALL_USERS = 'ALL_USERS';
  /**
   * Only billing account-level users have full access to the budget. Project-
   * level users have read-only access, even if they have the required IAM
   * permissions.
   */
  public const OWNERSHIP_SCOPE_BILLING_ACCOUNT = 'BILLING_ACCOUNT';
  protected $collection_key = 'thresholdRules';
  protected $amountType = GoogleCloudBillingBudgetsV1BudgetAmount::class;
  protected $amountDataType = '';
  protected $budgetFilterType = GoogleCloudBillingBudgetsV1Filter::class;
  protected $budgetFilterDataType = '';
  /**
   * User data for display name in UI. The name must be less than or equal to 60
   * characters.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional. Etag to validate that the object is unchanged for a read-modify-
   * write operation. An empty etag causes an update to overwrite other changes.
   *
   * @var string
   */
  public $etag;
  /**
   * Output only. Resource name of the budget. The resource name implies the
   * scope of a budget. Values are of the form
   * `billingAccounts/{billingAccountId}/budgets/{budgetId}`.
   *
   * @var string
   */
  public $name;
  protected $notificationsRuleType = GoogleCloudBillingBudgetsV1NotificationsRule::class;
  protected $notificationsRuleDataType = '';
  /**
   * @var string
   */
  public $ownershipScope;
  protected $thresholdRulesType = GoogleCloudBillingBudgetsV1ThresholdRule::class;
  protected $thresholdRulesDataType = 'array';

  /**
   * Required. Budgeted amount.
   *
   * @param GoogleCloudBillingBudgetsV1BudgetAmount $amount
   */
  public function setAmount(GoogleCloudBillingBudgetsV1BudgetAmount $amount)
  {
    $this->amount = $amount;
  }
  /**
   * @return GoogleCloudBillingBudgetsV1BudgetAmount
   */
  public function getAmount()
  {
    return $this->amount;
  }
  /**
   * Optional. Filters that define which resources are used to compute the
   * actual spend against the budget amount, such as projects, services, and the
   * budget's time period, as well as other filters.
   *
   * @param GoogleCloudBillingBudgetsV1Filter $budgetFilter
   */
  public function setBudgetFilter(GoogleCloudBillingBudgetsV1Filter $budgetFilter)
  {
    $this->budgetFilter = $budgetFilter;
  }
  /**
   * @return GoogleCloudBillingBudgetsV1Filter
   */
  public function getBudgetFilter()
  {
    return $this->budgetFilter;
  }
  /**
   * User data for display name in UI. The name must be less than or equal to 60
   * characters.
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
   * Optional. Etag to validate that the object is unchanged for a read-modify-
   * write operation. An empty etag causes an update to overwrite other changes.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Output only. Resource name of the budget. The resource name implies the
   * scope of a budget. Values are of the form
   * `billingAccounts/{billingAccountId}/budgets/{budgetId}`.
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
   * Optional. Rules to apply to notifications sent based on budget spend and
   * thresholds.
   *
   * @param GoogleCloudBillingBudgetsV1NotificationsRule $notificationsRule
   */
  public function setNotificationsRule(GoogleCloudBillingBudgetsV1NotificationsRule $notificationsRule)
  {
    $this->notificationsRule = $notificationsRule;
  }
  /**
   * @return GoogleCloudBillingBudgetsV1NotificationsRule
   */
  public function getNotificationsRule()
  {
    return $this->notificationsRule;
  }
  /**
   * @param self::OWNERSHIP_SCOPE_* $ownershipScope
   */
  public function setOwnershipScope($ownershipScope)
  {
    $this->ownershipScope = $ownershipScope;
  }
  /**
   * @return self::OWNERSHIP_SCOPE_*
   */
  public function getOwnershipScope()
  {
    return $this->ownershipScope;
  }
  /**
   * Optional. Rules that trigger alerts (notifications of thresholds being
   * crossed) when spend exceeds the specified percentages of the budget.
   * Optional for `pubsubTopic` notifications. Required if using email
   * notifications.
   *
   * @param GoogleCloudBillingBudgetsV1ThresholdRule[] $thresholdRules
   */
  public function setThresholdRules($thresholdRules)
  {
    $this->thresholdRules = $thresholdRules;
  }
  /**
   * @return GoogleCloudBillingBudgetsV1ThresholdRule[]
   */
  public function getThresholdRules()
  {
    return $this->thresholdRules;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBillingBudgetsV1Budget::class, 'Google_Service_CloudBillingBudget_GoogleCloudBillingBudgetsV1Budget');
