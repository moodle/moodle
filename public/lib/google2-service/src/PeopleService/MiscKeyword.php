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

namespace Google\Service\PeopleService;

class MiscKeyword extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Outlook field for billing information.
   */
  public const TYPE_OUTLOOK_BILLING_INFORMATION = 'OUTLOOK_BILLING_INFORMATION';
  /**
   * Outlook field for directory server.
   */
  public const TYPE_OUTLOOK_DIRECTORY_SERVER = 'OUTLOOK_DIRECTORY_SERVER';
  /**
   * Outlook field for keyword.
   */
  public const TYPE_OUTLOOK_KEYWORD = 'OUTLOOK_KEYWORD';
  /**
   * Outlook field for mileage.
   */
  public const TYPE_OUTLOOK_MILEAGE = 'OUTLOOK_MILEAGE';
  /**
   * Outlook field for priority.
   */
  public const TYPE_OUTLOOK_PRIORITY = 'OUTLOOK_PRIORITY';
  /**
   * Outlook field for sensitivity.
   */
  public const TYPE_OUTLOOK_SENSITIVITY = 'OUTLOOK_SENSITIVITY';
  /**
   * Outlook field for subject.
   */
  public const TYPE_OUTLOOK_SUBJECT = 'OUTLOOK_SUBJECT';
  /**
   * Outlook field for user.
   */
  public const TYPE_OUTLOOK_USER = 'OUTLOOK_USER';
  /**
   * Home.
   */
  public const TYPE_HOME = 'HOME';
  /**
   * Work.
   */
  public const TYPE_WORK = 'WORK';
  /**
   * Other.
   */
  public const TYPE_OTHER = 'OTHER';
  /**
   * Output only. The type of the miscellaneous keyword translated and formatted
   * in the viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @var string
   */
  public $formattedType;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The miscellaneous keyword type.
   *
   * @var string
   */
  public $type;
  /**
   * The value of the miscellaneous keyword.
   *
   * @var string
   */
  public $value;

  /**
   * Output only. The type of the miscellaneous keyword translated and formatted
   * in the viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @param string $formattedType
   */
  public function setFormattedType($formattedType)
  {
    $this->formattedType = $formattedType;
  }
  /**
   * @return string
   */
  public function getFormattedType()
  {
    return $this->formattedType;
  }
  /**
   * Metadata about the miscellaneous keyword.
   *
   * @param FieldMetadata $metadata
   */
  public function setMetadata(FieldMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return FieldMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The miscellaneous keyword type.
   *
   * Accepted values: TYPE_UNSPECIFIED, OUTLOOK_BILLING_INFORMATION,
   * OUTLOOK_DIRECTORY_SERVER, OUTLOOK_KEYWORD, OUTLOOK_MILEAGE,
   * OUTLOOK_PRIORITY, OUTLOOK_SENSITIVITY, OUTLOOK_SUBJECT, OUTLOOK_USER, HOME,
   * WORK, OTHER
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
   * The value of the miscellaneous keyword.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MiscKeyword::class, 'Google_Service_PeopleService_MiscKeyword');
