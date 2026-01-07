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

class CoverPhoto extends \Google\Model
{
  /**
   * True if the cover photo is the default cover photo; false if the cover
   * photo is a user-provided cover photo.
   *
   * @var bool
   */
  public $default;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The URL of the cover photo.
   *
   * @var string
   */
  public $url;

  /**
   * True if the cover photo is the default cover photo; false if the cover
   * photo is a user-provided cover photo.
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
   * Metadata about the cover photo.
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
   * The URL of the cover photo.
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
class_alias(CoverPhoto::class, 'Google_Service_PeopleService_CoverPhoto');
