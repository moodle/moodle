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

namespace Google\Service\Chromewebstore;

class MediaRequestInfo extends \Google\Model
{
  /**
   * @var string
   */
  public $currentBytes;
  /**
   * @var string
   */
  public $customData;
  /**
   * @var string
   */
  public $diffObjectVersion;
  /**
   * @var int
   */
  public $finalStatus;
  /**
   * @var string
   */
  public $notificationType;
  /**
   * @var string
   */
  public $physicalHeaders;
  /**
   * @var string
   */
  public $requestId;
  /**
   * @var string
   */
  public $requestReceivedParamsServingInfo;
  /**
   * @var string
   */
  public $totalBytes;
  /**
   * @var bool
   */
  public $totalBytesIsEstimated;

  /**
   * @param string
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
   * @param string
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
   * @param string
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
   * @param int
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
   * @param string
   */
  public function setNotificationType($notificationType)
  {
    $this->notificationType = $notificationType;
  }
  /**
   * @return string
   */
  public function getNotificationType()
  {
    return $this->notificationType;
  }
  /**
   * @param string
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
   * @param string
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
   * @param string
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
   * @param string
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
   * @param bool
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
class_alias(MediaRequestInfo::class, 'Google_Service_Chromewebstore_MediaRequestInfo');
