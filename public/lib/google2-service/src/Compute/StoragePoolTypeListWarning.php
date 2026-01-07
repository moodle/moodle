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

namespace Google\Service\Compute;

class StoragePoolTypeListWarning extends \Google\Collection
{
  /**
   * Warning about failed cleanup of transient changes made by a failed
   * operation.
   */
  public const CODE_CLEANUP_FAILED = 'CLEANUP_FAILED';
  /**
   * A link to a deprecated resource was created.
   */
  public const CODE_DEPRECATED_RESOURCE_USED = 'DEPRECATED_RESOURCE_USED';
  /**
   * When deploying and at least one of the resources has a type marked as
   * deprecated
   */
  public const CODE_DEPRECATED_TYPE_USED = 'DEPRECATED_TYPE_USED';
  /**
   * The user created a boot disk that is larger than image size.
   */
  public const CODE_DISK_SIZE_LARGER_THAN_IMAGE_SIZE = 'DISK_SIZE_LARGER_THAN_IMAGE_SIZE';
  /**
   * When deploying and at least one of the resources has a type marked as
   * experimental
   */
  public const CODE_EXPERIMENTAL_TYPE_USED = 'EXPERIMENTAL_TYPE_USED';
  /**
   * Warning that is present in an external api call
   */
  public const CODE_EXTERNAL_API_WARNING = 'EXTERNAL_API_WARNING';
  /**
   * Warning that value of a field has been overridden. Deprecated unused field.
   *
   * @deprecated
   */
  public const CODE_FIELD_VALUE_OVERRIDEN = 'FIELD_VALUE_OVERRIDEN';
  /**
   * The operation involved use of an injected kernel, which is deprecated.
   */
  public const CODE_INJECTED_KERNELS_DEPRECATED = 'INJECTED_KERNELS_DEPRECATED';
  /**
   * A WEIGHTED_MAGLEV backend service is associated with a health check that is
   * not of type HTTP/HTTPS/HTTP2.
   */
  public const CODE_INVALID_HEALTH_CHECK_FOR_DYNAMIC_WIEGHTED_LB = 'INVALID_HEALTH_CHECK_FOR_DYNAMIC_WIEGHTED_LB';
  /**
   * When deploying a deployment with a exceedingly large number of resources
   */
  public const CODE_LARGE_DEPLOYMENT_WARNING = 'LARGE_DEPLOYMENT_WARNING';
  /**
   * Resource can't be retrieved due to list overhead quota exceed which
   * captures the amount of resources filtered out by user-defined list filter.
   */
  public const CODE_LIST_OVERHEAD_QUOTA_EXCEED = 'LIST_OVERHEAD_QUOTA_EXCEED';
  /**
   * A resource depends on a missing type
   */
  public const CODE_MISSING_TYPE_DEPENDENCY = 'MISSING_TYPE_DEPENDENCY';
  /**
   * The route's nextHopIp address is not assigned to an instance on the
   * network.
   */
  public const CODE_NEXT_HOP_ADDRESS_NOT_ASSIGNED = 'NEXT_HOP_ADDRESS_NOT_ASSIGNED';
  /**
   * The route's next hop instance cannot ip forward.
   */
  public const CODE_NEXT_HOP_CANNOT_IP_FORWARD = 'NEXT_HOP_CANNOT_IP_FORWARD';
  /**
   * The route's nextHopInstance URL refers to an instance that does not have an
   * ipv6 interface on the same network as the route.
   */
  public const CODE_NEXT_HOP_INSTANCE_HAS_NO_IPV6_INTERFACE = 'NEXT_HOP_INSTANCE_HAS_NO_IPV6_INTERFACE';
  /**
   * The route's nextHopInstance URL refers to an instance that does not exist.
   */
  public const CODE_NEXT_HOP_INSTANCE_NOT_FOUND = 'NEXT_HOP_INSTANCE_NOT_FOUND';
  /**
   * The route's nextHopInstance URL refers to an instance that is not on the
   * same network as the route.
   */
  public const CODE_NEXT_HOP_INSTANCE_NOT_ON_NETWORK = 'NEXT_HOP_INSTANCE_NOT_ON_NETWORK';
  /**
   * The route's next hop instance does not have a status of RUNNING.
   */
  public const CODE_NEXT_HOP_NOT_RUNNING = 'NEXT_HOP_NOT_RUNNING';
  /**
   * Error which is not critical. We decided to continue the process despite the
   * mentioned error.
   */
  public const CODE_NOT_CRITICAL_ERROR = 'NOT_CRITICAL_ERROR';
  /**
   * No results are present on a particular list page.
   */
  public const CODE_NO_RESULTS_ON_PAGE = 'NO_RESULTS_ON_PAGE';
  /**
   * Success is reported, but some results may be missing due to errors
   */
  public const CODE_PARTIAL_SUCCESS = 'PARTIAL_SUCCESS';
  /**
   * Quota information is not available to client requests (e.g: regions.list).
   */
  public const CODE_QUOTA_INFO_UNAVAILABLE = 'QUOTA_INFO_UNAVAILABLE';
  /**
   * The user attempted to use a resource that requires a TOS they have not
   * accepted.
   */
  public const CODE_REQUIRED_TOS_AGREEMENT = 'REQUIRED_TOS_AGREEMENT';
  /**
   * Warning that a resource is in use.
   */
  public const CODE_RESOURCE_IN_USE_BY_OTHER_RESOURCE_WARNING = 'RESOURCE_IN_USE_BY_OTHER_RESOURCE_WARNING';
  /**
   * One or more of the resources set to auto-delete could not be deleted
   * because they were in use.
   */
  public const CODE_RESOURCE_NOT_DELETED = 'RESOURCE_NOT_DELETED';
  /**
   * When a resource schema validation is ignored.
   */
  public const CODE_SCHEMA_VALIDATION_IGNORED = 'SCHEMA_VALIDATION_IGNORED';
  /**
   * Instance template used in instance group manager is valid as such, but its
   * application does not make a lot of sense, because it allows only single
   * instance in instance group.
   */
  public const CODE_SINGLE_INSTANCE_PROPERTY_TEMPLATE = 'SINGLE_INSTANCE_PROPERTY_TEMPLATE';
  /**
   * When undeclared properties in the schema are present
   */
  public const CODE_UNDECLARED_PROPERTIES = 'UNDECLARED_PROPERTIES';
  /**
   * A given scope cannot be reached.
   */
  public const CODE_UNREACHABLE = 'UNREACHABLE';
  protected $collection_key = 'data';
  /**
   * [Output Only] A warning code, if applicable. For example, Compute Engine
   * returns NO_RESULTS_ON_PAGE if there are no results in the response.
   *
   * @var string
   */
  public $code;
  protected $dataType = StoragePoolTypeListWarningData::class;
  protected $dataDataType = 'array';
  /**
   * [Output Only] A human-readable description of the warning code.
   *
   * @var string
   */
  public $message;

  /**
   * [Output Only] A warning code, if applicable. For example, Compute Engine
   * returns NO_RESULTS_ON_PAGE if there are no results in the response.
   *
   * Accepted values: CLEANUP_FAILED, DEPRECATED_RESOURCE_USED,
   * DEPRECATED_TYPE_USED, DISK_SIZE_LARGER_THAN_IMAGE_SIZE,
   * EXPERIMENTAL_TYPE_USED, EXTERNAL_API_WARNING, FIELD_VALUE_OVERRIDEN,
   * INJECTED_KERNELS_DEPRECATED, INVALID_HEALTH_CHECK_FOR_DYNAMIC_WIEGHTED_LB,
   * LARGE_DEPLOYMENT_WARNING, LIST_OVERHEAD_QUOTA_EXCEED,
   * MISSING_TYPE_DEPENDENCY, NEXT_HOP_ADDRESS_NOT_ASSIGNED,
   * NEXT_HOP_CANNOT_IP_FORWARD, NEXT_HOP_INSTANCE_HAS_NO_IPV6_INTERFACE,
   * NEXT_HOP_INSTANCE_NOT_FOUND, NEXT_HOP_INSTANCE_NOT_ON_NETWORK,
   * NEXT_HOP_NOT_RUNNING, NOT_CRITICAL_ERROR, NO_RESULTS_ON_PAGE,
   * PARTIAL_SUCCESS, QUOTA_INFO_UNAVAILABLE, REQUIRED_TOS_AGREEMENT,
   * RESOURCE_IN_USE_BY_OTHER_RESOURCE_WARNING, RESOURCE_NOT_DELETED,
   * SCHEMA_VALIDATION_IGNORED, SINGLE_INSTANCE_PROPERTY_TEMPLATE,
   * UNDECLARED_PROPERTIES, UNREACHABLE
   *
   * @param self::CODE_* $code
   */
  public function setCode($code)
  {
    $this->code = $code;
  }
  /**
   * @return self::CODE_*
   */
  public function getCode()
  {
    return $this->code;
  }
  /**
   * [Output Only] Metadata about this warning in key: value format. For
   * example:
   *
   * "data": [   {    "key": "scope",    "value": "zones/us-east1-d"   }
   *
   * @param StoragePoolTypeListWarningData[] $data
   */
  public function setData($data)
  {
    $this->data = $data;
  }
  /**
   * @return StoragePoolTypeListWarningData[]
   */
  public function getData()
  {
    return $this->data;
  }
  /**
   * [Output Only] A human-readable description of the warning code.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(StoragePoolTypeListWarning::class, 'Google_Service_Compute_StoragePoolTypeListWarning');
