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

namespace Google\Service\AccessApproval;

class ApprovalRequest extends \Google\Model
{
  protected $approveType = ApproveDecision::class;
  protected $approveDataType = '';
  protected $dismissType = DismissDecision::class;
  protected $dismissDataType = '';
  /**
   * The resource name of the request. Format is "{projects|folders|organization
   * s}/{id}/approvalRequests/{approval_request}".
   *
   * @var string
   */
  public $name;
  /**
   * The time at which approval was requested.
   *
   * @var string
   */
  public $requestTime;
  protected $requestedAugmentedInfoType = AugmentedInfo::class;
  protected $requestedAugmentedInfoDataType = '';
  /**
   * The requested access duration.
   *
   * @var string
   */
  public $requestedDuration;
  /**
   * The original requested expiration for the approval. Calculated by adding
   * the requested_duration to the request_time.
   *
   * @var string
   */
  public $requestedExpiration;
  protected $requestedLocationsType = AccessLocations::class;
  protected $requestedLocationsDataType = '';
  protected $requestedReasonType = AccessReason::class;
  protected $requestedReasonDataType = '';
  /**
   * The resource for which approval is being requested. The format of the
   * resource name is defined at
   * https://cloud.google.com/apis/design/resource_names. The resource name here
   * may either be a "full" resource name (e.g.
   * "//library.googleapis.com/shelves/shelf1/books/book2") or a "relative"
   * resource name (e.g. "shelves/shelf1/books/book2") as described in the
   * resource name specification.
   *
   * @var string
   */
  public $requestedResourceName;
  protected $requestedResourcePropertiesType = ResourceProperties::class;
  protected $requestedResourcePropertiesDataType = '';

  /**
   * Access was approved.
   *
   * @param ApproveDecision $approve
   */
  public function setApprove(ApproveDecision $approve)
  {
    $this->approve = $approve;
  }
  /**
   * @return ApproveDecision
   */
  public function getApprove()
  {
    return $this->approve;
  }
  /**
   * The request was dismissed.
   *
   * @param DismissDecision $dismiss
   */
  public function setDismiss(DismissDecision $dismiss)
  {
    $this->dismiss = $dismiss;
  }
  /**
   * @return DismissDecision
   */
  public function getDismiss()
  {
    return $this->dismiss;
  }
  /**
   * The resource name of the request. Format is "{projects|folders|organization
   * s}/{id}/approvalRequests/{approval_request}".
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
   * The time at which approval was requested.
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
  /**
   * This field contains the augmented information of the request.
   *
   * @param AugmentedInfo $requestedAugmentedInfo
   */
  public function setRequestedAugmentedInfo(AugmentedInfo $requestedAugmentedInfo)
  {
    $this->requestedAugmentedInfo = $requestedAugmentedInfo;
  }
  /**
   * @return AugmentedInfo
   */
  public function getRequestedAugmentedInfo()
  {
    return $this->requestedAugmentedInfo;
  }
  /**
   * The requested access duration.
   *
   * @param string $requestedDuration
   */
  public function setRequestedDuration($requestedDuration)
  {
    $this->requestedDuration = $requestedDuration;
  }
  /**
   * @return string
   */
  public function getRequestedDuration()
  {
    return $this->requestedDuration;
  }
  /**
   * The original requested expiration for the approval. Calculated by adding
   * the requested_duration to the request_time.
   *
   * @param string $requestedExpiration
   */
  public function setRequestedExpiration($requestedExpiration)
  {
    $this->requestedExpiration = $requestedExpiration;
  }
  /**
   * @return string
   */
  public function getRequestedExpiration()
  {
    return $this->requestedExpiration;
  }
  /**
   * The locations for which approval is being requested.
   *
   * @param AccessLocations $requestedLocations
   */
  public function setRequestedLocations(AccessLocations $requestedLocations)
  {
    $this->requestedLocations = $requestedLocations;
  }
  /**
   * @return AccessLocations
   */
  public function getRequestedLocations()
  {
    return $this->requestedLocations;
  }
  /**
   * The access reason for which approval is being requested.
   *
   * @param AccessReason $requestedReason
   */
  public function setRequestedReason(AccessReason $requestedReason)
  {
    $this->requestedReason = $requestedReason;
  }
  /**
   * @return AccessReason
   */
  public function getRequestedReason()
  {
    return $this->requestedReason;
  }
  /**
   * The resource for which approval is being requested. The format of the
   * resource name is defined at
   * https://cloud.google.com/apis/design/resource_names. The resource name here
   * may either be a "full" resource name (e.g.
   * "//library.googleapis.com/shelves/shelf1/books/book2") or a "relative"
   * resource name (e.g. "shelves/shelf1/books/book2") as described in the
   * resource name specification.
   *
   * @param string $requestedResourceName
   */
  public function setRequestedResourceName($requestedResourceName)
  {
    $this->requestedResourceName = $requestedResourceName;
  }
  /**
   * @return string
   */
  public function getRequestedResourceName()
  {
    return $this->requestedResourceName;
  }
  /**
   * Properties related to the resource represented by requested_resource_name.
   *
   * @param ResourceProperties $requestedResourceProperties
   */
  public function setRequestedResourceProperties(ResourceProperties $requestedResourceProperties)
  {
    $this->requestedResourceProperties = $requestedResourceProperties;
  }
  /**
   * @return ResourceProperties
   */
  public function getRequestedResourceProperties()
  {
    return $this->requestedResourceProperties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApprovalRequest::class, 'Google_Service_AccessApproval_ApprovalRequest');
