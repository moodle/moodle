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

class AccountLabel extends \Google\Model
{
  /**
   * Unknown label type.
   */
  public const LABEL_TYPE_LABEL_TYPE_UNSPECIFIED = 'LABEL_TYPE_UNSPECIFIED';
  /**
   * Indicates that the label was created manually.
   */
  public const LABEL_TYPE_MANUAL = 'MANUAL';
  /**
   * Indicates that the label was created automatically by CSS Center.
   */
  public const LABEL_TYPE_AUTOMATIC = 'AUTOMATIC';
  /**
   * Immutable. The ID of account this label belongs to.
   *
   * @var string
   */
  public $accountId;
  /**
   * The description of this label.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The ID of the label.
   *
   * @var string
   */
  public $labelId;
  /**
   * Output only. The type of this label.
   *
   * @var string
   */
  public $labelType;
  /**
   * The display name of this label.
   *
   * @var string
   */
  public $name;

  /**
   * Immutable. The ID of account this label belongs to.
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
   * The description of this label.
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
   * Output only. The ID of the label.
   *
   * @param string $labelId
   */
  public function setLabelId($labelId)
  {
    $this->labelId = $labelId;
  }
  /**
   * @return string
   */
  public function getLabelId()
  {
    return $this->labelId;
  }
  /**
   * Output only. The type of this label.
   *
   * Accepted values: LABEL_TYPE_UNSPECIFIED, MANUAL, AUTOMATIC
   *
   * @param self::LABEL_TYPE_* $labelType
   */
  public function setLabelType($labelType)
  {
    $this->labelType = $labelType;
  }
  /**
   * @return self::LABEL_TYPE_*
   */
  public function getLabelType()
  {
    return $this->labelType;
  }
  /**
   * The display name of this label.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountLabel::class, 'Google_Service_ShoppingContent_AccountLabel');
