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

namespace Google\Service\Parallelstore;

class Instance extends \Google\Collection
{
  /**
   * Default Deployment Type It is equivalent to SCRATCH
   */
  public const DEPLOYMENT_TYPE_DEPLOYMENT_TYPE_UNSPECIFIED = 'DEPLOYMENT_TYPE_UNSPECIFIED';
  /**
   * Scratch
   */
  public const DEPLOYMENT_TYPE_SCRATCH = 'SCRATCH';
  /**
   * Persistent
   */
  public const DEPLOYMENT_TYPE_PERSISTENT = 'PERSISTENT';
  /**
   * If not set, DirectoryStripeLevel will default to DIRECTORY_STRIPE_LEVEL_MAX
   */
  public const DIRECTORY_STRIPE_LEVEL_DIRECTORY_STRIPE_LEVEL_UNSPECIFIED = 'DIRECTORY_STRIPE_LEVEL_UNSPECIFIED';
  /**
   * Minimum directory striping
   */
  public const DIRECTORY_STRIPE_LEVEL_DIRECTORY_STRIPE_LEVEL_MIN = 'DIRECTORY_STRIPE_LEVEL_MIN';
  /**
   * Medium directory striping
   */
  public const DIRECTORY_STRIPE_LEVEL_DIRECTORY_STRIPE_LEVEL_BALANCED = 'DIRECTORY_STRIPE_LEVEL_BALANCED';
  /**
   * Maximum directory striping
   */
  public const DIRECTORY_STRIPE_LEVEL_DIRECTORY_STRIPE_LEVEL_MAX = 'DIRECTORY_STRIPE_LEVEL_MAX';
  /**
   * If not set, FileStripeLevel will default to FILE_STRIPE_LEVEL_BALANCED
   */
  public const FILE_STRIPE_LEVEL_FILE_STRIPE_LEVEL_UNSPECIFIED = 'FILE_STRIPE_LEVEL_UNSPECIFIED';
  /**
   * Minimum file striping
   */
  public const FILE_STRIPE_LEVEL_FILE_STRIPE_LEVEL_MIN = 'FILE_STRIPE_LEVEL_MIN';
  /**
   * Medium file striping
   */
  public const FILE_STRIPE_LEVEL_FILE_STRIPE_LEVEL_BALANCED = 'FILE_STRIPE_LEVEL_BALANCED';
  /**
   * Maximum file striping
   */
  public const FILE_STRIPE_LEVEL_FILE_STRIPE_LEVEL_MAX = 'FILE_STRIPE_LEVEL_MAX';
  /**
   * Not set.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The instance is being created.
   */
  public const STATE_CREATING = 'CREATING';
  /**
   * The instance is available for use.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The instance is being deleted.
   */
  public const STATE_DELETING = 'DELETING';
  /**
   * The instance is not usable.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The instance is being upgraded.
   */
  public const STATE_UPGRADING = 'UPGRADING';
  /**
   * The instance is being repaired. This should only be used by instances using
   * the `PERSISTENT` deployment type.
   */
  public const STATE_REPAIRING = 'REPAIRING';
  protected $collection_key = 'accessPoints';
  /**
   * Output only. A list of IPv4 addresses used for client side configuration.
   *
   * @var string[]
   */
  public $accessPoints;
  /**
   * Required. Immutable. The instance's storage capacity in Gibibytes (GiB).
   * Allowed values are between 12000 and 100000, in multiples of 4000; e.g.,
   * 12000, 16000, 20000, ...
   *
   * @var string
   */
  public $capacityGib;
  /**
   * Output only. The time when the instance was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. Deprecated: The version of DAOS software running in the
   * instance.
   *
   * @deprecated
   * @var string
   */
  public $daosVersion;
  /**
   * Optional. Immutable. The deployment type of the instance. Allowed values
   * are: * `SCRATCH`: the instance is a scratch instance. * `PERSISTENT`: the
   * instance is a persistent instance.
   *
   * @var string
   */
  public $deploymentType;
  /**
   * Optional. The description of the instance. 2048 characters or less.
   *
   * @var string
   */
  public $description;
  /**
   * Optional. Immutable. Stripe level for directories. Allowed values are: *
   * `DIRECTORY_STRIPE_LEVEL_MIN`: recommended when directories contain a small
   * number of files. * `DIRECTORY_STRIPE_LEVEL_BALANCED`: balances performance
   * for workloads involving a mix of small and large directories. *
   * `DIRECTORY_STRIPE_LEVEL_MAX`: recommended for directories with a large
   * number of files.
   *
   * @var string
   */
  public $directoryStripeLevel;
  /**
   * Output only. Immutable. The ID of the IP address range being used by the
   * instance's VPC network. This field is populated by the service and contains
   * the value currently used by the service.
   *
   * @var string
   */
  public $effectiveReservedIpRange;
  /**
   * Optional. Immutable. Stripe level for files. Allowed values are: *
   * `FILE_STRIPE_LEVEL_MIN`: offers the best performance for small size files.
   * * `FILE_STRIPE_LEVEL_BALANCED`: balances performance for workloads
   * involving a mix of small and large files. * `FILE_STRIPE_LEVEL_MAX`: higher
   * throughput performance for larger files.
   *
   * @var string
   */
  public $fileStripeLevel;
  /**
   * Optional. Cloud Labels are a flexible and lightweight mechanism for
   * organizing cloud resources into groups that reflect a customer's
   * organizational needs and deployment strategies. See
   * https://cloud.google.com/resource-manager/docs/labels-overview for details.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the instance, in the format
   * `projects/{project}/locations/{location}/instances/{instance_id}`.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. Immutable. The name of the Compute Engine [VPC
   * network](https://cloud.google.com/vpc/docs/vpc) to which the instance is
   * connected.
   *
   * @var string
   */
  public $network;
  /**
   * Optional. Immutable. The ID of the IP address range being used by the
   * instance's VPC network. See [Configure a VPC network](https://cloud.google.
   * com/parallelstore/docs/vpc#create_and_configure_the_vpc). If no ID is
   * provided, all ranges are considered.
   *
   * @var string
   */
  public $reservedIpRange;
  /**
   * Output only. The instance state.
   *
   * @var string
   */
  public $state;
  /**
   * Output only. The time when the instance was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. A list of IPv4 addresses used for client side configuration.
   *
   * @param string[] $accessPoints
   */
  public function setAccessPoints($accessPoints)
  {
    $this->accessPoints = $accessPoints;
  }
  /**
   * @return string[]
   */
  public function getAccessPoints()
  {
    return $this->accessPoints;
  }
  /**
   * Required. Immutable. The instance's storage capacity in Gibibytes (GiB).
   * Allowed values are between 12000 and 100000, in multiples of 4000; e.g.,
   * 12000, 16000, 20000, ...
   *
   * @param string $capacityGib
   */
  public function setCapacityGib($capacityGib)
  {
    $this->capacityGib = $capacityGib;
  }
  /**
   * @return string
   */
  public function getCapacityGib()
  {
    return $this->capacityGib;
  }
  /**
   * Output only. The time when the instance was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Output only. Deprecated: The version of DAOS software running in the
   * instance.
   *
   * @deprecated
   * @param string $daosVersion
   */
  public function setDaosVersion($daosVersion)
  {
    $this->daosVersion = $daosVersion;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getDaosVersion()
  {
    return $this->daosVersion;
  }
  /**
   * Optional. Immutable. The deployment type of the instance. Allowed values
   * are: * `SCRATCH`: the instance is a scratch instance. * `PERSISTENT`: the
   * instance is a persistent instance.
   *
   * Accepted values: DEPLOYMENT_TYPE_UNSPECIFIED, SCRATCH, PERSISTENT
   *
   * @param self::DEPLOYMENT_TYPE_* $deploymentType
   */
  public function setDeploymentType($deploymentType)
  {
    $this->deploymentType = $deploymentType;
  }
  /**
   * @return self::DEPLOYMENT_TYPE_*
   */
  public function getDeploymentType()
  {
    return $this->deploymentType;
  }
  /**
   * Optional. The description of the instance. 2048 characters or less.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * Optional. Immutable. Stripe level for directories. Allowed values are: *
   * `DIRECTORY_STRIPE_LEVEL_MIN`: recommended when directories contain a small
   * number of files. * `DIRECTORY_STRIPE_LEVEL_BALANCED`: balances performance
   * for workloads involving a mix of small and large directories. *
   * `DIRECTORY_STRIPE_LEVEL_MAX`: recommended for directories with a large
   * number of files.
   *
   * Accepted values: DIRECTORY_STRIPE_LEVEL_UNSPECIFIED,
   * DIRECTORY_STRIPE_LEVEL_MIN, DIRECTORY_STRIPE_LEVEL_BALANCED,
   * DIRECTORY_STRIPE_LEVEL_MAX
   *
   * @param self::DIRECTORY_STRIPE_LEVEL_* $directoryStripeLevel
   */
  public function setDirectoryStripeLevel($directoryStripeLevel)
  {
    $this->directoryStripeLevel = $directoryStripeLevel;
  }
  /**
   * @return self::DIRECTORY_STRIPE_LEVEL_*
   */
  public function getDirectoryStripeLevel()
  {
    return $this->directoryStripeLevel;
  }
  /**
   * Output only. Immutable. The ID of the IP address range being used by the
   * instance's VPC network. This field is populated by the service and contains
   * the value currently used by the service.
   *
   * @param string $effectiveReservedIpRange
   */
  public function setEffectiveReservedIpRange($effectiveReservedIpRange)
  {
    $this->effectiveReservedIpRange = $effectiveReservedIpRange;
  }
  /**
   * @return string
   */
  public function getEffectiveReservedIpRange()
  {
    return $this->effectiveReservedIpRange;
  }
  /**
   * Optional. Immutable. Stripe level for files. Allowed values are: *
   * `FILE_STRIPE_LEVEL_MIN`: offers the best performance for small size files.
   * * `FILE_STRIPE_LEVEL_BALANCED`: balances performance for workloads
   * involving a mix of small and large files. * `FILE_STRIPE_LEVEL_MAX`: higher
   * throughput performance for larger files.
   *
   * Accepted values: FILE_STRIPE_LEVEL_UNSPECIFIED, FILE_STRIPE_LEVEL_MIN,
   * FILE_STRIPE_LEVEL_BALANCED, FILE_STRIPE_LEVEL_MAX
   *
   * @param self::FILE_STRIPE_LEVEL_* $fileStripeLevel
   */
  public function setFileStripeLevel($fileStripeLevel)
  {
    $this->fileStripeLevel = $fileStripeLevel;
  }
  /**
   * @return self::FILE_STRIPE_LEVEL_*
   */
  public function getFileStripeLevel()
  {
    return $this->fileStripeLevel;
  }
  /**
   * Optional. Cloud Labels are a flexible and lightweight mechanism for
   * organizing cloud resources into groups that reflect a customer's
   * organizational needs and deployment strategies. See
   * https://cloud.google.com/resource-manager/docs/labels-overview for details.
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
   * Identifier. The resource name of the instance, in the format
   * `projects/{project}/locations/{location}/instances/{instance_id}`.
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
   * Optional. Immutable. The name of the Compute Engine [VPC
   * network](https://cloud.google.com/vpc/docs/vpc) to which the instance is
   * connected.
   *
   * @param string $network
   */
  public function setNetwork($network)
  {
    $this->network = $network;
  }
  /**
   * @return string
   */
  public function getNetwork()
  {
    return $this->network;
  }
  /**
   * Optional. Immutable. The ID of the IP address range being used by the
   * instance's VPC network. See [Configure a VPC network](https://cloud.google.
   * com/parallelstore/docs/vpc#create_and_configure_the_vpc). If no ID is
   * provided, all ranges are considered.
   *
   * @param string $reservedIpRange
   */
  public function setReservedIpRange($reservedIpRange)
  {
    $this->reservedIpRange = $reservedIpRange;
  }
  /**
   * @return string
   */
  public function getReservedIpRange()
  {
    return $this->reservedIpRange;
  }
  /**
   * Output only. The instance state.
   *
   * Accepted values: STATE_UNSPECIFIED, CREATING, ACTIVE, DELETING, FAILED,
   * UPGRADING, REPAIRING
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
   * Output only. The time when the instance was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Instance::class, 'Google_Service_Parallelstore_Instance');
