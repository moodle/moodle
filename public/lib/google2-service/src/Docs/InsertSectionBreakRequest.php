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

namespace Google\Service\Docs;

class InsertSectionBreakRequest extends \Google\Model
{
  /**
   * The section type is unspecified.
   */
  public const SECTION_TYPE_SECTION_TYPE_UNSPECIFIED = 'SECTION_TYPE_UNSPECIFIED';
  /**
   * The section starts immediately after the last paragraph of the previous
   * section.
   */
  public const SECTION_TYPE_CONTINUOUS = 'CONTINUOUS';
  /**
   * The section starts on the next page.
   */
  public const SECTION_TYPE_NEXT_PAGE = 'NEXT_PAGE';
  protected $endOfSegmentLocationType = EndOfSegmentLocation::class;
  protected $endOfSegmentLocationDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';
  /**
   * The type of section to insert.
   *
   * @var string
   */
  public $sectionType;

  /**
   * Inserts a newline and a section break at the end of the document body.
   * Section breaks cannot be inserted inside a footnote, header or footer.
   * Because section breaks can only be inserted inside the body, the segment ID
   * field must be empty.
   *
   * @param EndOfSegmentLocation $endOfSegmentLocation
   */
  public function setEndOfSegmentLocation(EndOfSegmentLocation $endOfSegmentLocation)
  {
    $this->endOfSegmentLocation = $endOfSegmentLocation;
  }
  /**
   * @return EndOfSegmentLocation
   */
  public function getEndOfSegmentLocation()
  {
    return $this->endOfSegmentLocation;
  }
  /**
   * Inserts a newline and a section break at a specific index in the document.
   * The section break must be inserted inside the bounds of an existing
   * Paragraph. For instance, it cannot be inserted at a table's start index
   * (i.e. between the table and its preceding paragraph). Section breaks cannot
   * be inserted inside a table, equation, footnote, header, or footer. Since
   * section breaks can only be inserted inside the body, the segment ID field
   * must be empty.
   *
   * @param Location $location
   */
  public function setLocation(Location $location)
  {
    $this->location = $location;
  }
  /**
   * @return Location
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The type of section to insert.
   *
   * Accepted values: SECTION_TYPE_UNSPECIFIED, CONTINUOUS, NEXT_PAGE
   *
   * @param self::SECTION_TYPE_* $sectionType
   */
  public function setSectionType($sectionType)
  {
    $this->sectionType = $sectionType;
  }
  /**
   * @return self::SECTION_TYPE_*
   */
  public function getSectionType()
  {
    return $this->sectionType;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertSectionBreakRequest::class, 'Google_Service_Docs_InsertSectionBreakRequest');
