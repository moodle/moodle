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

namespace Google\Service\Slides;

class CreateSlideRequest extends \Google\Collection
{
  protected $collection_key = 'placeholderIdMappings';
  /**
   * The optional zero-based index indicating where to insert the slides. If you
   * don't specify an index, the slide is created at the end.
   *
   * @var int
   */
  public $insertionIndex;
  /**
   * A user-supplied object ID. If you specify an ID, it must be unique among
   * all pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The ID length must be between 5 and 50
   * characters, inclusive. If you don't specify an ID, a unique one is
   * generated.
   *
   * @var string
   */
  public $objectId;
  protected $placeholderIdMappingsType = LayoutPlaceholderIdMapping::class;
  protected $placeholderIdMappingsDataType = 'array';
  protected $slideLayoutReferenceType = LayoutReference::class;
  protected $slideLayoutReferenceDataType = '';

  /**
   * The optional zero-based index indicating where to insert the slides. If you
   * don't specify an index, the slide is created at the end.
   *
   * @param int $insertionIndex
   */
  public function setInsertionIndex($insertionIndex)
  {
    $this->insertionIndex = $insertionIndex;
  }
  /**
   * @return int
   */
  public function getInsertionIndex()
  {
    return $this->insertionIndex;
  }
  /**
   * A user-supplied object ID. If you specify an ID, it must be unique among
   * all pages and page elements in the presentation. The ID must start with an
   * alphanumeric character or an underscore (matches regex `[a-zA-Z0-9_]`);
   * remaining characters may include those as well as a hyphen or colon
   * (matches regex `[a-zA-Z0-9_-:]`). The ID length must be between 5 and 50
   * characters, inclusive. If you don't specify an ID, a unique one is
   * generated.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * An optional list of object ID mappings from the placeholder(s) on the
   * layout to the placeholders that are created on the slide from the specified
   * layout. Can only be used when `slide_layout_reference` is specified.
   *
   * @param LayoutPlaceholderIdMapping[] $placeholderIdMappings
   */
  public function setPlaceholderIdMappings($placeholderIdMappings)
  {
    $this->placeholderIdMappings = $placeholderIdMappings;
  }
  /**
   * @return LayoutPlaceholderIdMapping[]
   */
  public function getPlaceholderIdMappings()
  {
    return $this->placeholderIdMappings;
  }
  /**
   * Layout reference of the slide to be inserted, based on the *current
   * master*, which is one of the following: - The master of the previous slide
   * index. - The master of the first slide, if the insertion_index is zero. -
   * The first master in the presentation, if there are no slides. If the
   * LayoutReference is not found in the current master, a 400 bad request error
   * is returned. If you don't specify a layout reference, the slide uses the
   * predefined `BLANK` layout.
   *
   * @param LayoutReference $slideLayoutReference
   */
  public function setSlideLayoutReference(LayoutReference $slideLayoutReference)
  {
    $this->slideLayoutReference = $slideLayoutReference;
  }
  /**
   * @return LayoutReference
   */
  public function getSlideLayoutReference()
  {
    return $this->slideLayoutReference;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreateSlideRequest::class, 'Google_Service_Slides_CreateSlideRequest');
