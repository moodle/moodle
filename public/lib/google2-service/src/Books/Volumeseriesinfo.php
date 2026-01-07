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

class Volumeseriesinfo extends \Google\Collection
{
  protected $collection_key = 'volumeSeries';
  /**
   * The display number string. This should be used only for display purposes
   * and the actual sequence should be inferred from the below orderNumber.
   *
   * @var string
   */
  public $bookDisplayNumber;
  /**
   * Resource type.
   *
   * @var string
   */
  public $kind;
  /**
   * Short book title in the context of the series.
   *
   * @var string
   */
  public $shortSeriesBookTitle;
  protected $volumeSeriesType = VolumeseriesinfoVolumeSeries::class;
  protected $volumeSeriesDataType = 'array';

  /**
   * The display number string. This should be used only for display purposes
   * and the actual sequence should be inferred from the below orderNumber.
   *
   * @param string $bookDisplayNumber
   */
  public function setBookDisplayNumber($bookDisplayNumber)
  {
    $this->bookDisplayNumber = $bookDisplayNumber;
  }
  /**
   * @return string
   */
  public function getBookDisplayNumber()
  {
    return $this->bookDisplayNumber;
  }
  /**
   * Resource type.
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
   * Short book title in the context of the series.
   *
   * @param string $shortSeriesBookTitle
   */
  public function setShortSeriesBookTitle($shortSeriesBookTitle)
  {
    $this->shortSeriesBookTitle = $shortSeriesBookTitle;
  }
  /**
   * @return string
   */
  public function getShortSeriesBookTitle()
  {
    return $this->shortSeriesBookTitle;
  }
  /**
   * @param VolumeseriesinfoVolumeSeries[] $volumeSeries
   */
  public function setVolumeSeries($volumeSeries)
  {
    $this->volumeSeries = $volumeSeries;
  }
  /**
   * @return VolumeseriesinfoVolumeSeries[]
   */
  public function getVolumeSeries()
  {
    return $this->volumeSeries;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Volumeseriesinfo::class, 'Google_Service_Books_Volumeseriesinfo');
