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

class Nickname extends \Google\Model
{
  /**
   * Generic nickname.
   */
  public const TYPE_DEFAULT = 'DEFAULT';
  /**
   * Maiden name or birth family name. Used when the person's family name has
   * changed as a result of marriage.
   *
   * @deprecated
   */
  public const TYPE_MAIDEN_NAME = 'MAIDEN_NAME';
  /**
   * Initials.
   *
   * @deprecated
   */
  public const TYPE_INITIALS = 'INITIALS';
  /**
   * Google+ profile nickname.
   *
   * @deprecated
   */
  public const TYPE_GPLUS = 'GPLUS';
  /**
   * A professional affiliation or other name; for example, `Dr. Smith.`
   *
   * @deprecated
   */
  public const TYPE_OTHER_NAME = 'OTHER_NAME';
  /**
   * Alternate name person is known by.
   */
  public const TYPE_ALTERNATE_NAME = 'ALTERNATE_NAME';
  /**
   * A shorter version of the person's name.
   *
   * @deprecated
   */
  public const TYPE_SHORT_NAME = 'SHORT_NAME';
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The type of the nickname.
   *
   * @var string
   */
  public $type;
  /**
   * The nickname.
   *
   * @var string
   */
  public $value;

  /**
   * Metadata about the nickname.
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
   * The type of the nickname.
   *
   * Accepted values: DEFAULT, MAIDEN_NAME, INITIALS, GPLUS, OTHER_NAME,
   * ALTERNATE_NAME, SHORT_NAME
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
   * The nickname.
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
class_alias(Nickname::class, 'Google_Service_PeopleService_Nickname');
