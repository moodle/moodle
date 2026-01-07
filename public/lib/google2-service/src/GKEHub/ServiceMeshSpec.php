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

namespace Google\Service\GKEHub;

class ServiceMeshSpec extends \Google\Model
{
  /**
   * Unspecified
   */
  public const CONFIG_API_CONFIG_API_UNSPECIFIED = 'CONFIG_API_UNSPECIFIED';
  /**
   * Use the Istio API for configuration.
   */
  public const CONFIG_API_CONFIG_API_ISTIO = 'CONFIG_API_ISTIO';
  /**
   * Use the K8s Gateway API for configuration.
   */
  public const CONFIG_API_CONFIG_API_GATEWAY = 'CONFIG_API_GATEWAY';
  /**
   * Unspecified
   */
  public const CONTROL_PLANE_CONTROL_PLANE_MANAGEMENT_UNSPECIFIED = 'CONTROL_PLANE_MANAGEMENT_UNSPECIFIED';
  /**
   * Google should provision a control plane revision and make it available in
   * the cluster. Google will enroll this revision in a release channel and keep
   * it up to date. The control plane revision may be a managed service, or a
   * managed install.
   */
  public const CONTROL_PLANE_AUTOMATIC = 'AUTOMATIC';
  /**
   * User will manually configure the control plane (e.g. via CLI, or via the
   * ControlPlaneRevision KRM API)
   */
  public const CONTROL_PLANE_MANUAL = 'MANUAL';
  /**
   * Unspecified
   */
  public const DEFAULT_CHANNEL_CHANNEL_UNSPECIFIED = 'CHANNEL_UNSPECIFIED';
  /**
   * RAPID channel is offered on an early access basis for customers who want to
   * test new releases.
   */
  public const DEFAULT_CHANNEL_RAPID = 'RAPID';
  /**
   * REGULAR channel is intended for production users who want to take advantage
   * of new features.
   */
  public const DEFAULT_CHANNEL_REGULAR = 'REGULAR';
  /**
   * STABLE channel includes versions that are known to be stable and reliable
   * in production.
   */
  public const DEFAULT_CHANNEL_STABLE = 'STABLE';
  /**
   * Unspecified.
   */
  public const MANAGEMENT_MANAGEMENT_UNSPECIFIED = 'MANAGEMENT_UNSPECIFIED';
  /**
   * Google should manage my Service Mesh for the cluster.
   */
  public const MANAGEMENT_MANAGEMENT_AUTOMATIC = 'MANAGEMENT_AUTOMATIC';
  /**
   * User will manually configure their service mesh components.
   */
  public const MANAGEMENT_MANAGEMENT_MANUAL = 'MANAGEMENT_MANUAL';
  /**
   * Google should remove any managed Service Mesh components from this cluster
   * and deprovision any resources.
   */
  public const MANAGEMENT_MANAGEMENT_NOT_INSTALLED = 'MANAGEMENT_NOT_INSTALLED';
  /**
   * Optional. Specifies the API that will be used for configuring the mesh
   * workloads.
   *
   * @var string
   */
  public $configApi;
  /**
   * Deprecated: use `management` instead Enables automatic control plane
   * management.
   *
   * @deprecated
   * @var string
   */
  public $controlPlane;
  /**
   * Determines which release channel to use for default injection and service
   * mesh APIs.
   *
   * @deprecated
   * @var string
   */
  public $defaultChannel;
  /**
   * Optional. Enables automatic Service Mesh management.
   *
   * @var string
   */
  public $management;

  /**
   * Optional. Specifies the API that will be used for configuring the mesh
   * workloads.
   *
   * Accepted values: CONFIG_API_UNSPECIFIED, CONFIG_API_ISTIO,
   * CONFIG_API_GATEWAY
   *
   * @param self::CONFIG_API_* $configApi
   */
  public function setConfigApi($configApi)
  {
    $this->configApi = $configApi;
  }
  /**
   * @return self::CONFIG_API_*
   */
  public function getConfigApi()
  {
    return $this->configApi;
  }
  /**
   * Deprecated: use `management` instead Enables automatic control plane
   * management.
   *
   * Accepted values: CONTROL_PLANE_MANAGEMENT_UNSPECIFIED, AUTOMATIC, MANUAL
   *
   * @deprecated
   * @param self::CONTROL_PLANE_* $controlPlane
   */
  public function setControlPlane($controlPlane)
  {
    $this->controlPlane = $controlPlane;
  }
  /**
   * @deprecated
   * @return self::CONTROL_PLANE_*
   */
  public function getControlPlane()
  {
    return $this->controlPlane;
  }
  /**
   * Determines which release channel to use for default injection and service
   * mesh APIs.
   *
   * Accepted values: CHANNEL_UNSPECIFIED, RAPID, REGULAR, STABLE
   *
   * @deprecated
   * @param self::DEFAULT_CHANNEL_* $defaultChannel
   */
  public function setDefaultChannel($defaultChannel)
  {
    $this->defaultChannel = $defaultChannel;
  }
  /**
   * @deprecated
   * @return self::DEFAULT_CHANNEL_*
   */
  public function getDefaultChannel()
  {
    return $this->defaultChannel;
  }
  /**
   * Optional. Enables automatic Service Mesh management.
   *
   * Accepted values: MANAGEMENT_UNSPECIFIED, MANAGEMENT_AUTOMATIC,
   * MANAGEMENT_MANUAL, MANAGEMENT_NOT_INSTALLED
   *
   * @param self::MANAGEMENT_* $management
   */
  public function setManagement($management)
  {
    $this->management = $management;
  }
  /**
   * @return self::MANAGEMENT_*
   */
  public function getManagement()
  {
    return $this->management;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ServiceMeshSpec::class, 'Google_Service_GKEHub_ServiceMeshSpec');
