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

class ReadingPosition extends \Google\Model
{
  /**
   * Position in an EPUB as a CFI.
   *
   * @var string
   */
  public $epubCfiPosition;
  /**
   * Position in a volume for image-based content.
   *
   * @var string
   */
  public $gbImagePosition;
  /**
   * Position in a volume for text-based content.
   *
   * @var string
   */
  public $gbTextPosition;
  /**
   * Resource type for a reading position.
   *
   * @var string
   */
  public $kind;
  /**
   * Position in a PDF file.
   *
   * @var string
   */
  public $pdfPosition;
  /**
   * Timestamp when this reading position was last updated (formatted UTC
   * timestamp with millisecond resolution).
   *
   * @var string
   */
  public $updated;
  /**
   * Volume id associated with this reading position.
   *
   * @var string
   */
  public $volumeId;

  /**
   * Position in an EPUB as a CFI.
   *
   * @param string $epubCfiPosition
   */
  public function setEpubCfiPosition($epubCfiPosition)
  {
    $this->epubCfiPosition = $epubCfiPosition;
  }
  /**
   * @return string
   */
  public function getEpubCfiPosition()
  {
    return $this->epubCfiPosition;
  }
  /**
   * Position in a volume for image-based content.
   *
   * @param string $gbImagePosition
   */
  public function setGbImagePosition($gbImagePosition)
  {
    $this->gbImagePosition = $gbImagePosition;
  }
  /**
   * @return string
   */
  public function getGbImagePosition()
  {
    return $this->gbImagePosition;
  }
  /**
   * Position in a volume for text-based content.
   *
   * @param string $gbTextPosition
   */
  public function setGbTextPosition($gbTextPosition)
  {
    $this->gbTextPosition = $gbTextPosition;
  }
  /**
   * @return string
   */
  public function getGbTextPosition()
  {
    return $this->gbTextPosition;
  }
  /**
   * Resource type for a reading position.
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Position in a PDF file.
   *
   * @param string $pdfPosition
   */
  public function setPdfPosition($pdfPosition)
  {
    $this->pdfPosition = $pdfPosition;
  }
  /**
   * @return string
   */
  public function getPdfPosition()
  {
    return $this->pdfPosition;
  }
  /**
   * Timestamp when this reading position was last updated (formatted UTC
   * timestamp with millisecond resolution).
   *
   * @param string $updated
   */
  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }
  /**
   * @return string
   */
  public function getUpdated()
  {
    return $this->updated;
  }
  /**
   * Volume id associated with this reading position.
   *
   * @param string $volumeId
   */
  public function setVolumeId($volumeId)
  {
    $this->volumeId = $volumeId;
  }
  /**
   * @return string
   */
  public function getVolumeId()
  {
    return $this->volumeId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ReadingPosition::class, 'Google_Service_Books_ReadingPosition');
