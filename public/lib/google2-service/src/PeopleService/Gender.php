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

class Gender extends \Google\Model
{
  /**
   * Free form text field for pronouns that should be used to address the
   * person. Common values are: * `he`/`him` * `she`/`her` * `they`/`them`
   *
   * @var string
   */
  public $addressMeAs;
  /**
   * Output only. The value of the gender translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   * Unspecified or custom value are not localized.
   *
   * @var string
   */
  public $formattedValue;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The gender for the person. The gender can be custom or one of these
   * predefined values: * `male` * `female` * `unspecified`
   *
   * @var string
   */
  public $value;

  /**
   * Free form text field for pronouns that should be used to address the
   * person. Common values are: * `he`/`him` * `she`/`her` * `they`/`them`
   *
   * @param string $addressMeAs
   */
  public function setAddressMeAs($addressMeAs)
  {
    $this->addressMeAs = $addressMeAs;
  }
  /**
   * @return string
   */
  public function getAddressMeAs()
  {
    return $this->addressMeAs;
  }
  /**
   * Output only. The value of the gender translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   * Unspecified or custom value are not localized.
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
   * Metadata about the gender.
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
   * The gender for the person. The gender can be custom or one of these
   * predefined values: * `male` * `female` * `unspecified`
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
class_alias(Gender::class, 'Google_Service_PeopleService_Gender');
