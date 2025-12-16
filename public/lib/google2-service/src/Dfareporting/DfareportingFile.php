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

namespace Google\Service\Dfareporting;

class DfareportingFile extends \Google\Model
{
  public const FORMAT_CSV = 'CSV';
  public const FORMAT_EXCEL = 'EXCEL';
  public const STATUS_PROCESSING = 'PROCESSING';
  public const STATUS_REPORT_AVAILABLE = 'REPORT_AVAILABLE';
  public const STATUS_FAILED = 'FAILED';
  public const STATUS_CANCELLED = 'CANCELLED';
  public const STATUS_QUEUED = 'QUEUED';
  protected $dateRangeType = DateRange::class;
  protected $dateRangeDataType = '';
  /**
   * Etag of this resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The filename of the file.
   *
   * @var string
   */
  public $fileName;
  /**
   * The output format of the report. Only available once the file is available.
   *
   * @var string
   */
  public $format;
  /**
   * The unique ID of this report file.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#file".
   *
   * @var string
   */
  public $kind;
  /**
   * The timestamp in milliseconds since epoch when this file was last modified.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * The ID of the report this file was generated from.
   *
   * @var string
   */
  public $reportId;
  /**
   * The status of the report file.
   *
   * @var string
   */
  public $status;
  protected $urlsType = DfareportingFileUrls::class;
  protected $urlsDataType = '';

  /**
   * The date range for which the file has report data. The date range will
   * always be the absolute date range for which the report is run.
   *
   * @param DateRange $dateRange
   */
  public function setDateRange(DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }
  /**
   * @return DateRange
   */
  public function getDateRange()
  {
    return $this->dateRange;
  }
  /**
   * Etag of this resource.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * The filename of the file.
   *
   * @param string $fileName
   */
  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }
  /**
   * @return string
   */
  public function getFileName()
  {
    return $this->fileName;
  }
  /**
   * The output format of the report. Only available once the file is available.
   *
   * Accepted values: CSV, EXCEL
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
   * The unique ID of this report file.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#file".
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
   * The timestamp in milliseconds since epoch when this file was last modified.
   *
   * @param string $lastModifiedTime
   */
  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }
  /**
   * @return string
   */
  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }
  /**
   * The ID of the report this file was generated from.
   *
   * @param string $reportId
   */
  public function setReportId($reportId)
  {
    $this->reportId = $reportId;
  }
  /**
   * @return string
   */
  public function getReportId()
  {
    return $this->reportId;
  }
  /**
   * The status of the report file.
   *
   * Accepted values: PROCESSING, REPORT_AVAILABLE, FAILED, CANCELLED, QUEUED
   *
   * @param self::STATUS_* $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return self::STATUS_*
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * The URLs where the completed report file can be downloaded.
   *
   * @param DfareportingFileUrls $urls
   */
  public function setUrls(DfareportingFileUrls $urls)
  {
    $this->urls = $urls;
  }
  /**
   * @return DfareportingFileUrls
   */
  public function getUrls()
  {
    return $this->urls;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DfareportingFile::class, 'Google_Service_Dfareporting_DfareportingFile');
