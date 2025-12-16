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

class BuiltInSimpleAction extends \Google\Model
{
  /**
   * Default value. Will never be provided by the API.
   */
  public const TYPE_BUILT_IN_SIMPLE_ACTION_TYPE_UNSPECIFIED = 'BUILT_IN_SIMPLE_ACTION_TYPE_UNSPECIFIED';
  /**
   * Redirect merchant to the part of your application where they can verify
   * their phone.
   */
  public const TYPE_VERIFY_PHONE = 'VERIFY_PHONE';
  /**
   * Redirect merchant to the part of your application where they can claim
   * their website.
   */
  public const TYPE_CLAIM_WEBSITE = 'CLAIM_WEBSITE';
  /**
   * Redirect merchant to the part of your application where they can add
   * products.
   */
  public const TYPE_ADD_PRODUCTS = 'ADD_PRODUCTS';
  /**
   * Open a form where the merchant can edit their contact information.
   */
  public const TYPE_ADD_CONTACT_INFO = 'ADD_CONTACT_INFO';
  /**
   * Redirect merchant to the part of your application where they can link ads
   * account.
   */
  public const TYPE_LINK_ADS_ACCOUNT = 'LINK_ADS_ACCOUNT';
  /**
   * Open a form where the merchant can add their business registration number.
   */
  public const TYPE_ADD_BUSINESS_REGISTRATION_NUMBER = 'ADD_BUSINESS_REGISTRATION_NUMBER';
  /**
   * Open a form where the merchant can edit an attribute. The attribute that
   * needs to be updated is specified in attribute_code field of the action.
   */
  public const TYPE_EDIT_ITEM_ATTRIBUTE = 'EDIT_ITEM_ATTRIBUTE';
  /**
   * Redirect merchant from the product issues to the diagnostic page with their
   * account issues in your application. This action will be returned only for
   * product issues that are caused by an account issue and thus merchant should
   * resolve the problem on the account level.
   */
  public const TYPE_FIX_ACCOUNT_ISSUE = 'FIX_ACCOUNT_ISSUE';
  /**
   * Show additional content to the merchant. This action will be used for
   * example to deliver a justification from national authority.
   */
  public const TYPE_SHOW_ADDITIONAL_CONTENT = 'SHOW_ADDITIONAL_CONTENT';
  protected $additionalContentType = BuiltInSimpleActionAdditionalContent::class;
  protected $additionalContentDataType = '';
  /**
   * The attribute that needs to be updated. Present when the type is
   * `EDIT_ITEM_ATTRIBUTE`. This field contains a code for attribute,
   * represented in snake_case. You can find a list of product's attributes,
   * with their codes
   * [here](https://support.google.com/merchants/answer/7052112).
   *
   * @var string
   */
  public $attributeCode;
  /**
   * The type of action that represents a functionality that is expected to be
   * available in third-party application.
   *
   * @var string
   */
  public $type;

  /**
   * Long text from an external source that should be available to the merchant.
   * Present when the type is `SHOW_ADDITIONAL_CONTENT`.
   *
   * @param BuiltInSimpleActionAdditionalContent $additionalContent
   */
  public function setAdditionalContent(BuiltInSimpleActionAdditionalContent $additionalContent)
  {
    $this->additionalContent = $additionalContent;
  }
  /**
   * @return BuiltInSimpleActionAdditionalContent
   */
  public function getAdditionalContent()
  {
    return $this->additionalContent;
  }
  /**
   * The attribute that needs to be updated. Present when the type is
   * `EDIT_ITEM_ATTRIBUTE`. This field contains a code for attribute,
   * represented in snake_case. You can find a list of product's attributes,
   * with their codes
   * [here](https://support.google.com/merchants/answer/7052112).
   *
   * @param string $attributeCode
   */
  public function setAttributeCode($attributeCode)
  {
    $this->attributeCode = $attributeCode;
  }
  /**
   * @return string
   */
  public function getAttributeCode()
  {
    return $this->attributeCode;
  }
  /**
   * The type of action that represents a functionality that is expected to be
   * available in third-party application.
   *
   * Accepted values: BUILT_IN_SIMPLE_ACTION_TYPE_UNSPECIFIED, VERIFY_PHONE,
   * CLAIM_WEBSITE, ADD_PRODUCTS, ADD_CONTACT_INFO, LINK_ADS_ACCOUNT,
   * ADD_BUSINESS_REGISTRATION_NUMBER, EDIT_ITEM_ATTRIBUTE, FIX_ACCOUNT_ISSUE,
   * SHOW_ADDITIONAL_CONTENT
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(BuiltInSimpleAction::class, 'Google_Service_ShoppingContent_BuiltInSimpleAction');
