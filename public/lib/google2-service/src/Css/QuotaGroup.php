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

class QuotaGroup extends \Google\Collection
{
  protected $collection_key = 'methodDetails';
  protected $methodDetailsType = MethodDetails::class;
  protected $methodDetailsDataType = 'array';
  /**
   * Identifier. The resource name of the quota group. Format:
   * accounts/{account}/quotas/{group} Example: `accounts/12345678/quotas/css-
   * products-insert` Note: The {group} part is not guaranteed to follow a
   * specific pattern.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The maximum number of calls allowed per day for the group.
   *
   * @var string
   */
  public $quotaLimit;
  /**
   * Output only. The maximum number of calls allowed per minute for the group.
   *
   * @var string
   */
  public $quotaMinuteLimit;
  /**
   * Output only. The current quota usage, meaning the number of calls already
   * made on a given day to the methods in the group. The daily quota limits
   * reset at at 12:00 PM midday UTC.
   *
   * @var string
   */
  public $quotaUsage;

  /**
   * Output only. List of all methods group quota applies to.
   *
   * @param MethodDetails[] $methodDetails
   */
  public function setMethodDetails($methodDetails)
  {
    $this->methodDetails = $methodDetails;
  }
  /**
   * @return MethodDetails[]
   */
  public function getMethodDetails()
  {
    return $this->methodDetails;
  }
  /**
   * Identifier. The resource name of the quota group. Format:
   * accounts/{account}/quotas/{group} Example: `accounts/12345678/quotas/css-
   * products-insert` Note: The {group} part is not guaranteed to follow a
   * specific pattern.
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
   * Output only. The maximum number of calls allowed per day for the group.
   *
   * @param string $quotaLimit
   */
  public function setQuotaLimit($quotaLimit)
  {
    $this->quotaLimit = $quotaLimit;
  }
  /**
   * @return string
   */
  public function getQuotaLimit()
  {
    return $this->quotaLimit;
  }
  /**
   * Output only. The maximum number of calls allowed per minute for the group.
   *
   * @param string $quotaMinuteLimit
   */
  public function setQuotaMinuteLimit($quotaMinuteLimit)
  {
    $this->quotaMinuteLimit = $quotaMinuteLimit;
  }
  /**
   * @return string
   */
  public function getQuotaMinuteLimit()
  {
    return $this->quotaMinuteLimit;
  }
  /**
   * Output only. The current quota usage, meaning the number of calls already
   * made on a given day to the methods in the group. The daily quota limits
   * reset at at 12:00 PM midday UTC.
   *
   * @param string $quotaUsage
   */
  public function setQuotaUsage($quotaUsage)
  {
    $this->quotaUsage = $quotaUsage;
  }
  /**
   * @return string
   */
  public function getQuotaUsage()
  {
    return $this->quotaUsage;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QuotaGroup::class, 'Google_Service_Css_QuotaGroup');
