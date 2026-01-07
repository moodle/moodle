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

namespace Google\Service\ServiceControl;

class AuditLog extends \Google\Collection
{
  protected $collection_key = 'authorizationInfo';
  protected $authenticationInfoType = AuthenticationInfo::class;
  protected $authenticationInfoDataType = '';
  protected $authorizationInfoType = AuthorizationInfo::class;
  protected $authorizationInfoDataType = 'array';
  /**
   * Other service-specific data about the request, response, and other
   * information associated with the current audited event.
   *
   * @var array[]
   */
  public $metadata;
  /**
   * The name of the service method or operation. For API calls, this should be
   * the name of the API method. For example,
   * "google.cloud.bigquery.v2.TableService.InsertTable"
   * "google.logging.v2.ConfigServiceV2.CreateSink"
   *
   * @var string
   */
  public $methodName;
  /**
   * The number of items returned from a List or Query API method, if
   * applicable.
   *
   * @var string
   */
  public $numResponseItems;
  protected $policyViolationInfoType = PolicyViolationInfo::class;
  protected $policyViolationInfoDataType = '';
  /**
   * The operation request. This may not include all request parameters, such as
   * those that are too large, privacy-sensitive, or duplicated elsewhere in the
   * log record. It should never include user-generated data, such as file
   * contents. When the JSON object represented here has a proto equivalent, the
   * proto name will be indicated in the `@type` property.
   *
   * @var array[]
   */
  public $request;
  protected $requestMetadataType = RequestMetadata::class;
  protected $requestMetadataDataType = '';
  protected $resourceLocationType = ResourceLocation::class;
  protected $resourceLocationDataType = '';
  /**
   * The resource or collection that is the target of the operation. The name is
   * a scheme-less URI, not including the API service name. For example:
   * "projects/PROJECT_ID/zones/us-central1-a/instances"
   * "projects/PROJECT_ID/datasets/DATASET_ID"
   *
   * @var string
   */
  public $resourceName;
  /**
   * The resource's original state before mutation. Present only for operations
   * which have successfully modified the targeted resource(s). In general, this
   * field should contain all changed fields, except those that are already been
   * included in `request`, `response`, `metadata` or `service_data` fields.
   * When the JSON object represented here has a proto equivalent, the proto
   * name will be indicated in the `@type` property.
   *
   * @var array[]
   */
  public $resourceOriginalState;
  /**
   * The operation response. This may not include all response elements, such as
   * those that are too large, privacy-sensitive, or duplicated elsewhere in the
   * log record. It should never include user-generated data, such as file
   * contents. When the JSON object represented here has a proto equivalent, the
   * proto name will be indicated in the `@type` property.
   *
   * @var array[]
   */
  public $response;
  /**
   * Deprecated. Use the `metadata` field instead. Other service-specific data
   * about the request, response, and other activities.
   *
   * @deprecated
   * @var array[]
   */
  public $serviceData;
  /**
   * The name of the API service performing the operation. For example,
   * `"compute.googleapis.com"`.
   *
   * @var string
   */
  public $serviceName;
  protected $statusType = Status::class;
  protected $statusDataType = '';

  /**
   * Authentication information.
   *
   * @param AuthenticationInfo $authenticationInfo
   */
  public function setAuthenticationInfo(AuthenticationInfo $authenticationInfo)
  {
    $this->authenticationInfo = $authenticationInfo;
  }
  /**
   * @return AuthenticationInfo
   */
  public function getAuthenticationInfo()
  {
    return $this->authenticationInfo;
  }
  /**
   * Authorization information. If there are multiple resources or permissions
   * involved, then there is one AuthorizationInfo element for each {resource,
   * permission} tuple.
   *
   * @param AuthorizationInfo[] $authorizationInfo
   */
  public function setAuthorizationInfo($authorizationInfo)
  {
    $this->authorizationInfo = $authorizationInfo;
  }
  /**
   * @return AuthorizationInfo[]
   */
  public function getAuthorizationInfo()
  {
    return $this->authorizationInfo;
  }
  /**
   * Other service-specific data about the request, response, and other
   * information associated with the current audited event.
   *
   * @param array[] $metadata
   */
  public function setMetadata($metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return array[]
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The name of the service method or operation. For API calls, this should be
   * the name of the API method. For example,
   * "google.cloud.bigquery.v2.TableService.InsertTable"
   * "google.logging.v2.ConfigServiceV2.CreateSink"
   *
   * @param string $methodName
   */
  public function setMethodName($methodName)
  {
    $this->methodName = $methodName;
  }
  /**
   * @return string
   */
  public function getMethodName()
  {
    return $this->methodName;
  }
  /**
   * The number of items returned from a List or Query API method, if
   * applicable.
   *
   * @param string $numResponseItems
   */
  public function setNumResponseItems($numResponseItems)
  {
    $this->numResponseItems = $numResponseItems;
  }
  /**
   * @return string
   */
  public function getNumResponseItems()
  {
    return $this->numResponseItems;
  }
  /**
   * Indicates the policy violations for this request. If the request is denied
   * by the policy, violation information will be logged here.
   *
   * @param PolicyViolationInfo $policyViolationInfo
   */
  public function setPolicyViolationInfo(PolicyViolationInfo $policyViolationInfo)
  {
    $this->policyViolationInfo = $policyViolationInfo;
  }
  /**
   * @return PolicyViolationInfo
   */
  public function getPolicyViolationInfo()
  {
    return $this->policyViolationInfo;
  }
  /**
   * The operation request. This may not include all request parameters, such as
   * those that are too large, privacy-sensitive, or duplicated elsewhere in the
   * log record. It should never include user-generated data, such as file
   * contents. When the JSON object represented here has a proto equivalent, the
   * proto name will be indicated in the `@type` property.
   *
   * @param array[] $request
   */
  public function setRequest($request)
  {
    $this->request = $request;
  }
  /**
   * @return array[]
   */
  public function getRequest()
  {
    return $this->request;
  }
  /**
   * Metadata about the operation.
   *
   * @param RequestMetadata $requestMetadata
   */
  public function setRequestMetadata(RequestMetadata $requestMetadata)
  {
    $this->requestMetadata = $requestMetadata;
  }
  /**
   * @return RequestMetadata
   */
  public function getRequestMetadata()
  {
    return $this->requestMetadata;
  }
  /**
   * The resource location information.
   *
   * @param ResourceLocation $resourceLocation
   */
  public function setResourceLocation(ResourceLocation $resourceLocation)
  {
    $this->resourceLocation = $resourceLocation;
  }
  /**
   * @return ResourceLocation
   */
  public function getResourceLocation()
  {
    return $this->resourceLocation;
  }
  /**
   * The resource or collection that is the target of the operation. The name is
   * a scheme-less URI, not including the API service name. For example:
   * "projects/PROJECT_ID/zones/us-central1-a/instances"
   * "projects/PROJECT_ID/datasets/DATASET_ID"
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * The resource's original state before mutation. Present only for operations
   * which have successfully modified the targeted resource(s). In general, this
   * field should contain all changed fields, except those that are already been
   * included in `request`, `response`, `metadata` or `service_data` fields.
   * When the JSON object represented here has a proto equivalent, the proto
   * name will be indicated in the `@type` property.
   *
   * @param array[] $resourceOriginalState
   */
  public function setResourceOriginalState($resourceOriginalState)
  {
    $this->resourceOriginalState = $resourceOriginalState;
  }
  /**
   * @return array[]
   */
  public function getResourceOriginalState()
  {
    return $this->resourceOriginalState;
  }
  /**
   * The operation response. This may not include all response elements, such as
   * those that are too large, privacy-sensitive, or duplicated elsewhere in the
   * log record. It should never include user-generated data, such as file
   * contents. When the JSON object represented here has a proto equivalent, the
   * proto name will be indicated in the `@type` property.
   *
   * @param array[] $response
   */
  public function setResponse($response)
  {
    $this->response = $response;
  }
  /**
   * @return array[]
   */
  public function getResponse()
  {
    return $this->response;
  }
  /**
   * Deprecated. Use the `metadata` field instead. Other service-specific data
   * about the request, response, and other activities.
   *
   * @deprecated
   * @param array[] $serviceData
   */
  public function setServiceData($serviceData)
  {
    $this->serviceData = $serviceData;
  }
  /**
   * @deprecated
   * @return array[]
   */
  public function getServiceData()
  {
    return $this->serviceData;
  }
  /**
   * The name of the API service performing the operation. For example,
   * `"compute.googleapis.com"`.
   *
   * @param string $serviceName
   */
  public function setServiceName($serviceName)
  {
    $this->serviceName = $serviceName;
  }
  /**
   * @return string
   */
  public function getServiceName()
  {
    return $this->serviceName;
  }
  /**
   * The status of the overall operation.
   *
   * @param Status $status
   */
  public function setStatus(Status $status)
  {
    $this->status = $status;
  }
  /**
   * @return Status
   */
  public function getStatus()
  {
    return $this->status;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AuditLog::class, 'Google_Service_ServiceControl_AuditLog');
