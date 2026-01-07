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

class ExternalAction extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const TYPE_EXTERNAL_ACTION_TYPE_UNSPECIFIED = 'EXTERNAL_ACTION_TYPE_UNSPECIFIED';
  /**
   * Redirect to Merchant Center where the merchant can request a review for
   * issue related to their product.
   */
  public const TYPE_REVIEW_PRODUCT_ISSUE_IN_MERCHANT_CENTER = 'REVIEW_PRODUCT_ISSUE_IN_MERCHANT_CENTER';
  /**
   * Redirect to Merchant Center where the merchant can request a review for
   * issue related to their account.
   */
  public const TYPE_REVIEW_ACCOUNT_ISSUE_IN_MERCHANT_CENTER = 'REVIEW_ACCOUNT_ISSUE_IN_MERCHANT_CENTER';
  /**
   * Redirect to the form in Help Center where the merchant can request a legal
   * appeal for the issue.
   */
  public const TYPE_LEGAL_APPEAL_IN_HELP_CENTER = 'LEGAL_APPEAL_IN_HELP_CENTER';
  /**
   * Redirect to Merchant Center where the merchant can perform identity
   * verification.
   */
  public const TYPE_VERIFY_IDENTITY_IN_MERCHANT_CENTER = 'VERIFY_IDENTITY_IN_MERCHANT_CENTER';
  /**
   * The type of external action.
   *
   * @var string
   */
  public $type;
  /**
   * URL to external system, for example Merchant Center, where the merchant can
   * perform the action.
   *
   * @var string
   */
  public $uri;

  /**
   * The type of external action.
   *
   * Accepted values: EXTERNAL_ACTION_TYPE_UNSPECIFIED,
   * REVIEW_PRODUCT_ISSUE_IN_MERCHANT_CENTER,
   * REVIEW_ACCOUNT_ISSUE_IN_MERCHANT_CENTER, LEGAL_APPEAL_IN_HELP_CENTER,
   * VERIFY_IDENTITY_IN_MERCHANT_CENTER
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * URL to external system, for example Merchant Center, where the merchant can
   * perform the action.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ExternalAction::class, 'Google_Service_ShoppingContent_ExternalAction');
