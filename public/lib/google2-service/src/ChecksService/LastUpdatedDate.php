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

namespace Google\Service\ChecksService;

class LastUpdatedDate extends \Google\Model
{
  /**
   * @var string
   */
  public $endOffset;
  protected $lastUpdatedDateType = Date::class;
  protected $lastUpdatedDateDataType = '';
  /**
   * @var string
   */
  public $startOffset;
  /**
   * @var string
   */
  public $textContent;

  /**
   * @param string
   */
  public function setEndOffset($endOffset)
  {
    $this->endOffset = $endOffset;
  }
  /**
   * @return string
   */
  public function getEndOffset()
  {
    return $this->endOffset;
  }
  /**
   * @param Date
   */
  public function setLastUpdatedDate(Date $lastUpdatedDate)
  {
    $this->lastUpdatedDate = $lastUpdatedDate;
  }
  /**
   * @return Date
   */
  public function getLastUpdatedDate()
  {
    return $this->lastUpdatedDate;
  }
  /**
   * @param string
   */
  public function setStartOffset($startOffset)
  {
    $this->startOffset = $startOffset;
  }
  /**
   * @return string
   */
  public function getStartOffset()
  {
    return $this->startOffset;
  }
  /**
   * @param string
   */
  public function setTextContent($textContent)
  {
    $this->textContent = $textContent;
  }
  /**
   * @return string
   */
  public function getTextContent()
  {
    return $this->textContent;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LastUpdatedDate::class, 'Google_Service_ChecksService_LastUpdatedDate');
