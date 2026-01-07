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

namespace Google\Service\TPU;

class QueuedResourceState extends \Google\Model
{
  /**
   * State of the QueuedResource request is not known/set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The QueuedResource request has been received. We're still working on
   * determining if we will be able to honor this request.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The QueuedResource request has passed initial validation/admission control
   * and has been persisted in the queue.
   */
  public const STATE_ACCEPTED = 'ACCEPTED';
  /**
   * The QueuedResource request has been selected. The associated resources are
   * currently being provisioned (or very soon will begin provisioning).
   */
  public const STATE_PROVISIONING = 'PROVISIONING';
  /**
   * The request could not be completed. This may be due to some late-discovered
   * problem with the request itself, or due to unavailability of resources
   * within the constraints of the request (e.g., the 'valid until' start timing
   * constraint expired).
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The QueuedResource is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The resources specified in the QueuedResource request have been provisioned
   * and are ready for use by the end-user/consumer.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The resources specified in the QueuedResource request are being deleted.
   * This may have been initiated by the user, or the Cloud TPU service. Inspect
   * the state data for more details.
   */
  public const STATE_SUSPENDING = 'SUSPENDING';
  /**
   * The resources specified in the QueuedResource request have been deleted.
   */
  public const STATE_SUSPENDED = 'SUSPENDED';
  /**
   * The QueuedResource request has passed initial validation and has been
   * persisted in the queue. It will remain in this state until there are
   * sufficient free resources to begin provisioning your request. Wait times
   * will vary significantly depending on demand levels. When demand is high,
   * not all requests can be immediately provisioned. If you need more reliable
   * obtainability of TPUs consider purchasing a reservation. To put a limit on
   * how long you are willing to wait, use [timing
   * constraints](https://cloud.google.com/tpu/docs/queued-
   * resources#request_a_queued_resource_before_a_specified_time).
   */
  public const STATE_WAITING_FOR_RESOURCES = 'WAITING_FOR_RESOURCES';
  /**
   * The state initiator is unspecified.
   */
  public const STATE_INITIATOR_STATE_INITIATOR_UNSPECIFIED = 'STATE_INITIATOR_UNSPECIFIED';
  /**
   * The current QueuedResource state was initiated by the user.
   */
  public const STATE_INITIATOR_USER = 'USER';
  /**
   * The current QueuedResource state was initiated by the service.
   */
  public const STATE_INITIATOR_SERVICE = 'SERVICE';
  protected $acceptedDataType = AcceptedData::class;
  protected $acceptedDataDataType = '';
  protected $activeDataType = ActiveData::class;
  protected $activeDataDataType = '';
  protected $creatingDataType = CreatingData::class;
  protected $creatingDataDataType = '';
  protected $deletingDataType = DeletingData::class;
  protected $deletingDataDataType = '';
  protected $failedDataType = FailedData::class;
  protected $failedDataDataType = '';
  protected $provisioningDataType = ProvisioningData::class;
  protected $provisioningDataDataType = '';
  /**
   * Output only. State of the QueuedResource request.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The initiator of the QueuedResources's current state. Used to
   * indicate whether the SUSPENDING/SUSPENDED state was initiated by the user
   * or the service.
   *
   * @var string
   */
  public $stateInitiator;
  protected $suspendedDataType = SuspendedData::class;
  protected $suspendedDataDataType = '';
  protected $suspendingDataType = SuspendingData::class;
  protected $suspendingDataDataType = '';

  /**
   * Output only. Further data for the accepted state.
   *
   * @param AcceptedData $acceptedData
   */
  public function setAcceptedData(AcceptedData $acceptedData)
  {
    $this->acceptedData = $acceptedData;
  }
  /**
   * @return AcceptedData
   */
  public function getAcceptedData()
  {
    return $this->acceptedData;
  }
  /**
   * Output only. Further data for the active state.
   *
   * @param ActiveData $activeData
   */
  public function setActiveData(ActiveData $activeData)
  {
    $this->activeData = $activeData;
  }
  /**
   * @return ActiveData
   */
  public function getActiveData()
  {
    return $this->activeData;
  }
  /**
   * Output only. Further data for the creating state.
   *
   * @param CreatingData $creatingData
   */
  public function setCreatingData(CreatingData $creatingData)
  {
    $this->creatingData = $creatingData;
  }
  /**
   * @return CreatingData
   */
  public function getCreatingData()
  {
    return $this->creatingData;
  }
  /**
   * Output only. Further data for the deleting state.
   *
   * @param DeletingData $deletingData
   */
  public function setDeletingData(DeletingData $deletingData)
  {
    $this->deletingData = $deletingData;
  }
  /**
   * @return DeletingData
   */
  public function getDeletingData()
  {
    return $this->deletingData;
  }
  /**
   * Output only. Further data for the failed state.
   *
   * @param FailedData $failedData
   */
  public function setFailedData(FailedData $failedData)
  {
    $this->failedData = $failedData;
  }
  /**
   * @return FailedData
   */
  public function getFailedData()
  {
    return $this->failedData;
  }
  /**
   * Output only. Further data for the provisioning state.
   *
   * @param ProvisioningData $provisioningData
   */
  public function setProvisioningData(ProvisioningData $provisioningData)
  {
    $this->provisioningData = $provisioningData;
  }
  /**
   * @return ProvisioningData
   */
  public function getProvisioningData()
  {
    return $this->provisioningData;
  }
  /**
   * Output only. State of the QueuedResource request.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACCEPTED, PROVISIONING,
   * FAILED, DELETING, ACTIVE, SUSPENDING, SUSPENDED, WAITING_FOR_RESOURCES
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Output only. The initiator of the QueuedResources's current state. Used to
   * indicate whether the SUSPENDING/SUSPENDED state was initiated by the user
   * or the service.
   *
   * Accepted values: STATE_INITIATOR_UNSPECIFIED, USER, SERVICE
   *
   * @param self::STATE_INITIATOR_* $stateInitiator
   */
  public function setStateInitiator($stateInitiator)
  {
    $this->stateInitiator = $stateInitiator;
  }
  /**
   * @return self::STATE_INITIATOR_*
   */
  public function getStateInitiator()
  {
    return $this->stateInitiator;
  }
  /**
   * Output only. Further data for the suspended state.
   *
   * @param SuspendedData $suspendedData
   */
  public function setSuspendedData(SuspendedData $suspendedData)
  {
    $this->suspendedData = $suspendedData;
  }
  /**
   * @return SuspendedData
   */
  public function getSuspendedData()
  {
    return $this->suspendedData;
  }
  /**
   * Output only. Further data for the suspending state.
   *
   * @param SuspendingData $suspendingData
   */
  public function setSuspendingData(SuspendingData $suspendingData)
  {
    $this->suspendingData = $suspendingData;
  }
  /**
   * @return SuspendingData
   */
  public function getSuspendingData()
  {
    return $this->suspendingData;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(QueuedResourceState::class, 'Google_Service_TPU_QueuedResourceState');
