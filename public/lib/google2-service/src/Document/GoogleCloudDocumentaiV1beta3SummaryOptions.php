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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1beta3SummaryOptions extends \Google\Model
{
  /**
   * Default.
   */
  public const FORMAT_FORMAT_UNSPECIFIED = 'FORMAT_UNSPECIFIED';
  /**
   * Format the output in paragraphs.
   */
  public const FORMAT_PARAGRAPH = 'PARAGRAPH';
  /**
   * Format the output in bullets.
   */
  public const FORMAT_BULLETS = 'BULLETS';
  /**
   * Default.
   */
  public const LENGTH_LENGTH_UNSPECIFIED = 'LENGTH_UNSPECIFIED';
  /**
   * A brief summary of one or two sentences.
   */
  public const LENGTH_BRIEF = 'BRIEF';
  /**
   * A paragraph-length summary.
   */
  public const LENGTH_MODERATE = 'MODERATE';
  /**
   * The longest option available.
   */
  public const LENGTH_COMPREHENSIVE = 'COMPREHENSIVE';
  /**
   * The format the summary should be in.
   *
   * @var string
   */
  public $format;
  /**
   * How long the summary should be.
   *
   * @var string
   */
  public $length;

  /**
   * The format the summary should be in.
   *
   * Accepted values: FORMAT_UNSPECIFIED, PARAGRAPH, BULLETS
   *
   * @param self::FORMAT_* $format
   */
  public function setFormat($format)
  {
    $this->format = $format;
  }
  /**
   * @return self::FORMAT_*
   */
  public function getFormat()
  {
    return $this->format;
  }
  /**
   * How long the summary should be.
   *
   * Accepted values: LENGTH_UNSPECIFIED, BRIEF, MODERATE, COMPREHENSIVE
   *
   * @param self::LENGTH_* $length
   */
  public function setLength($length)
  {
    $this->length = $length;
  }
  /**
   * @return self::LENGTH_*
   */
  public function getLength()
  {
    return $this->length;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1beta3SummaryOptions::class, 'Google_Service_Document_GoogleCloudDocumentaiV1beta3SummaryOptions');
