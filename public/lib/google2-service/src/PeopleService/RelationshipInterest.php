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

class RelationshipInterest extends \Google\Model
{
  /**
   * Output only. The value of the relationship interest translated and
   * formatted in the viewer's account locale or the locale specified in the
   * Accept-Language HTTP header.
   *
   * @var string
   */
  public $formattedValue;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The kind of relationship the person is looking for. The value can be custom
   * or one of these predefined values: * `friend` * `date` * `relationship` *
   * `networking`
   *
   * @var string
   */
  public $value;

  /**
   * Output only. The value of the relationship interest translated and
   * formatted in the viewer's account locale or the locale specified in the
   * Accept-Language HTTP header.
   *
   * @param string $formattedValue
   */
  public function setFormattedValue($formattedValue)
  {
    $this->formattedValue = $formattedValue;
  }
  /**
   * @return string
   */
  public function getFormattedValue()
  {
    return $this->formattedValue;
  }
  /**
   * Metadata about the relationship interest.
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
   * The kind of relationship the person is looking for. The value can be custom
   * or one of these predefined values: * `friend` * `date` * `relationship` *
   * `networking`
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
class_alias(RelationshipInterest::class, 'Google_Service_PeopleService_RelationshipInterest');
