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

class MethodQuota extends \Google\Model
{
  /**
   * Output only. The method name, for example `products.list`. Method name does
   * not contain version because quota can be shared between different API
   * versions of the same method.
   *
   * @var string
   */
  public $method;
  /**
   * Output only. The maximum number of calls allowed per day for the method.
   *
   * @var string
   */
  public $quotaLimit;
  /**
   * Output only. The maximum number of calls allowed per minute for the method.
   *
   * @var string
   */
  public $quotaMinuteLimit;
  /**
   * Output only. The current quota usage, meaning the number of calls already
   * made to the method per day. Usage is reset every day at 12 PM midday UTC.
   *
   * @var string
   */
  public $quotaUsage;

  /**
   * Output only. The method name, for example `products.list`. Method name does
   * not contain version because quota can be shared between different API
   * versions of the same method.
   *
   * @param string $method
   */
  public function setMethod($method)
  {
    $this->method = $method;
  }
  /**
   * @return string
   */
  public function getMethod()
  {
    return $this->method;
  }
  /**
   * Output only. The maximum number of calls allowed per day for the method.
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
   * Output only. The maximum number of calls allowed per minute for the method.
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
   * made to the method per day. Usage is reset every day at 12 PM midday UTC.
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
class_alias(MethodQuota::class, 'Google_Service_ShoppingContent_MethodQuota');
