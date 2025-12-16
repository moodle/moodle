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

namespace Google\Service\CloudSearch;

class ItemContent extends \Google\Model
{
  /**
   * Invalid value.
   */
  public const CONTENT_FORMAT_UNSPECIFIED = 'UNSPECIFIED';
  /**
   * contentFormat is HTML.
   */
  public const CONTENT_FORMAT_HTML = 'HTML';
  /**
   * contentFormat is free text.
   */
  public const CONTENT_FORMAT_TEXT = 'TEXT';
  /**
   * contentFormat is raw bytes.
   */
  public const CONTENT_FORMAT_RAW = 'RAW';
  protected $contentDataRefType = UploadItemRef::class;
  protected $contentDataRefDataType = '';
  /**
   * @var string
   */
  public $contentFormat;
  /**
   * Hashing info calculated and provided by the API client for content. Can be
   * used with the items.push method to calculate modified state. The maximum
   * length is 2048 characters.
   *
   * @var string
   */
  public $hash;
  /**
   * Content that is supplied inlined within the update method. The maximum
   * length is 102400 bytes (100 KiB).
   *
   * @var string
   */
  public $inlineContent;

  /**
   * Upload reference ID of a previously uploaded content via write method.
   *
   * @param UploadItemRef $contentDataRef
   */
  public function setContentDataRef(UploadItemRef $contentDataRef)
  {
    $this->contentDataRef = $contentDataRef;
  }
  /**
   * @return UploadItemRef
   */
  public function getContentDataRef()
  {
    return $this->contentDataRef;
  }
  /**
   * @param self::CONTENT_FORMAT_* $contentFormat
   */
  public function setContentFormat($contentFormat)
  {
    $this->contentFormat = $contentFormat;
  }
  /**
   * @return self::CONTENT_FORMAT_*
   */
  public function getContentFormat()
  {
    return $this->contentFormat;
  }
  /**
   * Hashing info calculated and provided by the API client for content. Can be
   * used with the items.push method to calculate modified state. The maximum
   * length is 2048 characters.
   *
   * @param string $hash
   */
  public function setHash($hash)
  {
    $this->hash = $hash;
  }
  /**
   * @return string
   */
  public function getHash()
  {
    return $this->hash;
  }
  /**
   * Content that is supplied inlined within the update method. The maximum
   * length is 102400 bytes (100 KiB).
   *
   * @param string $inlineContent
   */
  public function setInlineContent($inlineContent)
  {
    $this->inlineContent = $inlineContent;
  }
  /**
   * @return string
   */
  public function getInlineContent()
  {
    return $this->inlineContent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ItemContent::class, 'Google_Service_CloudSearch_ItemContent');
