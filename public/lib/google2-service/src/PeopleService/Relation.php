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

class Relation extends \Google\Model
{
  /**
   * Output only. The type of the relation translated and formatted in the
   * viewer's account locale or the locale specified in the Accept-Language HTTP
   * header.
   *
   * @var string
   */
  public $formattedType;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The name of the other person this relation refers to.
   *
   * @var string
   */
  public $person;
  /**
   * The person's relation to the other person. The type can be custom or one of
   * these predefined values: * `spouse` * `child` * `mother` * `father` *
   * `parent` * `brother` * `sister` * `friend` * `relative` * `domesticPartner`
   * * `manager` * `assistant` * `referredBy` * `partner`
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The type of the relation translated and formatted in the
   * viewer's account locale or the locale specified in the Accept-Language HTTP
   * header.
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
   * Metadata about the relation.
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
   * The name of the other person this relation refers to.
   *
   * @param string $person
   */
  public function setPerson($person)
  {
    $this->person = $person;
  }
  /**
   * @return string
   */
  public function getPerson()
  {
    return $this->person;
  }
  /**
   * The person's relation to the other person. The type can be custom or one of
   * these predefined values: * `spouse` * `child` * `mother` * `father` *
   * `parent` * `brother` * `sister` * `friend` * `relative` * `domesticPartner`
   * * `manager` * `assistant` * `referredBy` * `partner`
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Relation::class, 'Google_Service_PeopleService_Relation');
