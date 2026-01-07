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

class FieldMetadata extends \Google\Model
{
  /**
   * Output only. True if the field is the primary field for all sources in the
   * person. Each person will have at most one field with `primary` set to true.
   *
   * @var bool
   */
  public $primary;
  protected $sourceType = Source::class;
  protected $sourceDataType = '';
  /**
   * True if the field is the primary field for the source. Each source must
   * have at most one field with `source_primary` set to true.
   *
   * @var bool
   */
  public $sourcePrimary;
  /**
   * Output only. True if the field is verified; false if the field is
   * unverified. A verified field is typically a name, email address, phone
   * number, or website that has been confirmed to be owned by the person.
   *
   * @var bool
   */
  public $verified;

  /**
   * Output only. True if the field is the primary field for all sources in the
   * person. Each person will have at most one field with `primary` set to true.
   *
   * @param bool $primary
   */
  public function setPrimary($primary)
  {
    $this->primary = $primary;
  }
  /**
   * @return bool
   */
  public function getPrimary()
  {
    return $this->primary;
  }
  /**
   * The source of the field.
   *
   * @param Source $source
   */
  public function setSource(Source $source)
  {
    $this->source = $source;
  }
  /**
   * @return Source
   */
  public function getSource()
  {
    return $this->source;
  }
  /**
   * True if the field is the primary field for the source. Each source must
   * have at most one field with `source_primary` set to true.
   *
   * @param bool $sourcePrimary
   */
  public function setSourcePrimary($sourcePrimary)
  {
    $this->sourcePrimary = $sourcePrimary;
  }
  /**
   * @return bool
   */
  public function getSourcePrimary()
  {
    return $this->sourcePrimary;
  }
  /**
   * Output only. True if the field is verified; false if the field is
   * unverified. A verified field is typically a name, email address, phone
   * number, or website that has been confirmed to be owned by the person.
   *
   * @param bool $verified
   */
  public function setVerified($verified)
  {
    $this->verified = $verified;
  }
  /**
   * @return bool
   */
  public function getVerified()
  {
    return $this->verified;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FieldMetadata::class, 'Google_Service_PeopleService_FieldMetadata');
