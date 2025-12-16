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

class AccountStatusItemLevelIssue extends \Google\Model
{
  /**
   * The attribute's name, if the issue is caused by a single attribute.
   *
   * @var string
   */
  public $attributeName;
  /**
   * The error code of the issue.
   *
   * @var string
   */
  public $code;
  /**
   * A short issue description in English.
   *
   * @var string
   */
  public $description;
  /**
   * A detailed issue description in English.
   *
   * @var string
   */
  public $detail;
  /**
   * The URL of a web page to help with resolving this issue.
   *
   * @var string
   */
  public $documentation;
  /**
   * Number of items with this issue.
   *
   * @var string
   */
  public $numItems;
  /**
   * Whether the issue can be resolved by the merchant.
   *
   * @var string
   */
  public $resolution;
  /**
   * How this issue affects serving of the offer.
   *
   * @var string
   */
  public $servability;

  /**
   * The attribute's name, if the issue is caused by a single attribute.
   *
   * @param string $attributeName
   */
  public function setAttributeName($attributeName)
  {
    $this->attributeName = $attributeName;
  }
  /**
   * @return string
   */
  public function getAttributeName()
  {
    return $this->attributeName;
  }
  /**
   * The error code of the issue.
   *
   * @param string $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return string
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * A short issue description in English.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * A detailed issue description in English.
   *
   * @param string $detail
   */
  public function setDetail($detail)
  {
    $this->detail = $detail;
  }
  /**
   * @return string
   */
  public function getDetail()
  {
    return $this->detail;
  }
  /**
   * The URL of a web page to help with resolving this issue.
   *
   * @param string $documentation
   */
  public function setDocumentation($documentation)
  {
    $this->documentation = $documentation;
  }
  /**
   * @return string
   */
  public function getDocumentation()
  {
    return $this->documentation;
  }
  /**
   * Number of items with this issue.
   *
   * @param string $numItems
   */
  public function setNumItems($numItems)
  {
    $this->numItems = $numItems;
  }
  /**
   * @return string
   */
  public function getNumItems()
  {
    return $this->numItems;
  }
  /**
   * Whether the issue can be resolved by the merchant.
   *
   * @param string $resolution
   */
  public function setResolution($resolution)
  {
    $this->resolution = $resolution;
  }
  /**
   * @return string
   */
  public function getResolution()
  {
    return $this->resolution;
  }
  /**
   * How this issue affects serving of the offer.
   *
   * @param string $servability
   */
  public function setServability($servability)
  {
    $this->servability = $servability;
  }
  /**
   * @return string
   */
  public function getServability()
  {
    return $this->servability;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountStatusItemLevelIssue::class, 'Google_Service_ShoppingContent_AccountStatusItemLevelIssue');
