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

namespace Google\Service\CloudAlloyDBAdmin;

class UpgradeClusterResponse extends \Google\Collection
{
  /**
   * Unspecified status.
   */
  public const STATUS_STATUS_UNSPECIFIED = 'STATUS_UNSPECIFIED';
  /**
   * Not started.
   */
  public const STATUS_NOT_STARTED = 'NOT_STARTED';
  /**
   * In progress.
   */
  public const STATUS_IN_PROGRESS = 'IN_PROGRESS';
  /**
   * Operation succeeded.
   */
  public const STATUS_SUCCESS = 'SUCCESS';
  /**
   * Operation failed.
   */
  public const STATUS_FAILED = 'FAILED';
  /**
   * Operation partially succeeded.
   */
  public const STATUS_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Cancel is in progress.
   */
  public const STATUS_CANCEL_IN_PROGRESS = 'CANCEL_IN_PROGRESS';
  /**
   * Cancellation complete.
   */
  public const STATUS_CANCELLED = 'CANCELLED';
  protected $collection_key = 'clusterUpgradeDetails';
  protected $clusterUpgradeDetailsType = ClusterUpgradeDetails::class;
  protected $clusterUpgradeDetailsDataType = 'array';
  /**
   * A user friendly message summarising the upgrade operation details and the
   * next steps for the user if there is any.
   *
   * @var string
   */
  public $message;
  /**
   * Status of upgrade operation.
   *
   * @var string
   */
  public $status;

  /**
   * Array of upgrade details for the current cluster and all the secondary
   * clusters associated with this cluster.
   *
   * @param ClusterUpgradeDetails[] $clusterUpgradeDetails
   */
  public function setClusterUpgradeDetails($clusterUpgradeDetails)
  {
    $this->clusterUpgradeDetails = $clusterUpgradeDetails;
  }
  /**
   * @return ClusterUpgradeDetails[]
   */
  public function getClusterUpgradeDetails()
  {
    return $this->clusterUpgradeDetails;
  }
  /**
   * A user friendly message summarising the upgrade operation details and the
   * next steps for the user if there is any.
   *
   * @param string $message
   */
  public function setMessage($message)
  {
    $this->message = $message;
  }
  /**
   * @return string
   */
  public function getMessage()
  {
    return $this->message;
  }
  /**
   * Status of upgrade operation.
   *
   * Accepted values: STATUS_UNSPECIFIED, NOT_STARTED, IN_PROGRESS, SUCCESS,
   * FAILED, PARTIAL_SUCCESS, CANCEL_IN_PROGRESS, CANCELLED
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UpgradeClusterResponse::class, 'Google_Service_CloudAlloyDBAdmin_UpgradeClusterResponse');
