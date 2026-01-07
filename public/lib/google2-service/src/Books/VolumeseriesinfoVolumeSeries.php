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

class VolumeseriesinfoVolumeSeries extends \Google\Collection
{
  protected $collection_key = 'issue';
  protected $issueType = VolumeseriesinfoVolumeSeriesIssue::class;
  protected $issueDataType = 'array';
  /**
   * The book order number in the series.
   *
   * @var int
   */
  public $orderNumber;
  /**
   * The book type in the context of series. Examples - Single Issue, Collection
   * Edition, etc.
   *
   * @var string
   */
  public $seriesBookType;
  /**
   * The series id.
   *
   * @var string
   */
  public $seriesId;

  /**
   * List of issues. Applicable only for Collection Edition and Omnibus.
   *
   * @param VolumeseriesinfoVolumeSeriesIssue[] $issue
   */
  public function setIssue($issue)
  {
    $this->issue = $issue;
  }
  /**
   * @return VolumeseriesinfoVolumeSeriesIssue[]
   */
  public function getIssue()
  {
    return $this->issue;
  }
  /**
   * The book order number in the series.
   *
   * @param int $orderNumber
   */
  public function setOrderNumber($orderNumber)
  {
    $this->orderNumber = $orderNumber;
  }
  /**
   * @return int
   */
  public function getOrderNumber()
  {
    return $this->orderNumber;
  }
  /**
   * The book type in the context of series. Examples - Single Issue, Collection
   * Edition, etc.
   *
   * @param string $seriesBookType
   */
  public function setSeriesBookType($seriesBookType)
  {
    $this->seriesBookType = $seriesBookType;
  }
  /**
   * @return string
   */
  public function getSeriesBookType()
  {
    return $this->seriesBookType;
  }
  /**
   * The series id.
   *
   * @param string $seriesId
   */
  public function setSeriesId($seriesId)
  {
    $this->seriesId = $seriesId;
  }
  /**
   * @return string
   */
  public function getSeriesId()
  {
    return $this->seriesId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(VolumeseriesinfoVolumeSeries::class, 'Google_Service_Books_VolumeseriesinfoVolumeSeries');
