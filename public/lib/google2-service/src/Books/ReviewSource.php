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

namespace Google\Service\Books;

class ReviewSource extends \Google\Model
{
  /**
   * Name of the source.
   *
   * @var string
   */
  public $description;
  /**
   * Extra text about the source of the review.
   *
   * @var string
   */
  public $extraDescription;
  /**
   * URL of the source of the review.
   *
   * @var string
   */
  public $url;

  /**
   * Name of the source.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Extra text about the source of the review.
   *
   * @param string $extraDescription
   */
  public function setExtraDescription($extraDescription)
  {
    $this->extraDescription = $extraDescription;
  }
  /**
   * @return string
   */
  public function getExtraDescription()
  {
    return $this->extraDescription;
  }
  /**
   * URL of the source of the review.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReviewSource::class, 'Google_Service_Books_ReviewSource');
