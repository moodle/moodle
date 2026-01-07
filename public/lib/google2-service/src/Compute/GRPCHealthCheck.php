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

class GRPCHealthCheck extends \Google\Model
{
  /**
   * The port number in the health check's port is used for health checking.
   * Applies to network endpoint group and instance group backends.
   */
  public const PORT_SPECIFICATION_USE_FIXED_PORT = 'USE_FIXED_PORT';
  /**
   * Not supported.
   */
  public const PORT_SPECIFICATION_USE_NAMED_PORT = 'USE_NAMED_PORT';
  /**
   * For network endpoint group backends, the health check uses the port number
   * specified on each endpoint in the network endpoint group. For instance
   * group backends, the health check uses the port number specified for the
   * backend service's named port defined in the instance group's named ports.
   */
  public const PORT_SPECIFICATION_USE_SERVING_PORT = 'USE_SERVING_PORT';
  /**
   * The gRPC service name for the health check. This field is optional. The
   * value of grpc_service_name has the following meanings by convention:
   *
   * - Empty service_name means the overall status of all services at the
   * backend.
   *
   * - Non-empty service_name means the health of that gRPC service, as defined
   * by the owner of the service.
   *
   * The grpc_service_name can only be ASCII.
   *
   * @var string
   */
  public $grpcServiceName;
  /**
   * The TCP port number to which the health check prober sends packets. Valid
   * values are 1 through 65535.
   *
   * @var int
   */
  public $port;
  /**
   * Not supported.
   *
   * @var string
   */
  public $portName;
  /**
   * Specifies how a port is selected for health checking. Can be one of the
   * following values:  USE_FIXED_PORT: Specifies a port number explicitly using
   * theport field  in the health check. Supported by backend services for
   * passthrough load balancers and backend services for proxy load balancers.
   * Not supported by target pools. The health check supports all backends
   * supported by the backend service provided the backend can be health
   * checked. For example, GCE_VM_IP network endpoint groups, GCE_VM_IP_PORT
   * network endpoint groups, and instance group backends.   USE_NAMED_PORT: Not
   * supported.  USE_SERVING_PORT: Provides an indirect method of specifying the
   * health check port by referring to the backend service. Only supported by
   * backend services for proxy load balancers. Not supported by target pools.
   * Not supported by backend services for passthrough load balancers. Supports
   * all backends that can be health checked; for example,GCE_VM_IP_PORT network
   * endpoint groups and instance group backends.
   *
   * For GCE_VM_IP_PORT network endpoint group backends, the health check uses
   * the port number specified for each endpoint in the network endpoint group.
   * For instance group backends, the health check uses the port number
   * determined by looking up the backend service's named port in the instance
   * group's list of named ports.
   *
   * @var string
   */
  public $portSpecification;

  /**
   * The gRPC service name for the health check. This field is optional. The
   * value of grpc_service_name has the following meanings by convention:
   *
   * - Empty service_name means the overall status of all services at the
   * backend.
   *
   * - Non-empty service_name means the health of that gRPC service, as defined
   * by the owner of the service.
   *
   * The grpc_service_name can only be ASCII.
   *
   * @param string $grpcServiceName
   */
  public function setGrpcServiceName($grpcServiceName)
  {
    $this->grpcServiceName = $grpcServiceName;
  }
  /**
   * @return string
   */
  public function getGrpcServiceName()
  {
    return $this->grpcServiceName;
  }
  /**
   * The TCP port number to which the health check prober sends packets. Valid
   * values are 1 through 65535.
   *
   * @param int $port
   */
  public function setPort($port)
  {
    $this->port = $port;
  }
  /**
   * @return int
   */
  public function getPort()
  {
    return $this->port;
  }
  /**
   * Not supported.
   *
   * @param string $portName
   */
  public function setPortName($portName)
  {
    $this->portName = $portName;
  }
  /**
   * @return string
   */
  public function getPortName()
  {
    return $this->portName;
  }
  /**
   * Specifies how a port is selected for health checking. Can be one of the
   * following values:  USE_FIXED_PORT: Specifies a port number explicitly using
   * theport field  in the health check. Supported by backend services for
   * passthrough load balancers and backend services for proxy load balancers.
   * Not supported by target pools. The health check supports all backends
   * supported by the backend service provided the backend can be health
   * checked. For example, GCE_VM_IP network endpoint groups, GCE_VM_IP_PORT
   * network endpoint groups, and instance group backends.   USE_NAMED_PORT: Not
   * supported.  USE_SERVING_PORT: Provides an indirect method of specifying the
   * health check port by referring to the backend service. Only supported by
   * backend services for proxy load balancers. Not supported by target pools.
   * Not supported by backend services for passthrough load balancers. Supports
   * all backends that can be health checked; for example,GCE_VM_IP_PORT network
   * endpoint groups and instance group backends.
   *
   * For GCE_VM_IP_PORT network endpoint group backends, the health check uses
   * the port number specified for each endpoint in the network endpoint group.
   * For instance group backends, the health check uses the port number
   * determined by looking up the backend service's named port in the instance
   * group's list of named ports.
   *
   * Accepted values: USE_FIXED_PORT, USE_NAMED_PORT, USE_SERVING_PORT
   *
   * @param self::PORT_SPECIFICATION_* $portSpecification
   */
  public function setPortSpecification($portSpecification)
  {
    $this->portSpecification = $portSpecification;
  }
  /**
   * @return self::PORT_SPECIFICATION_*
   */
  public function getPortSpecification()
  {
    return $this->portSpecification;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GRPCHealthCheck::class, 'Google_Service_Compute_GRPCHealthCheck');
