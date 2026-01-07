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

class Report extends \Google\Model
{
  public const FORMAT_CSV = 'CSV';
  public const FORMAT_EXCEL = 'EXCEL';
  public const TYPE_STANDARD = 'STANDARD';
  public const TYPE_REACH = 'REACH';
  public const TYPE_PATH_TO_CONVERSION = 'PATH_TO_CONVERSION';
  public const TYPE_FLOODLIGHT = 'FLOODLIGHT';
  public const TYPE_CROSS_MEDIA_REACH = 'CROSS_MEDIA_REACH';
  /**
   * The account ID to which this report belongs.
   *
   * @var string
   */
  public $accountId;
  protected $criteriaType = ReportCriteria::class;
  protected $criteriaDataType = '';
  protected $crossMediaReachCriteriaType = ReportCrossMediaReachCriteria::class;
  protected $crossMediaReachCriteriaDataType = '';
  protected $deliveryType = ReportDelivery::class;
  protected $deliveryDataType = '';
  /**
   * The eTag of this response for caching purposes.
   *
   * @var string
   */
  public $etag;
  /**
   * The filename used when generating report files for this report.
   *
   * @var string
   */
  public $fileName;
  protected $floodlightCriteriaType = ReportFloodlightCriteria::class;
  protected $floodlightCriteriaDataType = '';
  /**
   * The output format of the report. If not specified, default format is "CSV".
   * Note that the actual format in the completed report file might differ if
   * for instance the report's size exceeds the format's capabilities. "CSV"
   * will then be the fallback format.
   *
   * @var string
   */
  public $format;
  /**
   * The unique ID identifying this report resource.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of resource this is, in this case dfareporting#report.
   *
   * @var string
   */
  public $kind;
  /**
   * The timestamp (in milliseconds since epoch) of when this report was last
   * modified.
   *
   * @var string
   */
  public $lastModifiedTime;
  /**
   * The name of the report.
   *
   * @var string
   */
  public $name;
  /**
   * The user profile id of the owner of this report.
   *
   * @var string
   */
  public $ownerProfileId;
  protected $pathToConversionCriteriaType = ReportPathToConversionCriteria::class;
  protected $pathToConversionCriteriaDataType = '';
  protected $reachCriteriaType = ReportReachCriteria::class;
  protected $reachCriteriaDataType = '';
  protected $scheduleType = ReportSchedule::class;
  protected $scheduleDataType = '';
  /**
   * The subaccount ID to which this report belongs if applicable.
   *
   * @var string
   */
  public $subAccountId;
  /**
   * The type of the report.
   *
   * @var string
   */
  public $type;

  /**
   * The account ID to which this report belongs.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * The report criteria for a report of type "STANDARD".
   *
   * @param ReportCriteria $criteria
   */
  public function setCriteria(ReportCriteria $criteria)
  {
    $this->criteria = $criteria;
  }
  /**
   * @return ReportCriteria
   */
  public function getCriteria()
  {
    return $this->criteria;
  }
  /**
   * Optional. The report criteria for a report of type "CROSS_MEDIA_REACH".
   *
   * @param ReportCrossMediaReachCriteria $crossMediaReachCriteria
   */
  public function setCrossMediaReachCriteria(ReportCrossMediaReachCriteria $crossMediaReachCriteria)
  {
    $this->crossMediaReachCriteria = $crossMediaReachCriteria;
  }
  /**
   * @return ReportCrossMediaReachCriteria
   */
  public function getCrossMediaReachCriteria()
  {
    return $this->crossMediaReachCriteria;
  }
  /**
   * The report's email delivery settings.
   *
   * @param ReportDelivery $delivery
   */
  public function setDelivery(ReportDelivery $delivery)
  {
    $this->delivery = $delivery;
  }
  /**
   * @return ReportDelivery
   */
  public function getDelivery()
  {
    return $this->delivery;
  }
  /**
   * The eTag of this response for caching purposes.
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
   * The filename used when generating report files for this report.
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
   * The report criteria for a report of type "FLOODLIGHT".
   *
   * @param ReportFloodlightCriteria $floodlightCriteria
   */
  public function setFloodlightCriteria(ReportFloodlightCriteria $floodlightCriteria)
  {
    $this->floodlightCriteria = $floodlightCriteria;
  }
  /**
   * @return ReportFloodlightCriteria
   */
  public function getFloodlightCriteria()
  {
    return $this->floodlightCriteria;
  }
  /**
   * The output format of the report. If not specified, default format is "CSV".
   * Note that the actual format in the completed report file might differ if
   * for instance the report's size exceeds the format's capabilities. "CSV"
   * will then be the fallback format.
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
   * The unique ID identifying this report resource.
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
   * The kind of resource this is, in this case dfareporting#report.
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
   * The timestamp (in milliseconds since epoch) of when this report was last
   * modified.
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
   * The name of the report.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * The user profile id of the owner of this report.
   *
   * @param string $ownerProfileId
   */
  public function setOwnerProfileId($ownerProfileId)
  {
    $this->ownerProfileId = $ownerProfileId;
  }
  /**
   * @return string
   */
  public function getOwnerProfileId()
  {
    return $this->ownerProfileId;
  }
  /**
   * The report criteria for a report of type "PATH_TO_CONVERSION".
   *
   * @param ReportPathToConversionCriteria $pathToConversionCriteria
   */
  public function setPathToConversionCriteria(ReportPathToConversionCriteria $pathToConversionCriteria)
  {
    $this->pathToConversionCriteria = $pathToConversionCriteria;
  }
  /**
   * @return ReportPathToConversionCriteria
   */
  public function getPathToConversionCriteria()
  {
    return $this->pathToConversionCriteria;
  }
  /**
   * The report criteria for a report of type "REACH".
   *
   * @param ReportReachCriteria $reachCriteria
   */
  public function setReachCriteria(ReportReachCriteria $reachCriteria)
  {
    $this->reachCriteria = $reachCriteria;
  }
  /**
   * @return ReportReachCriteria
   */
  public function getReachCriteria()
  {
    return $this->reachCriteria;
  }
  /**
   * The report's schedule. Can only be set if the report's 'dateRange' is a
   * relative date range and the relative date range is not "TODAY".
   *
   * @param ReportSchedule $schedule
   */
  public function setSchedule(ReportSchedule $schedule)
  {
    $this->schedule = $schedule;
  }
  /**
   * @return ReportSchedule
   */
  public function getSchedule()
  {
    return $this->schedule;
  }
  /**
   * The subaccount ID to which this report belongs if applicable.
   *
   * @param string $subAccountId
   */
  public function setSubAccountId($subAccountId)
  {
    $this->subAccountId = $subAccountId;
  }
  /**
   * @return string
   */
  public function getSubAccountId()
  {
    return $this->subAccountId;
  }
  /**
   * The type of the report.
   *
   * Accepted values: STANDARD, REACH, PATH_TO_CONVERSION, FLOODLIGHT,
   * CROSS_MEDIA_REACH
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
class_alias(Report::class, 'Google_Service_Dfareporting_Report');
