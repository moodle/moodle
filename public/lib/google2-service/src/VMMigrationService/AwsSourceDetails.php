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

namespace Google\Service\VMMigrationService;

class AwsSourceDetails extends \Google\Collection
{
  /**
   * The state is unknown. This is used for API compatibility only and is not
   * used by the system.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The state was not sampled by the health checks yet.
   */
  public const STATE_PENDING = 'PENDING';
  /**
   * The source is available but might not be usable yet due to invalid
   * credentials or another reason. The error message will contain further
   * details.
   */
  public const STATE_FAILED = 'FAILED';
  /**
   * The source exists and its credentials were verified.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  protected $collection_key = 'inventoryTagList';
  protected $accessKeyCredsType = AccessKeyCredentials::class;
  protected $accessKeyCredsDataType = '';
  /**
   * Immutable. The AWS region that the source VMs will be migrated from.
   *
   * @var string
   */
  public $awsRegion;
  protected $errorType = Status::class;
  protected $errorDataType = '';
  /**
   * AWS security group names to limit the scope of the source inventory.
   *
   * @var string[]
   */
  public $inventorySecurityGroupNames;
  protected $inventoryTagListType = Tag::class;
  protected $inventoryTagListDataType = 'array';
  /**
   * User specified tags to add to every M2VM generated resource in AWS. These
   * tags will be set in addition to the default tags that are set as part of
   * the migration process. The tags must not begin with the reserved prefix
   * `m2vm`.
   *
   * @var string[]
   */
  public $migrationResourcesUserTags;
  /**
   * Output only. The source's public IP. All communication initiated by this
   * source will originate from this IP.
   *
   * @var string
   */
  public $publicIp;
  /**
   * Output only. State of the source as determined by the health check.
   *
   * @var string
   */
  public $state;

  /**
   * AWS Credentials using access key id and secret.
   *
   * @param AccessKeyCredentials $accessKeyCreds
   */
  public function setAccessKeyCreds(AccessKeyCredentials $accessKeyCreds)
  {
    $this->accessKeyCreds = $accessKeyCreds;
  }
  /**
   * @return AccessKeyCredentials
   */
  public function getAccessKeyCreds()
  {
    return $this->accessKeyCreds;
  }
  /**
   * Immutable. The AWS region that the source VMs will be migrated from.
   *
   * @param string $awsRegion
   */
  public function setAwsRegion($awsRegion)
  {
    $this->awsRegion = $awsRegion;
  }
  /**
   * @return string
   */
  public function getAwsRegion()
  {
    return $this->awsRegion;
  }
  /**
   * Output only. Provides details on the state of the Source in case of an
   * error.
   *
   * @param Status $error
   */
  public function setError(Status $error)
  {
    $this->error = $error;
  }
  /**
   * @return Status
   */
  public function getError()
  {
    return $this->error;
  }
  /**
   * AWS security group names to limit the scope of the source inventory.
   *
   * @param string[] $inventorySecurityGroupNames
   */
  public function setInventorySecurityGroupNames($inventorySecurityGroupNames)
  {
    $this->inventorySecurityGroupNames = $inventorySecurityGroupNames;
  }
  /**
   * @return string[]
   */
  public function getInventorySecurityGroupNames()
  {
    return $this->inventorySecurityGroupNames;
  }
  /**
   * AWS resource tags to limit the scope of the source inventory.
   *
   * @param Tag[] $inventoryTagList
   */
  public function setInventoryTagList($inventoryTagList)
  {
    $this->inventoryTagList = $inventoryTagList;
  }
  /**
   * @return Tag[]
   */
  public function getInventoryTagList()
  {
    return $this->inventoryTagList;
  }
  /**
   * User specified tags to add to every M2VM generated resource in AWS. These
   * tags will be set in addition to the default tags that are set as part of
   * the migration process. The tags must not begin with the reserved prefix
   * `m2vm`.
   *
   * @param string[] $migrationResourcesUserTags
   */
  public function setMigrationResourcesUserTags($migrationResourcesUserTags)
  {
    $this->migrationResourcesUserTags = $migrationResourcesUserTags;
  }
  /**
   * @return string[]
   */
  public function getMigrationResourcesUserTags()
  {
    return $this->migrationResourcesUserTags;
  }
  /**
   * Output only. The source's public IP. All communication initiated by this
   * source will originate from this IP.
   *
   * @param string $publicIp
   */
  public function setPublicIp($publicIp)
  {
    $this->publicIp = $publicIp;
  }
  /**
   * @return string
   */
  public function getPublicIp()
  {
    return $this->publicIp;
  }
  /**
   * Output only. State of the source as determined by the health check.
   *
   * Accepted values: STATE_UNSPECIFIED, PENDING, FAILED, ACTIVE
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AwsSourceDetails::class, 'Google_Service_VMMigrationService_AwsSourceDetails');
