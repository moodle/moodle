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

class Residence extends \Google\Model
{
  /**
   * True if the residence is the person's current residence; false if the
   * residence is a past residence.
   *
   * @var bool
   */
  public $current;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The address of the residence.
   *
   * @var string
   */
  public $value;

  /**
   * True if the residence is the person's current residence; false if the
   * residence is a past residence.
   *
   * @param bool $current
   */
  public function setCurrent($current)
  {
    $this->current = $current;
  }
  /**
   * @return bool
   */
  public function getCurrent()
  {
    return $this->current;
  }
  /**
   * Metadata about the residence.
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
   * The address of the residence.
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
class_alias(Residence::class, 'Google_Service_PeopleService_Residence');
