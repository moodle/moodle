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

namespace Google\Service\DataManager;

class RequestStatusPerDestination extends \Google\Model
{
  /**
   * The request status is unknown.
   */
  public const REQUEST_STATUS_REQUEST_STATUS_UNKNOWN = 'REQUEST_STATUS_UNKNOWN';
  /**
   * The request succeeded.
   */
  public const REQUEST_STATUS_SUCCESS = 'SUCCESS';
  /**
   * The request is processing.
   */
  public const REQUEST_STATUS_PROCESSING = 'PROCESSING';
  /**
   * The request failed.
   */
  public const REQUEST_STATUS_FAILED = 'FAILED';
  /**
   * The request partially succeeded.
   */
  public const REQUEST_STATUS_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  protected $audienceMembersIngestionStatusType = IngestAudienceMembersStatus::class;
  protected $audienceMembersIngestionStatusDataType = '';
  protected $audienceMembersRemovalStatusType = RemoveAudienceMembersStatus::class;
  protected $audienceMembersRemovalStatusDataType = '';
  protected $destinationType = Destination::class;
  protected $destinationDataType = '';
  protected $errorInfoType = ErrorInfo::class;
  protected $errorInfoDataType = '';
  protected $eventsIngestionStatusType = IngestEventsStatus::class;
  protected $eventsIngestionStatusDataType = '';
  /**
   * The request status of the destination.
   *
   * @var string
   */
  public $requestStatus;
  protected $warningInfoType = WarningInfo::class;
  protected $warningInfoDataType = '';

  /**
   * The status of the ingest audience members request.
   *
   * @param IngestAudienceMembersStatus $audienceMembersIngestionStatus
   */
  public function setAudienceMembersIngestionStatus(IngestAudienceMembersStatus $audienceMembersIngestionStatus)
  {
    $this->audienceMembersIngestionStatus = $audienceMembersIngestionStatus;
  }
  /**
   * @return IngestAudienceMembersStatus
   */
  public function getAudienceMembersIngestionStatus()
  {
    return $this->audienceMembersIngestionStatus;
  }
  /**
   * The status of the remove audience members request.
   *
   * @param RemoveAudienceMembersStatus $audienceMembersRemovalStatus
   */
  public function setAudienceMembersRemovalStatus(RemoveAudienceMembersStatus $audienceMembersRemovalStatus)
  {
    $this->audienceMembersRemovalStatus = $audienceMembersRemovalStatus;
  }
  /**
   * @return RemoveAudienceMembersStatus
   */
  public function getAudienceMembersRemovalStatus()
  {
    return $this->audienceMembersRemovalStatus;
  }
  /**
   * A destination within a DM API request.
   *
   * @param Destination $destination
   */
  public function setDestination(Destination $destination)
  {
    $this->destination = $destination;
  }
  /**
   * @return Destination
   */
  public function getDestination()
  {
    return $this->destination;
  }
  /**
   * An error info error containing the error reason and error counts related to
   * the upload.
   *
   * @param ErrorInfo $errorInfo
   */
  public function setErrorInfo(ErrorInfo $errorInfo)
  {
    $this->errorInfo = $errorInfo;
  }
  /**
   * @return ErrorInfo
   */
  public function getErrorInfo()
  {
    return $this->errorInfo;
  }
  /**
   * The status of the ingest events request.
   *
   * @param IngestEventsStatus $eventsIngestionStatus
   */
  public function setEventsIngestionStatus(IngestEventsStatus $eventsIngestionStatus)
  {
    $this->eventsIngestionStatus = $eventsIngestionStatus;
  }
  /**
   * @return IngestEventsStatus
   */
  public function getEventsIngestionStatus()
  {
    return $this->eventsIngestionStatus;
  }
  /**
   * The request status of the destination.
   *
   * Accepted values: REQUEST_STATUS_UNKNOWN, SUCCESS, PROCESSING, FAILED,
   * PARTIAL_SUCCESS
   *
   * @param self::REQUEST_STATUS_* $requestStatus
   */
  public function setRequestStatus($requestStatus)
  {
    $this->requestStatus = $requestStatus;
  }
  /**
   * @return self::REQUEST_STATUS_*
   */
  public function getRequestStatus()
  {
    return $this->requestStatus;
  }
  /**
   * A warning info containing the warning reason and warning counts related to
   * the upload.
   *
   * @param WarningInfo $warningInfo
   */
  public function setWarningInfo(WarningInfo $warningInfo)
  {
    $this->warningInfo = $warningInfo;
  }
  /**
   * @return WarningInfo
   */
  public function getWarningInfo()
  {
    return $this->warningInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(RequestStatusPerDestination::class, 'Google_Service_DataManager_RequestStatusPerDestination');
