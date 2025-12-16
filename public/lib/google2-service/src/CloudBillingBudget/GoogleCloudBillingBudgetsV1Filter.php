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

class GoogleCloudBillingBudgetsV1Filter extends \Google\Collection
{
  /**
   * Calendar period is unset. This is the default if the budget is for a custom
   * time period (CustomPeriod).
   */
  public const CALENDAR_PERIOD_CALENDAR_PERIOD_UNSPECIFIED = 'CALENDAR_PERIOD_UNSPECIFIED';
  /**
   * A month. Month starts on the first day of each month, such as January 1,
   * February 1, March 1, and so on.
   */
  public const CALENDAR_PERIOD_MONTH = 'MONTH';
  /**
   * A quarter. Quarters start on dates January 1, April 1, July 1, and October
   * 1 of each year.
   */
  public const CALENDAR_PERIOD_QUARTER = 'QUARTER';
  /**
   * A year. Year starts on January 1.
   */
  public const CALENDAR_PERIOD_YEAR = 'YEAR';
  public const CREDIT_TYPES_TREATMENT_CREDIT_TYPES_TREATMENT_UNSPECIFIED = 'CREDIT_TYPES_TREATMENT_UNSPECIFIED';
  /**
   * All types of credit are subtracted from the gross cost to determine the
   * spend for threshold calculations.
   */
  public const CREDIT_TYPES_TREATMENT_INCLUDE_ALL_CREDITS = 'INCLUDE_ALL_CREDITS';
  /**
   * All types of credit are added to the net cost to determine the spend for
   * threshold calculations.
   */
  public const CREDIT_TYPES_TREATMENT_EXCLUDE_ALL_CREDITS = 'EXCLUDE_ALL_CREDITS';
  /**
   * [Credit types](https://cloud.google.com/billing/docs/how-to/export-data-
   * bigquery-tables#credits-type) specified in the credit_types field are
   * subtracted from the gross cost to determine the spend for threshold
   * calculations.
   */
  public const CREDIT_TYPES_TREATMENT_INCLUDE_SPECIFIED_CREDITS = 'INCLUDE_SPECIFIED_CREDITS';
  protected $collection_key = 'subaccounts';
  /**
   * Optional. Specifies to track usage for recurring calendar period. For
   * example, assume that CalendarPeriod.QUARTER is set. The budget tracks usage
   * from April 1 to June 30, when the current calendar month is April, May,
   * June. After that, it tracks usage from July 1 to September 30 when the
   * current calendar month is July, August, September, so on.
   *
   * @var string
   */
  public $calendarPeriod;
  /**
   * Optional. If Filter.credit_types_treatment is INCLUDE_SPECIFIED_CREDITS,
   * this is a list of credit types to be subtracted from gross cost to
   * determine the spend for threshold calculations. See [a list of acceptable
   * credit type values](https://cloud.google.com/billing/docs/how-to/export-
   * data-bigquery-tables#credits-type). If Filter.credit_types_treatment is
   * **not** INCLUDE_SPECIFIED_CREDITS, this field must be empty.
   *
   * @var string[]
   */
  public $creditTypes;
  /**
   * Optional. If not set, default behavior is `INCLUDE_ALL_CREDITS`.
   *
   * @var string
   */
  public $creditTypesTreatment;
  protected $customPeriodType = GoogleCloudBillingBudgetsV1CustomPeriod::class;
  protected $customPeriodDataType = '';
  /**
   * Optional. A single label and value pair specifying that usage from only
   * this set of labeled resources should be included in the budget. If omitted,
   * the report includes all labeled and unlabeled usage. An object containing a
   * single `"key": value` pair. Example: `{ "name": "wrench" }`. _Currently,
   * multiple entries or multiple values per entry are not allowed._
   *
   * @var array[]
   */
  public $labels;
  /**
   * Optional. A set of projects of the form `projects/{project}`, specifying
   * that usage from only this set of projects should be included in the budget.
   * If omitted, the report includes all usage for the billing account,
   * regardless of which project the usage occurred on.
   *
   * @var string[]
   */
  public $projects;
  /**
   * Optional. A set of folder and organization names of the form
   * `folders/{folderId}` or `organizations/{organizationId}`, specifying that
   * usage from only this set of folders and organizations should be included in
   * the budget. If omitted, the budget includes all usage that the billing
   * account pays for. If the folder or organization contains projects that are
   * paid for by a different Cloud Billing account, the budget *doesn't* apply
   * to those projects.
   *
   * @var string[]
   */
  public $resourceAncestors;
  /**
   * Optional. A set of services of the form `services/{service_id}`, specifying
   * that usage from only this set of services should be included in the budget.
   * If omitted, the report includes usage for all the services. The service
   * names are available through the Catalog API:
   * https://cloud.google.com/billing/v1/how-tos/catalog-api.
   *
   * @var string[]
   */
  public $services;
  /**
   * Optional. A set of subaccounts of the form `billingAccounts/{account_id}`,
   * specifying that usage from only this set of subaccounts should be included
   * in the budget. If a subaccount is set to the name of the parent account,
   * usage from the parent account is included. If the field is omitted, the
   * report includes usage from the parent account and all subaccounts, if they
   * exist.
   *
   * @var string[]
   */
  public $subaccounts;

  /**
   * Optional. Specifies to track usage for recurring calendar period. For
   * example, assume that CalendarPeriod.QUARTER is set. The budget tracks usage
   * from April 1 to June 30, when the current calendar month is April, May,
   * June. After that, it tracks usage from July 1 to September 30 when the
   * current calendar month is July, August, September, so on.
   *
   * Accepted values: CALENDAR_PERIOD_UNSPECIFIED, MONTH, QUARTER, YEAR
   *
   * @param self::CALENDAR_PERIOD_* $calendarPeriod
   */
  public function setCalendarPeriod($calendarPeriod)
  {
    $this->calendarPeriod = $calendarPeriod;
  }
  /**
   * @return self::CALENDAR_PERIOD_*
   */
  public function getCalendarPeriod()
  {
    return $this->calendarPeriod;
  }
  /**
   * Optional. If Filter.credit_types_treatment is INCLUDE_SPECIFIED_CREDITS,
   * this is a list of credit types to be subtracted from gross cost to
   * determine the spend for threshold calculations. See [a list of acceptable
   * credit type values](https://cloud.google.com/billing/docs/how-to/export-
   * data-bigquery-tables#credits-type). If Filter.credit_types_treatment is
   * **not** INCLUDE_SPECIFIED_CREDITS, this field must be empty.
   *
   * @param string[] $creditTypes
   */
  public function setCreditTypes($creditTypes)
  {
    $this->creditTypes = $creditTypes;
  }
  /**
   * @return string[]
   */
  public function getCreditTypes()
  {
    return $this->creditTypes;
  }
  /**
   * Optional. If not set, default behavior is `INCLUDE_ALL_CREDITS`.
   *
   * Accepted values: CREDIT_TYPES_TREATMENT_UNSPECIFIED, INCLUDE_ALL_CREDITS,
   * EXCLUDE_ALL_CREDITS, INCLUDE_SPECIFIED_CREDITS
   *
   * @param self::CREDIT_TYPES_TREATMENT_* $creditTypesTreatment
   */
  public function setCreditTypesTreatment($creditTypesTreatment)
  {
    $this->creditTypesTreatment = $creditTypesTreatment;
  }
  /**
   * @return self::CREDIT_TYPES_TREATMENT_*
   */
  public function getCreditTypesTreatment()
  {
    return $this->creditTypesTreatment;
  }
  /**
   * Optional. Specifies to track usage from any start date (required) to any
   * end date (optional). This time period is static, it does not recur.
   *
   * @param GoogleCloudBillingBudgetsV1CustomPeriod $customPeriod
   */
  public function setCustomPeriod(GoogleCloudBillingBudgetsV1CustomPeriod $customPeriod)
  {
    $this->customPeriod = $customPeriod;
  }
  /**
   * @return GoogleCloudBillingBudgetsV1CustomPeriod
   */
  public function getCustomPeriod()
  {
    return $this->customPeriod;
  }
  /**
   * Optional. A single label and value pair specifying that usage from only
   * this set of labeled resources should be included in the budget. If omitted,
   * the report includes all labeled and unlabeled usage. An object containing a
   * single `"key": value` pair. Example: `{ "name": "wrench" }`. _Currently,
   * multiple entries or multiple values per entry are not allowed._
   *
   * @param array[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return array[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Optional. A set of projects of the form `projects/{project}`, specifying
   * that usage from only this set of projects should be included in the budget.
   * If omitted, the report includes all usage for the billing account,
   * regardless of which project the usage occurred on.
   *
   * @param string[] $projects
   */
  public function setProjects($projects)
  {
    $this->projects = $projects;
  }
  /**
   * @return string[]
   */
  public function getProjects()
  {
    return $this->projects;
  }
  /**
   * Optional. A set of folder and organization names of the form
   * `folders/{folderId}` or `organizations/{organizationId}`, specifying that
   * usage from only this set of folders and organizations should be included in
   * the budget. If omitted, the budget includes all usage that the billing
   * account pays for. If the folder or organization contains projects that are
   * paid for by a different Cloud Billing account, the budget *doesn't* apply
   * to those projects.
   *
   * @param string[] $resourceAncestors
   */
  public function setResourceAncestors($resourceAncestors)
  {
    $this->resourceAncestors = $resourceAncestors;
  }
  /**
   * @return string[]
   */
  public function getResourceAncestors()
  {
    return $this->resourceAncestors;
  }
  /**
   * Optional. A set of services of the form `services/{service_id}`, specifying
   * that usage from only this set of services should be included in the budget.
   * If omitted, the report includes usage for all the services. The service
   * names are available through the Catalog API:
   * https://cloud.google.com/billing/v1/how-tos/catalog-api.
   *
   * @param string[] $services
   */
  public function setServices($services)
  {
    $this->services = $services;
  }
  /**
   * @return string[]
   */
  public function getServices()
  {
    return $this->services;
  }
  /**
   * Optional. A set of subaccounts of the form `billingAccounts/{account_id}`,
   * specifying that usage from only this set of subaccounts should be included
   * in the budget. If a subaccount is set to the name of the parent account,
   * usage from the parent account is included. If the field is omitted, the
   * report includes usage from the parent account and all subaccounts, if they
   * exist.
   *
   * @param string[] $subaccounts
   */
  public function setSubaccounts($subaccounts)
  {
    $this->subaccounts = $subaccounts;
  }
  /**
   * @return string[]
   */
  public function getSubaccounts()
  {
    return $this->subaccounts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudBillingBudgetsV1Filter::class, 'Google_Service_CloudBillingBudget_GoogleCloudBillingBudgetsV1Filter');
