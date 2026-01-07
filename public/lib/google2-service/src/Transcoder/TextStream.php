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

namespace Google\Service\Transcoder;

class TextStream extends \Google\Collection
{
  protected $collection_key = 'mapping';
  /**
   * The codec for this text stream. The default is `webvtt`. Supported text
   * codecs: - `srt` - `ttml` - `cea608` - `cea708` - `webvtt`
   *
   * @var string
   */
  public $codec;
  /**
   * The name for this particular text stream that will be added to the HLS/DASH
   * manifest. Not supported in MP4 files.
   *
   * @var string
   */
  public $displayName;
  /**
   * The BCP-47 language code, such as `en-US` or `sr-Latn`. For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier. Not
   * supported in MP4 files.
   *
   * @var string
   */
  public $languageCode;
  protected $mappingType = TextMapping::class;
  protected $mappingDataType = 'array';

  /**
   * The codec for this text stream. The default is `webvtt`. Supported text
   * codecs: - `srt` - `ttml` - `cea608` - `cea708` - `webvtt`
   *
   * @param string $codec
   */
  public function setCodec($codec)
  {
    $this->codec = $codec;
  }
  /**
   * @return string
   */
  public function getCodec()
  {
    return $this->codec;
  }
  /**
   * The name for this particular text stream that will be added to the HLS/DASH
   * manifest. Not supported in MP4 files.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * The BCP-47 language code, such as `en-US` or `sr-Latn`. For more
   * information, see
   * https://www.unicode.org/reports/tr35/#Unicode_locale_identifier. Not
   * supported in MP4 files.
   *
   * @param string $languageCode
   */
  public function setLanguageCode($languageCode)
  {
    $this->languageCode = $languageCode;
  }
  /**
   * @return string
   */
  public function getLanguageCode()
  {
    return $this->languageCode;
  }
  /**
   * The mapping for the JobConfig.edit_list atoms with text EditAtom.inputs.
   *
   * @param TextMapping[] $mapping
   */
  public function setMapping($mapping)
  {
    $this->mapping = $mapping;
  }
  /**
   * @return TextMapping[]
   */
  public function getMapping()
  {
    return $this->mapping;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextStream::class, 'Google_Service_Transcoder_TextStream');
