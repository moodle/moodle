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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementV1PrintJob extends \Google\Model
{
  /**
   * Unspecified.
   */
  public const COLOR_MODE_COLOR_MODE_UNSPECIFIED = 'COLOR_MODE_UNSPECIFIED';
  /**
   * Black and white.
   */
  public const COLOR_MODE_BLACK_AND_WHITE = 'BLACK_AND_WHITE';
  /**
   * Color.
   */
  public const COLOR_MODE_COLOR = 'COLOR';
  /**
   * Unspecified.
   */
  public const DUPLEX_MODE_DUPLEX_MODE_UNSPECIFIED = 'DUPLEX_MODE_UNSPECIFIED';
  /**
   * One-sided.
   */
  public const DUPLEX_MODE_ONE_SIDED = 'ONE_SIDED';
  /**
   * Two-sided flipping over long edge.
   */
  public const DUPLEX_MODE_TWO_SIDED_LONG_EDGE = 'TWO_SIDED_LONG_EDGE';
  /**
   * Two-sided flipping over short edge.
   */
  public const DUPLEX_MODE_TWO_SIDED_SHORT_EDGE = 'TWO_SIDED_SHORT_EDGE';
  /**
   * Print job is in an unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The document was successfully printed.
   */
  public const STATE_PRINTED = 'PRINTED';
  /**
   * Print job was cancelled.
   */
  public const STATE_CANCELLED = 'CANCELLED';
  /**
   * Print job failed.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * Color mode.
   *
   * @var string
   */
  public $colorMode;
  /**
   * Print job completion timestamp.
   *
   * @var string
   */
  public $completeTime;
  /**
   * Number of copies.
   *
   * @var int
   */
  public $copyCount;
  /**
   * Print job creation timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * Number of pages in the document.
   *
   * @var int
   */
  public $documentPageCount;
  /**
   * Duplex mode.
   *
   * @var string
   */
  public $duplexMode;
  /**
   * Unique ID of the print job.
   *
   * @var string
   */
  public $id;
  /**
   * Name of the printer used for printing.
   *
   * @var string
   */
  public $printer;
  /**
   * API ID of the printer used for printing.
   *
   * @var string
   */
  public $printerId;
  /**
   * The final state of the job.
   *
   * @var string
   */
  public $state;
  /**
   * The title of the document.
   *
   * @var string
   */
  public $title;
  /**
   * The primary e-mail address of the user who submitted the print job.
   *
   * @var string
   */
  public $userEmail;
  /**
   * The unique Directory API ID of the user who submitted the print job.
   *
   * @var string
   */
  public $userId;

  /**
   * Color mode.
   *
   * Accepted values: COLOR_MODE_UNSPECIFIED, BLACK_AND_WHITE, COLOR
   *
   * @param self::COLOR_MODE_* $colorMode
   */
  public function setColorMode($colorMode)
  {
    $this->colorMode = $colorMode;
  }
  /**
   * @return self::COLOR_MODE_*
   */
  public function getColorMode()
  {
    return $this->colorMode;
  }
  /**
   * Print job completion timestamp.
   *
   * @param string $completeTime
   */
  public function setCompleteTime($completeTime)
  {
    $this->completeTime = $completeTime;
  }
  /**
   * @return string
   */
  public function getCompleteTime()
  {
    return $this->completeTime;
  }
  /**
   * Number of copies.
   *
   * @param int $copyCount
   */
  public function setCopyCount($copyCount)
  {
    $this->copyCount = $copyCount;
  }
  /**
   * @return int
   */
  public function getCopyCount()
  {
    return $this->copyCount;
  }
  /**
   * Print job creation timestamp.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Number of pages in the document.
   *
   * @param int $documentPageCount
   */
  public function setDocumentPageCount($documentPageCount)
  {
    $this->documentPageCount = $documentPageCount;
  }
  /**
   * @return int
   */
  public function getDocumentPageCount()
  {
    return $this->documentPageCount;
  }
  /**
   * Duplex mode.
   *
   * Accepted values: DUPLEX_MODE_UNSPECIFIED, ONE_SIDED, TWO_SIDED_LONG_EDGE,
   * TWO_SIDED_SHORT_EDGE
   *
   * @param self::DUPLEX_MODE_* $duplexMode
   */
  public function setDuplexMode($duplexMode)
  {
    $this->duplexMode = $duplexMode;
  }
  /**
   * @return self::DUPLEX_MODE_*
   */
  public function getDuplexMode()
  {
    return $this->duplexMode;
  }
  /**
   * Unique ID of the print job.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Name of the printer used for printing.
   *
   * @param string $printer
   */
  public function setPrinter($printer)
  {
    $this->printer = $printer;
  }
  /**
   * @return string
   */
  public function getPrinter()
  {
    return $this->printer;
  }
  /**
   * API ID of the printer used for printing.
   *
   * @param string $printerId
   */
  public function setPrinterId($printerId)
  {
    $this->printerId = $printerId;
  }
  /**
   * @return string
   */
  public function getPrinterId()
  {
    return $this->printerId;
  }
  /**
   * The final state of the job.
   *
   * Accepted values: STATE_UNSPECIFIED, PRINTED, CANCELLED, FAILED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * The title of the document.
   *
   * @param string $title
   */
  public function setTitle($title)
  {
    $this->title = $title;
  }
  /**
   * @return string
   */
  public function getTitle()
  {
    return $this->title;
  }
  /**
   * The primary e-mail address of the user who submitted the print job.
   *
   * @param string $userEmail
   */
  public function setUserEmail($userEmail)
  {
    $this->userEmail = $userEmail;
  }
  /**
   * @return string
   */
  public function getUserEmail()
  {
    return $this->userEmail;
  }
  /**
   * The unique Directory API ID of the user who submitted the print job.
   *
   * @param string $userId
   */
  public function setUserId($userId)
  {
    $this->userId = $userId;
  }
  /**
   * @return string
   */
  public function getUserId()
  {
    return $this->userId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementV1PrintJob::class, 'Google_Service_ChromeManagement_GoogleChromeManagementV1PrintJob');
