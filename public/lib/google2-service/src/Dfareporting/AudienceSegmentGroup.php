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

namespace Google\Service\Dfareporting;

class AudienceSegmentGroup extends \Google\Collection
{
  protected $collection_key = 'audienceSegments';
  protected $audienceSegmentsType = AudienceSegment::class;
  protected $audienceSegmentsDataType = 'array';
  /**
   * ID of this audience segment group. This is a read-only, auto-generated
   * field.
   *
   * @var string
   */
  public $id;
  /**
   * Name of this audience segment group. This is a required field and must be
   * less than 65 characters long.
   *
   * @var string
   */
  public $name;

  /**
   * Audience segments assigned to this group. The number of segments must be
   * between 2 and 100.
   *
   * @param AudienceSegment[] $audienceSegments
   */
  public function setAudienceSegments($audienceSegments)
  {
    $this->audienceSegments = $audienceSegments;
  }
  /**
   * @return AudienceSegment[]
   */
  public function getAudienceSegments()
  {
    return $this->audienceSegments;
  }
  /**
   * ID of this audience segment group. This is a read-only, auto-generated
   * field.
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
   * Name of this audience segment group. This is a required field and must be
   * less than 65 characters long.
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
class_alias(AudienceSegmentGroup::class, 'Google_Service_Dfareporting_AudienceSegmentGroup');
