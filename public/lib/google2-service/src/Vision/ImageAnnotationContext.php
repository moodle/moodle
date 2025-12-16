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

namespace Google\Service\Vision;

class ImageAnnotationContext extends \Google\Model
{
  /**
   * If the file was a PDF or TIFF, this field gives the page number within the
   * file used to produce the image.
   *
   * @var int
   */
  public $pageNumber;
  /**
   * The URI of the file used to produce the image.
   *
   * @var string
   */
  public $uri;

  /**
   * If the file was a PDF or TIFF, this field gives the page number within the
   * file used to produce the image.
   *
   * @param int $pageNumber
   */
  public function setPageNumber($pageNumber)
  {
    $this->pageNumber = $pageNumber;
  }
  /**
   * @return int
   */
  public function getPageNumber()
  {
    return $this->pageNumber;
  }
  /**
   * The URI of the file used to produce the image.
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
class_alias(ImageAnnotationContext::class, 'Google_Service_Vision_ImageAnnotationContext');
