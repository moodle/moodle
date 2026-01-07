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

class CreateHeaderRequest extends \Google\Model
{
  /**
   * The header/footer type is unspecified.
   */
  public const TYPE_HEADER_FOOTER_TYPE_UNSPECIFIED = 'HEADER_FOOTER_TYPE_UNSPECIFIED';
  /**
   * A default header/footer.
   */
  public const TYPE_DEFAULT = 'DEFAULT';
  protected $sectionBreakLocationType = Location::class;
  protected $sectionBreakLocationDataType = '';
  /**
   * The type of header to create.
   *
   * @var string
   */
  public $type;

  /**
   * The location of the SectionBreak which begins the section this header
   * should belong to. If `section_break_location' is unset or if it refers to
   * the first section break in the document body, the header applies to the
   * DocumentStyle
   *
   * @param Location $sectionBreakLocation
   */
  public function setSectionBreakLocation(Location $sectionBreakLocation)
  {
    $this->sectionBreakLocation = $sectionBreakLocation;
  }
  /**
   * @return Location
   */
  public function getSectionBreakLocation()
  {
    return $this->sectionBreakLocation;
  }
  /**
   * The type of header to create.
   *
   * Accepted values: HEADER_FOOTER_TYPE_UNSPECIFIED, DEFAULT
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateHeaderRequest::class, 'Google_Service_Docs_CreateHeaderRequest');
