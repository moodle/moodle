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

class Photo extends \Google\Model
{
  /**
   * True if the photo is a default photo; false if the photo is a user-provided
   * photo.
   *
   * @var bool
   */
  public $default;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The URL of the photo. You can change the desired size by appending a query
   * parameter `sz={size}` at the end of the url, where {size} is the size in
   * pixels. Example: https://lh3.googleusercontent.com/-
   * T_wVWLlmg7w/AAAAAAAAAAI/AAAAAAAABa8/00gzXvDBYqw/s100/photo.jpg?sz=50
   *
   * @var string
   */
  public $url;

  /**
   * True if the photo is a default photo; false if the photo is a user-provided
   * photo.
   *
   * @param bool $default
   */
  public function setDefault($default)
  {
    $this->default = $default;
  }
  /**
   * @return bool
   */
  public function getDefault()
  {
    return $this->default;
  }
  /**
   * Metadata about the photo.
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
  /**
   * The URL of the photo. You can change the desired size by appending a query
   * parameter `sz={size}` at the end of the url, where {size} is the size in
   * pixels. Example: https://lh3.googleusercontent.com/-
   * T_wVWLlmg7w/AAAAAAAAAAI/AAAAAAAABa8/00gzXvDBYqw/s100/photo.jpg?sz=50
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
class_alias(Photo::class, 'Google_Service_PeopleService_Photo');
