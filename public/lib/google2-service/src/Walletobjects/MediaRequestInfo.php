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

namespace Google\Service\Walletobjects;

class MediaRequestInfo extends \Google\Model
{
  /**
   * Such requests signals the start of a request containing media upload. Only
   * the media field(s) in the inserted/updated resource are set. The response
   * should either return an error or succeed. On success, responses don't need
   * to contain anything.
   */
  public const NOTIFICATION_TYPE_START = 'START';
  /**
   * Such requests signals that the upload has progressed and that the backend
   * might want to access the media file specified in relevant fields in the
   * resource. Only the media field(s) in the inserted/updated resource are set.
   * The response should either return an error or succeed. On success,
   * responses don't need to contain anything.
   */
  public const NOTIFICATION_TYPE_PROGRESS = 'PROGRESS';
  /**
   * Such requests signals the end of a request containing media upload. END
   * should be handled just like normal Insert/Upload requests, that is, they
   * should process the request and return a complete resource in the response.
   * Pointers to media data (a GFS path usually) appear in the relevant fields
   * in the inserted/updated resource. See gdata.Media in data.proto.
   */
  public const NOTIFICATION_TYPE_END = 'END';
  /**
   * Such requests occur after an END and signal that the response has been sent
   * back to the client. RESPONSE_SENT is only sent to the backend if it is
   * configured to receive them. The response does not need to contain anything.
   */
  public const NOTIFICATION_TYPE_RESPONSE_SENT = 'RESPONSE_SENT';
  /**
   * Such requests indicate that an error occurred while processing the request.
   * ERROR is only sent to the backend if it is configured to receive them. It
   * is not guaranteed that all errors will result in this notification to the
   * backend, even if the backend requests them. Since these requests are just
   * for informational purposes, the response does not need to contain anything.
   */
  public const NOTIFICATION_TYPE_ERROR = 'ERROR';
  /**
   * The number of current bytes uploaded or downloaded.
   *
   * @var string
   */
  public $currentBytes;
  /**
   * Data to be copied to backend requests. Custom data is returned to Scotty in
   * the agent_state field, which Scotty will then provide in subsequent upload
   * notifications.
   *
   * @var string
   */
  public $customData;
  /**
   * Set if the http request info is diff encoded. The value of this field is
   * the version number of the base revision. This is corresponding to Apiary's
   * mediaDiffObjectVersion (//depot/google3/java/com/google/api/server/media/va
   * riable/DiffObjectVersionVariable.java). See go/esf-scotty-diff-upload for
   * more information.
   *
   * @var string
   */
  public $diffObjectVersion;
  /**
   * @var int
   */
  public $finalStatus;
  /**
   * The type of notification received from Scotty.
   *
   * @var string
   */
  public $notificationType;
  /**
   * The physical headers provided by RequestReceivedParameters in Scotty
   * request. type is uploader_service.KeyValuePairs.
   *
   * @var string
   */
  public $physicalHeaders;
  /**
   * The Scotty request ID.
   *
   * @var string
   */
  public $requestId;
  /**
   * The partition of the Scotty server handling this request. type is
   * uploader_service.RequestReceivedParamsServingInfo
   * LINT.IfChange(request_received_params_serving_info_annotations)
   * LINT.ThenChange()
   *
   * @var string
   */
  public $requestReceivedParamsServingInfo;
  /**
   * The total size of the file.
   *
   * @var string
   */
  public $totalBytes;
  /**
   * Whether the total bytes field contains an estimated data.
   *
   * @var bool
   */
  public $totalBytesIsEstimated;

  /**
   * The number of current bytes uploaded or downloaded.
   *
   * @param string $currentBytes
   */
  public function setCurrentBytes($currentBytes)
  {
    $this->currentBytes = $currentBytes;
  }
  /**
   * @return string
   */
  public function getCurrentBytes()
  {
    return $this->currentBytes;
  }
  /**
   * Data to be copied to backend requests. Custom data is returned to Scotty in
   * the agent_state field, which Scotty will then provide in subsequent upload
   * notifications.
   *
   * @param string $customData
   */
  public function setCustomData($customData)
  {
    $this->customData = $customData;
  }
  /**
   * @return string
   */
  public function getCustomData()
  {
    return $this->customData;
  }
  /**
   * Set if the http request info is diff encoded. The value of this field is
   * the version number of the base revision. This is corresponding to Apiary's
   * mediaDiffObjectVersion (//depot/google3/java/com/google/api/server/media/va
   * riable/DiffObjectVersionVariable.java). See go/esf-scotty-diff-upload for
   * more information.
   *
   * @param string $diffObjectVersion
   */
  public function setDiffObjectVersion($diffObjectVersion)
  {
    $this->diffObjectVersion = $diffObjectVersion;
  }
  /**
   * @return string
   */
  public function getDiffObjectVersion()
  {
    return $this->diffObjectVersion;
  }
  /**
   * @param int $finalStatus
   */
  public function setFinalStatus($finalStatus)
  {
    $this->finalStatus = $finalStatus;
  }
  /**
   * @return int
   */
  public function getFinalStatus()
  {
    return $this->finalStatus;
  }
  /**
   * The type of notification received from Scotty.
   *
   * Accepted values: START, PROGRESS, END, RESPONSE_SENT, ERROR
   *
   * @param self::NOTIFICATION_TYPE_* $notificationType
   */
  public function setNotificationType($notificationType)
  {
    $this->notificationType = $notificationType;
  }
  /**
   * @return self::NOTIFICATION_TYPE_*
   */
  public function getNotificationType()
  {
    return $this->notificationType;
  }
  /**
   * The physical headers provided by RequestReceivedParameters in Scotty
   * request. type is uploader_service.KeyValuePairs.
   *
   * @param string $physicalHeaders
   */
  public function setPhysicalHeaders($physicalHeaders)
  {
    $this->physicalHeaders = $physicalHeaders;
  }
  /**
   * @return string
   */
  public function getPhysicalHeaders()
  {
    return $this->physicalHeaders;
  }
  /**
   * The Scotty request ID.
   *
   * @param string $requestId
   */
  public function setRequestId($requestId)
  {
    $this->requestId = $requestId;
  }
  /**
   * @return string
   */
  public function getRequestId()
  {
    return $this->requestId;
  }
  /**
   * The partition of the Scotty server handling this request. type is
   * uploader_service.RequestReceivedParamsServingInfo
   * LINT.IfChange(request_received_params_serving_info_annotations)
   * LINT.ThenChange()
   *
   * @param string $requestReceivedParamsServingInfo
   */
  public function setRequestReceivedParamsServingInfo($requestReceivedParamsServingInfo)
  {
    $this->requestReceivedParamsServingInfo = $requestReceivedParamsServingInfo;
  }
  /**
   * @return string
   */
  public function getRequestReceivedParamsServingInfo()
  {
    return $this->requestReceivedParamsServingInfo;
  }
  /**
   * The total size of the file.
   *
   * @param string $totalBytes
   */
  public function setTotalBytes($totalBytes)
  {
    $this->totalBytes = $totalBytes;
  }
  /**
   * @return string
   */
  public function getTotalBytes()
  {
    return $this->totalBytes;
  }
  /**
   * Whether the total bytes field contains an estimated data.
   *
   * @param bool $totalBytesIsEstimated
   */
  public function setTotalBytesIsEstimated($totalBytesIsEstimated)
  {
    $this->totalBytesIsEstimated = $totalBytesIsEstimated;
  }
  /**
   * @return bool
   */
  public function getTotalBytesIsEstimated()
  {
    return $this->totalBytesIsEstimated;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(MediaRequestInfo::class, 'Google_Service_Walletobjects_MediaRequestInfo');
