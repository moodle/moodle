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

namespace Google\Service\CloudRedis;

class DatabaseResourceId extends \Google\Model
{
  public const PROVIDER_PROVIDER_UNSPECIFIED = 'PROVIDER_UNSPECIFIED';
  /**
   * Google cloud platform provider
   */
  public const PROVIDER_GCP = 'GCP';
  /**
   * Amazon web service
   */
  public const PROVIDER_AWS = 'AWS';
  /**
   * Azure web service
   */
  public const PROVIDER_AZURE = 'AZURE';
  /**
   * On-prem database resources.
   */
  public const PROVIDER_ONPREM = 'ONPREM';
  /**
   * Self-managed database provider. These are resources on a cloud platform,
   * e.g., database resource installed in a GCE VM, but not a managed database
   * service.
   */
  public const PROVIDER_SELFMANAGED = 'SELFMANAGED';
  /**
   * For the rest of the other categories. Other refers to the rest of other
   * database service providers, this could be smaller cloud provider. This
   * needs to be provided when the provider is known, but it is not present in
   * the existing set of enum values.
   */
  public const PROVIDER_PROVIDER_OTHER = 'PROVIDER_OTHER';
  /**
   * Required. Cloud provider name. Ex: GCP/AWS/Azure/OnPrem/SelfManaged
   *
   * @var string
   */
  public $provider;
  /**
   * Optional. Needs to be used only when the provider is PROVIDER_OTHER.
   *
   * @var string
   */
  public $providerDescription;
  /**
   * Required. The type of resource this ID is identifying. Ex go/keep-sorted
   * start alloydb.googleapis.com/Cluster, alloydb.googleapis.com/Instance,
   * bigtableadmin.googleapis.com/Cluster, bigtableadmin.googleapis.com/Instance
   * compute.googleapis.com/Instance firestore.googleapis.com/Database,
   * redis.googleapis.com/Instance, redis.googleapis.com/Cluster,
   * oracledatabase.googleapis.com/CloudExadataInfrastructure
   * oracledatabase.googleapis.com/CloudVmCluster
   * oracledatabase.googleapis.com/AutonomousDatabase
   * spanner.googleapis.com/Instance, spanner.googleapis.com/Database,
   * sqladmin.googleapis.com/Instance, go/keep-sorted end REQUIRED Please refer
   * go/condor-common-datamodel
   *
   * @var string
   */
  public $resourceType;
  /**
   * Required. A service-local token that distinguishes this resource from other
   * resources within the same service.
   *
   * @var string
   */
  public $uniqueId;

  /**
   * Required. Cloud provider name. Ex: GCP/AWS/Azure/OnPrem/SelfManaged
   *
   * Accepted values: PROVIDER_UNSPECIFIED, GCP, AWS, AZURE, ONPREM,
   * SELFMANAGED, PROVIDER_OTHER
   *
   * @param self::PROVIDER_* $provider
   */
  public function setProvider($provider)
  {
    $this->provider = $provider;
  }
  /**
   * @return self::PROVIDER_*
   */
  public function getProvider()
  {
    return $this->provider;
  }
  /**
   * Optional. Needs to be used only when the provider is PROVIDER_OTHER.
   *
   * @param string $providerDescription
   */
  public function setProviderDescription($providerDescription)
  {
    $this->providerDescription = $providerDescription;
  }
  /**
   * @return string
   */
  public function getProviderDescription()
  {
    return $this->providerDescription;
  }
  /**
   * Required. The type of resource this ID is identifying. Ex go/keep-sorted
   * start alloydb.googleapis.com/Cluster, alloydb.googleapis.com/Instance,
   * bigtableadmin.googleapis.com/Cluster, bigtableadmin.googleapis.com/Instance
   * compute.googleapis.com/Instance firestore.googleapis.com/Database,
   * redis.googleapis.com/Instance, redis.googleapis.com/Cluster,
   * oracledatabase.googleapis.com/CloudExadataInfrastructure
   * oracledatabase.googleapis.com/CloudVmCluster
   * oracledatabase.googleapis.com/AutonomousDatabase
   * spanner.googleapis.com/Instance, spanner.googleapis.com/Database,
   * sqladmin.googleapis.com/Instance, go/keep-sorted end REQUIRED Please refer
   * go/condor-common-datamodel
   *
   * @param string $resourceType
   */
  public function setResourceType($resourceType)
  {
    $this->resourceType = $resourceType;
  }
  /**
   * @return string
   */
  public function getResourceType()
  {
    return $this->resourceType;
  }
  /**
   * Required. A service-local token that distinguishes this resource from other
   * resources within the same service.
   *
   * @param string $uniqueId
   */
  public function setUniqueId($uniqueId)
  {
    $this->uniqueId = $uniqueId;
  }
  /**
   * @return string
   */
  public function getUniqueId()
  {
    return $this->uniqueId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DatabaseResourceId::class, 'Google_Service_CloudRedis_DatabaseResourceId');
