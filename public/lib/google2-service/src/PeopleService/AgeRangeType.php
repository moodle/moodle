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

class AgeRangeType extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const AGE_RANGE_AGE_RANGE_UNSPECIFIED = 'AGE_RANGE_UNSPECIFIED';
  /**
   * Younger than eighteen.
   */
  public const AGE_RANGE_LESS_THAN_EIGHTEEN = 'LESS_THAN_EIGHTEEN';
  /**
   * Between eighteen and twenty.
   */
  public const AGE_RANGE_EIGHTEEN_TO_TWENTY = 'EIGHTEEN_TO_TWENTY';
  /**
   * Twenty-one and older.
   */
  public const AGE_RANGE_TWENTY_ONE_OR_OLDER = 'TWENTY_ONE_OR_OLDER';
  /**
   * The age range.
   *
   * @var string
   */
  public $ageRange;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';

  /**
   * The age range.
   *
   * Accepted values: AGE_RANGE_UNSPECIFIED, LESS_THAN_EIGHTEEN,
   * EIGHTEEN_TO_TWENTY, TWENTY_ONE_OR_OLDER
   *
   * @param self::AGE_RANGE_* $ageRange
   */
  public function setAgeRange($ageRange)
  {
    $this->ageRange = $ageRange;
  }
  /**
   * @return self::AGE_RANGE_*
   */
  public function getAgeRange()
  {
    return $this->ageRange;
  }
  /**
   * Metadata about the age range.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AgeRangeType::class, 'Google_Service_PeopleService_AgeRangeType');
