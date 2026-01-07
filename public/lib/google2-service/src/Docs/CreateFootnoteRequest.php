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

class CreateFootnoteRequest extends \Google\Model
{
  protected $endOfSegmentLocationType = EndOfSegmentLocation::class;
  protected $endOfSegmentLocationDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';

  /**
   * Inserts the footnote reference at the end of the document body. Footnote
   * references cannot be inserted inside a header, footer or footnote. Since
   * footnote references can only be inserted in the body, the segment ID field
   * must be empty.
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
   * Inserts the footnote reference at a specific index in the document. The
   * footnote reference must be inserted inside the bounds of an existing
   * Paragraph. For instance, it cannot be inserted at a table's start index
   * (i.e. between the table and its preceding paragraph). Footnote references
   * cannot be inserted inside an equation, header, footer or footnote. Since
   * footnote references can only be inserted in the body, the segment ID field
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateFootnoteRequest::class, 'Google_Service_Docs_CreateFootnoteRequest');
