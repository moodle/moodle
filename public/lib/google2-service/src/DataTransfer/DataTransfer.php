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

namespace Google\Service\DataTransfer;

class DataTransfer extends \Google\Collection
{
  protected $collection_key = 'applicationDataTransfers';
  protected $applicationDataTransfersType = ApplicationDataTransfer::class;
  protected $applicationDataTransfersDataType = 'array';
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Read-only. The transfer's ID.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies the resource as a DataTransfer request.
   *
   * @var string
   */
  public $kind;
  /**
   * ID of the user to whom the data is being transferred.
   *
   * @var string
   */
  public $newOwnerUserId;
  /**
   * ID of the user whose data is being transferred.
   *
   * @var string
   */
  public $oldOwnerUserId;
  /**
   * Read-only. Overall transfer status.
   *
   * @var string
   */
  public $overallTransferStatusCode;
  /**
   * Read-only. The time at which the data transfer was requested.
   *
   * @var string
   */
  public $requestTime;

  /**
   * The list of per-application data transfer resources. It contains details of
   * the applications associated with this transfer resource, and also specifies
   * the applications for which data transfer has to be done at the time of the
   * transfer resource creation.
   *
   * @param ApplicationDataTransfer[] $applicationDataTransfers
   */
  public function setApplicationDataTransfers($applicationDataTransfers)
  {
    $this->applicationDataTransfers = $applicationDataTransfers;
  }
  /**
   * @return ApplicationDataTransfer[]
   */
  public function getApplicationDataTransfers()
  {
    return $this->applicationDataTransfers;
  }
  /**
   * ETag of the resource.
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
   * Read-only. The transfer's ID.
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
   * Identifies the resource as a DataTransfer request.
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
   * ID of the user to whom the data is being transferred.
   *
   * @param string $newOwnerUserId
   */
  public function setNewOwnerUserId($newOwnerUserId)
  {
    $this->newOwnerUserId = $newOwnerUserId;
  }
  /**
   * @return string
   */
  public function getNewOwnerUserId()
  {
    return $this->newOwnerUserId;
  }
  /**
   * ID of the user whose data is being transferred.
   *
   * @param string $oldOwnerUserId
   */
  public function setOldOwnerUserId($oldOwnerUserId)
  {
    $this->oldOwnerUserId = $oldOwnerUserId;
  }
  /**
   * @return string
   */
  public function getOldOwnerUserId()
  {
    return $this->oldOwnerUserId;
  }
  /**
   * Read-only. Overall transfer status.
   *
   * @param string $overallTransferStatusCode
   */
  public function setOverallTransferStatusCode($overallTransferStatusCode)
  {
    $this->overallTransferStatusCode = $overallTransferStatusCode;
  }
  /**
   * @return string
   */
  public function getOverallTransferStatusCode()
  {
    return $this->overallTransferStatusCode;
  }
  /**
   * Read-only. The time at which the data transfer was requested.
   *
   * @param string $requestTime
   */
  public function setRequestTime($requestTime)
  {
    $this->requestTime = $requestTime;
  }
  /**
   * @return string
   */
  public function getRequestTime()
  {
    return $this->requestTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DataTransfer::class, 'Google_Service_DataTransfer_DataTransfer');
