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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2ByteContentItem extends \Google\Model
{
  /**
   * Unused
   */
  public const TYPE_BYTES_TYPE_UNSPECIFIED = 'BYTES_TYPE_UNSPECIFIED';
  /**
   * Any image type.
   */
  public const TYPE_IMAGE = 'IMAGE';
  /**
   * jpeg
   */
  public const TYPE_IMAGE_JPEG = 'IMAGE_JPEG';
  /**
   * bmp
   */
  public const TYPE_IMAGE_BMP = 'IMAGE_BMP';
  /**
   * png
   */
  public const TYPE_IMAGE_PNG = 'IMAGE_PNG';
  /**
   * svg
   */
  public const TYPE_IMAGE_SVG = 'IMAGE_SVG';
  /**
   * plain text
   */
  public const TYPE_TEXT_UTF8 = 'TEXT_UTF8';
  /**
   * docx, docm, dotx, dotm
   */
  public const TYPE_WORD_DOCUMENT = 'WORD_DOCUMENT';
  /**
   * pdf
   */
  public const TYPE_PDF = 'PDF';
  /**
   * pptx, pptm, potx, potm, pot
   */
  public const TYPE_POWERPOINT_DOCUMENT = 'POWERPOINT_DOCUMENT';
  /**
   * xlsx, xlsm, xltx, xltm
   */
  public const TYPE_EXCEL_DOCUMENT = 'EXCEL_DOCUMENT';
  /**
   * avro
   */
  public const TYPE_AVRO = 'AVRO';
  /**
   * csv
   */
  public const TYPE_CSV = 'CSV';
  /**
   * tsv
   */
  public const TYPE_TSV = 'TSV';
  /**
   * Audio file types. Only used for profiling.
   */
  public const TYPE_AUDIO = 'AUDIO';
  /**
   * Video file types. Only used for profiling.
   */
  public const TYPE_VIDEO = 'VIDEO';
  /**
   * Executable file types. Only used for profiling.
   */
  public const TYPE_EXECUTABLE = 'EXECUTABLE';
  /**
   * AI model file types. Only used for profiling.
   */
  public const TYPE_AI_MODEL = 'AI_MODEL';
  /**
   * Content data to inspect or redact.
   *
   * @var string
   */
  public $data;
  /**
   * The type of data stored in the bytes string. Default will be TEXT_UTF8.
   *
   * @var string
   */
  public $type;

  /**
   * Content data to inspect or redact.
   *
   * @param string $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return string
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * The type of data stored in the bytes string. Default will be TEXT_UTF8.
   *
   * Accepted values: BYTES_TYPE_UNSPECIFIED, IMAGE, IMAGE_JPEG, IMAGE_BMP,
   * IMAGE_PNG, IMAGE_SVG, TEXT_UTF8, WORD_DOCUMENT, PDF, POWERPOINT_DOCUMENT,
   * EXCEL_DOCUMENT, AVRO, CSV, TSV, AUDIO, VIDEO, EXECUTABLE, AI_MODEL
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
class_alias(GooglePrivacyDlpV2ByteContentItem::class, 'Google_Service_DLP_GooglePrivacyDlpV2ByteContentItem');
