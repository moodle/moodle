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

namespace Google\Service\Batch;

class AllocationPolicy extends \Google\Collection
{
  protected $collection_key = 'tags';
  protected $instancesType = InstancePolicyOrTemplate::class;
  protected $instancesDataType = 'array';
  /**
   * Custom labels to apply to the job and all the Compute Engine resources that
   * both are created by this allocation policy and support labels. Use labels
   * to group and describe the resources they are applied to. Batch
   * automatically applies predefined labels and supports multiple `labels`
   * fields for each job, which each let you apply custom labels to various
   * resources. Label names that start with "goog-" or "google-" are reserved
   * for predefined labels. For more information about labels with Batch, see
   * [Organize resources using
   * labels](https://cloud.google.com/batch/docs/organize-resources-using-
   * labels).
   *
   * @var string[]
   */
  public $labels;
  protected $locationType = LocationPolicy::class;
  protected $locationDataType = '';
  protected $networkType = NetworkPolicy::class;
  protected $networkDataType = '';
  protected $placementType = PlacementPolicy::class;
  protected $placementDataType = '';
  protected $serviceAccountType = ServiceAccount::class;
  protected $serviceAccountDataType = '';
  /**
   * Optional. Tags applied to the VM instances. The tags identify valid sources
   * or targets for network firewalls. Each tag must be 1-63 characters long,
   * and comply with [RFC1035](https://www.ietf.org/rfc/rfc1035.txt).
   *
   * @var string[]
   */
  public $tags;

  /**
   * Describe instances that can be created by this AllocationPolicy. Only
   * instances[0] is supported now.
   *
   * @param InstancePolicyOrTemplate[] $instances
   */
  public function setInstances($instances)
  {
    $this->instances = $instances;
  }
  /**
   * @return InstancePolicyOrTemplate[]
   */
  public function getInstances()
  {
    return $this->instances;
  }
  /**
   * Custom labels to apply to the job and all the Compute Engine resources that
   * both are created by this allocation policy and support labels. Use labels
   * to group and describe the resources they are applied to. Batch
   * automatically applies predefined labels and supports multiple `labels`
   * fields for each job, which each let you apply custom labels to various
   * resources. Label names that start with "goog-" or "google-" are reserved
   * for predefined labels. For more information about labels with Batch, see
   * [Organize resources using
   * labels](https://cloud.google.com/batch/docs/organize-resources-using-
   * labels).
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Location where compute resources should be allocated for the Job.
   *
   * @param LocationPolicy $location
   */
  public function setLocation(LocationPolicy $location)
  {
    $this->location = $location;
  }
  /**
   * @return LocationPolicy
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * The network policy. If you define an instance template in the
   * `InstancePolicyOrTemplate` field, Batch will use the network settings in
   * the instance template instead of this field.
   *
   * @param NetworkPolicy $network
   */
  public function setNetwork(NetworkPolicy $network)
  {
    $this->network = $network;
  }
  /**
   * @return NetworkPolicy
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * The placement policy.
   *
   * @param PlacementPolicy $placement
   */
  public function setPlacement(PlacementPolicy $placement)
  {
    $this->placement = $placement;
  }
  /**
   * @return PlacementPolicy
   */
  public function getPlacement()
  {
    return $this->placement;
  }
  /**
   * Defines the service account for Batch-created VMs. If omitted, the [default
   * Compute Engine service
   * account](https://cloud.google.com/compute/docs/access/service-
   * accounts#default_service_account) is used. Must match the service account
   * specified in any used instance template configured in the Batch job.
   * Includes the following fields: * email: The service account's email
   * address. If not set, the default Compute Engine service account is used. *
   * scopes: Additional OAuth scopes to grant the service account, beyond the
   * default cloud-platform scope. (list of strings)
   *
   * @param ServiceAccount $serviceAccount
   */
  public function setServiceAccount(ServiceAccount $serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return ServiceAccount
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Optional. Tags applied to the VM instances. The tags identify valid sources
   * or targets for network firewalls. Each tag must be 1-63 characters long,
   * and comply with [RFC1035](https://www.ietf.org/rfc/rfc1035.txt).
   *
   * @param string[] $tags
   */
  public function setTags($tags)
  {
    $this->tags = $tags;
  }
  /**
   * @return string[]
   */
  public function getTags()
  {
    return $this->tags;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AllocationPolicy::class, 'Google_Service_Batch_AllocationPolicy');
