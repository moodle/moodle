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

namespace Google\Service\Analytics;

class UnsampledReport extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "endDate" => "end-date",
        "startDate" => "start-date",
  ];
  /**
   * Account ID to which this unsampled report belongs.
   *
   * @var string
   */
  public $accountId;
  protected $cloudStorageDownloadDetailsType = UnsampledReportCloudStorageDownloadDetails::class;
  protected $cloudStorageDownloadDetailsDataType = '';
  /**
   * Time this unsampled report was created.
   *
   * @var string
   */
  public $created;
  /**
   * The dimensions for the unsampled report.
   *
   * @var string
   */
  public $dimensions;
  /**
   * The type of download you need to use for the report data file. Possible
   * values include `GOOGLE_DRIVE` and `GOOGLE_CLOUD_STORAGE`. If the value is
   * `GOOGLE_DRIVE`, see the `driveDownloadDetails` field. If the value is
   * `GOOGLE_CLOUD_STORAGE`, see the `cloudStorageDownloadDetails` field.
   *
   * @var string
   */
  public $downloadType;
  protected $driveDownloadDetailsType = UnsampledReportDriveDownloadDetails::class;
  protected $driveDownloadDetailsDataType = '';
  /**
   * The end date for the unsampled report.
   *
   * @var string
   */
  public $endDate;
  /**
   * The filters for the unsampled report.
   *
   * @var string
   */
  public $filters;
  /**
   * Unsampled report ID.
   *
   * @var string
   */
  public $id;
  /**
   * Resource type for an Analytics unsampled report.
   *
   * @var string
   */
  public $kind;
  /**
   * The metrics for the unsampled report.
   *
   * @var string
   */
  public $metrics;
  /**
   * View (Profile) ID to which this unsampled report belongs.
   *
   * @var string
   */
  public $profileId;
  /**
   * The segment for the unsampled report.
   *
   * @var string
   */
  public $segment;
  /**
   * Link for this unsampled report.
   *
   * @var string
   */
  public $selfLink;
  /**
   * The start date for the unsampled report.
   *
   * @var string
   */
  public $startDate;
  /**
   * Status of this unsampled report. Possible values are PENDING, COMPLETED, or
   * FAILED.
   *
   * @var string
   */
  public $status;
  /**
   * Title of the unsampled report.
   *
   * @var string
   */
  public $title;
  /**
   * Time this unsampled report was last modified.
   *
   * @var string
   */
  public $updated;
  /**
   * Web property ID to which this unsampled report belongs. The web property ID
   * is of the form UA-XXXXX-YY.
   *
   * @var string
   */
  public $webPropertyId;

  /**
   * Account ID to which this unsampled report belongs.
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
   * Download details for a file stored in Google Cloud Storage.
   *
   * @param UnsampledReportCloudStorageDownloadDetails $cloudStorageDownloadDetails
   */
  public function setCloudStorageDownloadDetails(UnsampledReportCloudStorageDownloadDetails $cloudStorageDownloadDetails)
  {
    $this->cloudStorageDownloadDetails = $cloudStorageDownloadDetails;
  }
  /**
   * @return UnsampledReportCloudStorageDownloadDetails
   */
  public function getCloudStorageDownloadDetails()
  {
    return $this->cloudStorageDownloadDetails;
  }
  /**
   * Time this unsampled report was created.
   *
   * @param string $created
   */
  public function setCreated($created)
  {
    $this->created = $created;
  }
  /**
   * @return string
   */
  public function getCreated()
  {
    return $this->created;
  }
  /**
   * The dimensions for the unsampled report.
   *
   * @param string $dimensions
   */
  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }
  /**
   * @return string
   */
  public function getDimensions()
  {
    return $this->dimensions;
  }
  /**
   * The type of download you need to use for the report data file. Possible
   * values include `GOOGLE_DRIVE` and `GOOGLE_CLOUD_STORAGE`. If the value is
   * `GOOGLE_DRIVE`, see the `driveDownloadDetails` field. If the value is
   * `GOOGLE_CLOUD_STORAGE`, see the `cloudStorageDownloadDetails` field.
   *
   * @param string $downloadType
   */
  public function setDownloadType($downloadType)
  {
    $this->downloadType = $downloadType;
  }
  /**
   * @return string
   */
  public function getDownloadType()
  {
    return $this->downloadType;
  }
  /**
   * Download details for a file stored in Google Drive.
   *
   * @param UnsampledReportDriveDownloadDetails $driveDownloadDetails
   */
  public function setDriveDownloadDetails(UnsampledReportDriveDownloadDetails $driveDownloadDetails)
  {
    $this->driveDownloadDetails = $driveDownloadDetails;
  }
  /**
   * @return UnsampledReportDriveDownloadDetails
   */
  public function getDriveDownloadDetails()
  {
    return $this->driveDownloadDetails;
  }
  /**
   * The end date for the unsampled report.
   *
   * @param string $endDate
   */
  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }
  /**
   * @return string
   */
  public function getEndDate()
  {
    return $this->endDate;
  }
  /**
   * The filters for the unsampled report.
   *
   * @param string $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }
  /**
   * @return string
   */
  public function getFilters()
  {
    return $this->filters;
  }
  /**
   * Unsampled report ID.
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
   * Resource type for an Analytics unsampled report.
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
   * The metrics for the unsampled report.
   *
   * @param string $metrics
   */
  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }
  /**
   * @return string
   */
  public function getMetrics()
  {
    return $this->metrics;
  }
  /**
   * View (Profile) ID to which this unsampled report belongs.
   *
   * @param string $profileId
   */
  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }
  /**
   * @return string
   */
  public function getProfileId()
  {
    return $this->profileId;
  }
  /**
   * The segment for the unsampled report.
   *
   * @param string $segment
   */
  public function setSegment($segment)
  {
    $this->segment = $segment;
  }
  /**
   * @return string
   */
  public function getSegment()
  {
    return $this->segment;
  }
  /**
   * Link for this unsampled report.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
  /**
   * The start date for the unsampled report.
   *
   * @param string $startDate
   */
  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }
  /**
   * @return string
   */
  public function getStartDate()
  {
    return $this->startDate;
  }
  /**
   * Status of this unsampled report. Possible values are PENDING, COMPLETED, or
   * FAILED.
   *
   * @param string $status
   */
  public function setStatus($status)
  {
    $this->status = $status;
  }
  /**
   * @return string
   */
  public function getStatus()
  {
    return $this->status;
  }
  /**
   * Title of the unsampled report.
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
   * Time this unsampled report was last modified.
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
   * Web property ID to which this unsampled report belongs. The web property ID
   * is of the form UA-XXXXX-YY.
   *
   * @param string $webPropertyId
   */
  public function setWebPropertyId($webPropertyId)
  {
    $this->webPropertyId = $webPropertyId;
  }
  /**
   * @return string
   */
  public function getWebPropertyId()
  {
    return $this->webPropertyId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UnsampledReport::class, 'Google_Service_Analytics_UnsampledReport');
