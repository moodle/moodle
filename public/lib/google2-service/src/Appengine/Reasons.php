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

namespace Google\Service\Appengine;

class Reasons extends \Google\Model
{
  /**
   * An unknown reason indicates that the abuse system has not sent a signal for
   * this container.
   */
  public const ABUSE_ABUSE_UNKNOWN_REASON = 'ABUSE_UNKNOWN_REASON';
  /**
   * Due to various reasons CCFE might proactively restate a container state to
   * a CLH to ensure that the CLH and CCFE are both aware of the container
   * state. This reason can be tied to any of the states.
   */
  public const ABUSE_ABUSE_CONTROL_PLANE_SYNC = 'ABUSE_CONTROL_PLANE_SYNC';
  /**
   * If a container is deemed abusive we receive a suspend signal. Suspend is a
   * reason to put the container into an INTERNAL_OFF state.
   */
  public const ABUSE_SUSPEND = 'SUSPEND';
  /**
   * Containers that were once considered abusive can later be deemed non-
   * abusive. When this happens we must reinstate the container. Reinstate is a
   * reason to put the container into an ON state.
   */
  public const ABUSE_REINSTATE = 'REINSTATE';
  /**
   * An unknown reason indicates that the billing system has not sent a signal
   * for this container.
   */
  public const BILLING_BILLING_UNKNOWN_REASON = 'BILLING_UNKNOWN_REASON';
  /**
   * Due to various reasons CCFE might proactively restate a container state to
   * a CLH to ensure that the CLH and CCFE are both aware of the container
   * state. This reason can be tied to any of the states.
   */
  public const BILLING_BILLING_CONTROL_PLANE_SYNC = 'BILLING_CONTROL_PLANE_SYNC';
  /**
   * Minor infractions cause a probation signal to be sent. Probation is a
   * reason to put the container into a ON state even though it is a negative
   * signal. CCFE will block mutations for this container while it is on billing
   * probation, but the CLH is expected to serve non-mutation requests.
   */
  public const BILLING_PROBATION = 'PROBATION';
  /**
   * When a billing account is closed, it is a stronger signal about non-
   * payment. Close is a reason to put the container into an INTERNAL_OFF state.
   */
  public const BILLING_CLOSE = 'CLOSE';
  /**
   * Consumers can re-open billing accounts and update accounts to pull them out
   * of probation. When this happens, we get a signal that the account is open.
   * Open is a reason to put the container into an ON state.
   */
  public const BILLING_OPEN = 'OPEN';
  /**
   * An unknown reason indicates that data governance has not sent a signal for
   * this container.
   */
  public const DATA_GOVERNANCE_DATA_GOVERNANCE_UNKNOWN_REASON = 'DATA_GOVERNANCE_UNKNOWN_REASON';
  /**
   * Due to various reasons CCFE might proactively restate a container state to
   * a CLH to ensure that the CLH and CCFE are both aware of the container
   * state. This reason can be tied to any of the states.
   */
  public const DATA_GOVERNANCE_DATA_GOVERNANCE_CONTROL_PLANE_SYNC = 'DATA_GOVERNANCE_CONTROL_PLANE_SYNC';
  /**
   * When a container is deleted we retain some data for a period of time to
   * allow the consumer to change their mind. Data governance sends a signal to
   * hide the data when this occurs. Hide is a reason to put the container in an
   * INTERNAL_OFF state.
   */
  public const DATA_GOVERNANCE_HIDE = 'HIDE';
  /**
   * The decision to un-delete a container can be made. When this happens data
   * governance tells us to unhide any hidden data. Unhide is a reason to put
   * the container in an ON state.
   */
  public const DATA_GOVERNANCE_UNHIDE = 'UNHIDE';
  /**
   * After a period of time data must be completely removed from our systems.
   * When data governance sends a purge signal we need to remove data. Purge is
   * a reason to put the container in a DELETED state. Purge is the only event
   * that triggers a delete mutation. All other events have update semantics.
   */
  public const DATA_GOVERNANCE_PURGE = 'PURGE';
  /**
   * Default Unspecified status
   */
  public const SERVICE_ACTIVATION_SERVICE_ACTIVATION_STATUS_UNSPECIFIED = 'SERVICE_ACTIVATION_STATUS_UNSPECIFIED';
  /**
   * Service is active in the project.
   */
  public const SERVICE_ACTIVATION_SERVICE_ACTIVATION_ENABLED = 'SERVICE_ACTIVATION_ENABLED';
  /**
   * Service is disabled in the project recently i.e., within last 24 hours.
   */
  public const SERVICE_ACTIVATION_SERVICE_ACTIVATION_DISABLED = 'SERVICE_ACTIVATION_DISABLED';
  /**
   * Service has been disabled for configured grace_period (default 30 days).
   */
  public const SERVICE_ACTIVATION_SERVICE_ACTIVATION_DISABLED_FULL = 'SERVICE_ACTIVATION_DISABLED_FULL';
  /**
   * Happens when PSM cannot determine the status of service in a project Could
   * happen due to variety of reasons like PERMISSION_DENIED or Project got
   * deleted etc.
   */
  public const SERVICE_ACTIVATION_SERVICE_ACTIVATION_UNKNOWN_REASON = 'SERVICE_ACTIVATION_UNKNOWN_REASON';
  /**
   * An unknown reason indicates that we have not received a signal from service
   * management about this container. Since containers are created by request of
   * service management, this reason should never be set.
   */
  public const SERVICE_MANAGEMENT_SERVICE_MANAGEMENT_UNKNOWN_REASON = 'SERVICE_MANAGEMENT_UNKNOWN_REASON';
  /**
   * Due to various reasons CCFE might proactively restate a container state to
   * a CLH to ensure that the CLH and CCFE are both aware of the container
   * state. This reason can be tied to any of the states.
   */
  public const SERVICE_MANAGEMENT_SERVICE_MANAGEMENT_CONTROL_PLANE_SYNC = 'SERVICE_MANAGEMENT_CONTROL_PLANE_SYNC';
  /**
   * When a customer activates an API CCFE notifies the CLH and sets the
   * container to the ON state.
   *
   * @deprecated
   */
  public const SERVICE_MANAGEMENT_ACTIVATION = 'ACTIVATION';
  /**
   * When a customer deactivates and API service management starts a two-step
   * process to perform the deactivation. The first step is to prepare. Prepare
   * is a reason to put the container in a EXTERNAL_OFF state.
   *
   * @deprecated
   */
  public const SERVICE_MANAGEMENT_PREPARE_DEACTIVATION = 'PREPARE_DEACTIVATION';
  /**
   * If the deactivation is cancelled, service managed needs to abort the
   * deactivation. Abort is a reason to put the container in an ON state.
   *
   * @deprecated
   */
  public const SERVICE_MANAGEMENT_ABORT_DEACTIVATION = 'ABORT_DEACTIVATION';
  /**
   * If the deactivation is followed through with, service management needs to
   * finish deactivation. Commit is a reason to put the container in a DELETED
   * state.
   *
   * @deprecated
   */
  public const SERVICE_MANAGEMENT_COMMIT_DEACTIVATION = 'COMMIT_DEACTIVATION';
  /**
   * @var string
   */
  public $abuse;
  /**
   * @var string
   */
  public $billing;
  /**
   * @var string
   */
  public $dataGovernance;
  /**
   * Consumer Container denotes if the service is active within a project or
   * not. This information could be used to clean up resources in case service
   * in DISABLED_FULL i.e. Service is inactive > 30 days.
   *
   * @var string
   */
  public $serviceActivation;
  /**
   * @var string
   */
  public $serviceManagement;

  /**
   * @param self::ABUSE_* $abuse
   */
  public function setAbuse($abuse)
  {
    $this->abuse = $abuse;
  }
  /**
   * @return self::ABUSE_*
   */
  public function getAbuse()
  {
    return $this->abuse;
  }
  /**
   * @param self::BILLING_* $billing
   */
  public function setBilling($billing)
  {
    $this->billing = $billing;
  }
  /**
   * @return self::BILLING_*
   */
  public function getBilling()
  {
    return $this->billing;
  }
  /**
   * @param self::DATA_GOVERNANCE_* $dataGovernance
   */
  public function setDataGovernance($dataGovernance)
  {
    $this->dataGovernance = $dataGovernance;
  }
  /**
   * @return self::DATA_GOVERNANCE_*
   */
  public function getDataGovernance()
  {
    return $this->dataGovernance;
  }
  /**
   * Consumer Container denotes if the service is active within a project or
   * not. This information could be used to clean up resources in case service
   * in DISABLED_FULL i.e. Service is inactive > 30 days.
   *
   * Accepted values: SERVICE_ACTIVATION_STATUS_UNSPECIFIED,
   * SERVICE_ACTIVATION_ENABLED, SERVICE_ACTIVATION_DISABLED,
   * SERVICE_ACTIVATION_DISABLED_FULL, SERVICE_ACTIVATION_UNKNOWN_REASON
   *
   * @param self::SERVICE_ACTIVATION_* $serviceActivation
   */
  public function setServiceActivation($serviceActivation)
  {
    $this->serviceActivation = $serviceActivation;
  }
  /**
   * @return self::SERVICE_ACTIVATION_*
   */
  public function getServiceActivation()
  {
    return $this->serviceActivation;
  }
  /**
   * @param self::SERVICE_MANAGEMENT_* $serviceManagement
   */
  public function setServiceManagement($serviceManagement)
  {
    $this->serviceManagement = $serviceManagement;
  }
  /**
   * @return self::SERVICE_MANAGEMENT_*
   */
  public function getServiceManagement()
  {
    return $this->serviceManagement;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Reasons::class, 'Google_Service_Appengine_Reasons');
