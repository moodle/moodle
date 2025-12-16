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

class FeatureSpec extends \Google\Model
{
  protected $cloudbuildType = CloudBuildSpec::class;
  protected $cloudbuildDataType = '';
  protected $configmanagementType = ConfigManagementSpec::class;
  protected $configmanagementDataType = '';
  protected $identityserviceType = IdentityServiceSpec::class;
  protected $identityserviceDataType = '';
  protected $originType = Origin::class;
  protected $originDataType = '';
  protected $policycontrollerType = PolicyControllerSpec::class;
  protected $policycontrollerDataType = '';
  protected $rbacrolebindingactuationType = RBACRoleBindingActuationSpec::class;
  protected $rbacrolebindingactuationDataType = '';
  protected $servicemeshType = ServiceMeshSpec::class;
  protected $servicemeshDataType = '';
  protected $workloadcertificateType = WorkloadCertificateSpec::class;
  protected $workloadcertificateDataType = '';

  /**
   * Cloudbuild-specific FeatureSpec.
   *
   * @param CloudBuildSpec $cloudbuild
   */
  public function setCloudbuild(CloudBuildSpec $cloudbuild)
  {
    $this->cloudbuild = $cloudbuild;
  }
  /**
   * @return CloudBuildSpec
   */
  public function getCloudbuild()
  {
    return $this->cloudbuild;
  }
  /**
   * Config Management FeatureSpec.
   *
   * @param ConfigManagementSpec $configmanagement
   */
  public function setConfigmanagement(ConfigManagementSpec $configmanagement)
  {
    $this->configmanagement = $configmanagement;
  }
  /**
   * @return ConfigManagementSpec
   */
  public function getConfigmanagement()
  {
    return $this->configmanagement;
  }
  /**
   * IdentityService FeatureSpec.
   *
   * @param IdentityServiceSpec $identityservice
   */
  public function setIdentityservice(IdentityServiceSpec $identityservice)
  {
    $this->identityservice = $identityservice;
  }
  /**
   * @return IdentityServiceSpec
   */
  public function getIdentityservice()
  {
    return $this->identityservice;
  }
  /**
   * Whether this per-Feature spec was inherited from a fleet-level default.
   * This field can be updated by users by either overriding a Feature config
   * (updated to USER implicitly) or setting to FLEET explicitly.
   *
   * @param Origin $origin
   */
  public function setOrigin(Origin $origin)
  {
    $this->origin = $origin;
  }
  /**
   * @return Origin
   */
  public function getOrigin()
  {
    return $this->origin;
  }
  /**
   * Policycontroller-specific FeatureSpec.
   *
   * @param PolicyControllerSpec $policycontroller
   */
  public function setPolicycontroller(PolicyControllerSpec $policycontroller)
  {
    $this->policycontroller = $policycontroller;
  }
  /**
   * @return PolicyControllerSpec
   */
  public function getPolicycontroller()
  {
    return $this->policycontroller;
  }
  /**
   * Rbacrolebindingactuation-specific FeatureSpec.
   *
   * @param RBACRoleBindingActuationSpec $rbacrolebindingactuation
   */
  public function setRbacrolebindingactuation(RBACRoleBindingActuationSpec $rbacrolebindingactuation)
  {
    $this->rbacrolebindingactuation = $rbacrolebindingactuation;
  }
  /**
   * @return RBACRoleBindingActuationSpec
   */
  public function getRbacrolebindingactuation()
  {
    return $this->rbacrolebindingactuation;
  }
  /**
   * ServiceMesh Feature Spec.
   *
   * @param ServiceMeshSpec $servicemesh
   */
  public function setServicemesh(ServiceMeshSpec $servicemesh)
  {
    $this->servicemesh = $servicemesh;
  }
  /**
   * @return ServiceMeshSpec
   */
  public function getServicemesh()
  {
    return $this->servicemesh;
  }
  /**
   * Workloadcertificate-specific FeatureSpec.
   *
   * @param WorkloadCertificateSpec $workloadcertificate
   */
  public function setWorkloadcertificate(WorkloadCertificateSpec $workloadcertificate)
  {
    $this->workloadcertificate = $workloadcertificate;
  }
  /**
   * @return WorkloadCertificateSpec
   */
  public function getWorkloadcertificate()
  {
    return $this->workloadcertificate;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(FeatureSpec::class, 'Google_Service_GKEHub_FeatureSpec');
