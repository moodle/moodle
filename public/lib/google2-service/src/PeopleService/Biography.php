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

class Biography extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const CONTENT_TYPE_CONTENT_TYPE_UNSPECIFIED = 'CONTENT_TYPE_UNSPECIFIED';
  /**
   * Plain text.
   */
  public const CONTENT_TYPE_TEXT_PLAIN = 'TEXT_PLAIN';
  /**
   * HTML text.
   */
  public const CONTENT_TYPE_TEXT_HTML = 'TEXT_HTML';
  /**
   * The content type of the biography.
   *
   * @var string
   */
  public $contentType;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The short biography.
   *
   * @var string
   */
  public $value;

  /**
   * The content type of the biography.
   *
   * Accepted values: CONTENT_TYPE_UNSPECIFIED, TEXT_PLAIN, TEXT_HTML
   *
   * @param self::CONTENT_TYPE_* $contentType
   */
  public function setContentType($contentType)
  {
    $this->contentType = $contentType;
  }
  /**
   * @return self::CONTENT_TYPE_*
   */
  public function getContentType()
  {
    return $this->contentType;
  }
  /**
   * Metadata about the biography.
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
   * The short biography.
   *
   * @param string $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return string
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Biography::class, 'Google_Service_PeopleService_Biography');
