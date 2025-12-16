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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ResourcesLabel extends \Google\Model
{
  /**
   * Not specified.
   */
  public const STATUS_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * Used for return value only. Represents value unknown in this version.
   */
  public const STATUS_UNKNOWN = 'UNKNOWN';
  /**
   * Label is enabled.
   */
  public const STATUS_ENABLED = 'ENABLED';
  /**
   * Label is removed.
   */
  public const STATUS_REMOVED = 'REMOVED';
  /**
   * Output only. ID of the label. Read only.
   *
   * @var string
   */
  public $id;
  /**
   * The name of the label. This field is required and should not be empty when
   * creating a new label. The length of this string should be between 1 and 80,
   * inclusive.
   *
   * @var string
   */
  public $name;
  /**
   * Immutable. Name of the resource. Label resource names have the form:
   * `customers/{owner_customer_id}/labels/{label_id}`
   *
   * @var string
   */
  public $resourceName;
  /**
   * Output only. Status of the label. Read only.
   *
   * @var string
   */
  public $status;
  protected $textLabelType = GoogleAdsSearchads360V0CommonTextLabel::class;
  protected $textLabelDataType = '';

  /**
   * Output only. ID of the label. Read only.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * The name of the label. This field is required and should not be empty when
   * creating a new label. The length of this string should be between 1 and 80,
   * inclusive.
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
   * Immutable. Name of the resource. Label resource names have the form:
   * `customers/{owner_customer_id}/labels/{label_id}`
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. Status of the label. Read only.
   *
   * Accepted values: UNSPECIFIED, UNKNOWN, ENABLED, REMOVED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * A type of label displaying text on a colored background.
   *
   * @param GoogleAdsSearchads360V0CommonTextLabel $textLabel
   */
  public function setTextLabel(GoogleAdsSearchads360V0CommonTextLabel $textLabel)
  {
    $this->textLabel = $textLabel;
  }
  /**
   * @return GoogleAdsSearchads360V0CommonTextLabel
   */
  public function getTextLabel()
  {
    return $this->textLabel;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ResourcesLabel::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ResourcesLabel');
