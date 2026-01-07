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

class InsertInlineImageRequest extends \Google\Model
{
  protected $endOfSegmentLocationType = EndOfSegmentLocation::class;
  protected $endOfSegmentLocationDataType = '';
  protected $locationType = Location::class;
  protected $locationDataType = '';
  protected $objectSizeType = Size::class;
  protected $objectSizeDataType = '';
  /**
   * The image URI. The image is fetched once at insertion time and a copy is
   * stored for display inside the document. Images must be less than 50MB in
   * size, cannot exceed 25 megapixels, and must be in one of PNG, JPEG, or GIF
   * format. The provided URI must be publicly accessible and at most 2 kB in
   * length. The URI itself is saved with the image, and exposed via the
   * ImageProperties.content_uri field.
   *
   * @var string
   */
  public $uri;

  /**
   * Inserts the text at the end of a header, footer or the document body.
   * Inline images cannot be inserted inside a footnote.
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
   * Inserts the image at a specific index in the document. The image must be
   * inserted inside the bounds of an existing Paragraph. For instance, it
   * cannot be inserted at a table's start index (i.e. between the table and its
   * preceding paragraph). Inline images cannot be inserted inside a footnote or
   * equation.
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
   * The size that the image should appear as in the document. This property is
   * optional and the final size of the image in the document is determined by
   * the following rules: * If neither width nor height is specified, then a
   * default size of the image is calculated based on its resolution. * If one
   * dimension is specified then the other dimension is calculated to preserve
   * the aspect ratio of the image. * If both width and height are specified,
   * the image is scaled to fit within the provided dimensions while maintaining
   * its aspect ratio.
   *
   * @param Size $objectSize
   */
  public function setObjectSize(Size $objectSize)
  {
    $this->objectSize = $objectSize;
  }
  /**
   * @return Size
   */
  public function getObjectSize()
  {
    return $this->objectSize;
  }
  /**
   * The image URI. The image is fetched once at insertion time and a copy is
   * stored for display inside the document. Images must be less than 50MB in
   * size, cannot exceed 25 megapixels, and must be in one of PNG, JPEG, or GIF
   * format. The provided URI must be publicly accessible and at most 2 kB in
   * length. The URI itself is saved with the image, and exposed via the
   * ImageProperties.content_uri field.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(InsertInlineImageRequest::class, 'Google_Service_Docs_InsertInlineImageRequest');
