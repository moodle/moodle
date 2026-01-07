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

class InsertDateRequest extends \Google\Model
{
  protected $dateElementPropertiesType = DateElementProperties::class;
  protected $dateElementPropertiesDataType = '';
  protected $endOfSegmentLocationType = EndOfSegmentLocation::class;
  protected $endOfSegmentLocationDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';

  /**
   * The properties of the date to insert.
   *
   * @param DateElementProperties $dateElementProperties
   */
  public function setDateElementProperties(DateElementProperties $dateElementProperties)
  {
    $this->dateElementProperties = $dateElementProperties;
  }
  /**
   * @return DateElementProperties
   */
  public function getDateElementProperties()
  {
    return $this->dateElementProperties;
  }
  /**
   * Inserts the date at the end of the given header, footer or document body.
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
   * Inserts the date at a specific index in the document. The date must be
   * inserted inside the bounds of an existing Paragraph. For instance, it
   * cannot be inserted at a table's start index (i.e. between an existing table
   * and its preceding paragraph).
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
class_alias(InsertDateRequest::class, 'Google_Service_Docs_InsertDateRequest');
