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

namespace Google\Service\Analytics;

class AdWordsAccount extends \Google\Model
{
  /**
   * True if auto-tagging is enabled on the Google Ads account. Read-only after
   * the insert operation.
   *
   * @var bool
   */
  public $autoTaggingEnabled;
  /**
   * Customer ID. This field is required when creating a Google Ads link.
   *
   * @var string
   */
  public $customerId;
  /**
   * Resource type for Google Ads account.
   *
   * @var string
   */
  public $kind;

  /**
   * True if auto-tagging is enabled on the Google Ads account. Read-only after
   * the insert operation.
   *
   * @param bool $autoTaggingEnabled
   */
  public function setAutoTaggingEnabled($autoTaggingEnabled)
  {
    $this->autoTaggingEnabled = $autoTaggingEnabled;
  }
  /**
   * @return bool
   */
  public function getAutoTaggingEnabled()
  {
    return $this->autoTaggingEnabled;
  }
  /**
   * Customer ID. This field is required when creating a Google Ads link.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Resource type for Google Ads account.
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
class_alias(AdWordsAccount::class, 'Google_Service_Analytics_AdWordsAccount');
