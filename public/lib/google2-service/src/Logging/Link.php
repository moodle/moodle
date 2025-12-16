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

namespace Google\Service\Logging;

class Link extends \Google\Model
{
  /**
   * Unspecified state. This is only used/useful for distinguishing unset
   * values.
   */
  public const LIFECYCLE_STATE_LIFECYCLE_STATE_UNSPECIFIED = 'LIFECYCLE_STATE_UNSPECIFIED';
  /**
   * The normal and active state.
   */
  public const LIFECYCLE_STATE_ACTIVE = 'ACTIVE';
  /**
   * The resource has been marked for deletion by the user. For some resources
   * (e.g. buckets), this can be reversed by an un-delete operation.
   */
  public const LIFECYCLE_STATE_DELETE_REQUESTED = 'DELETE_REQUESTED';
  /**
   * The resource has been marked for an update by the user. It will remain in
   * this state until the update is complete.
   */
  public const LIFECYCLE_STATE_UPDATING = 'UPDATING';
  /**
   * The resource has been marked for creation by the user. It will remain in
   * this state until the creation is complete.
   */
  public const LIFECYCLE_STATE_CREATING = 'CREATING';
  /**
   * The resource is in an INTERNAL error state.
   */
  public const LIFECYCLE_STATE_FAILED = 'FAILED';
  protected $bigqueryDatasetType = BigQueryDataset::class;
  protected $bigqueryDatasetDataType = '';
  /**
   * Output only. The creation timestamp of the link.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Describes this link.The maximum length of the description is 8000
   * characters.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. The resource lifecycle state.
   *
   * @var string
   */
  public $lifecycleState;
  /**
   * Output only. The resource name of the link. The name can have up to 100
   * characters. A valid link id (at the end of the link name) must only have
   * alphanumeric characters and underscores within it. "projects/[PROJECT_ID]/l
   * ocations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK_ID]" "organizations/
   * [ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK_I
   * D]" "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets/[
   * BUCKET_ID]/links/[LINK_ID]" "folders/[FOLDER_ID]/locations/[LOCATION_ID]/bu
   * ckets/[BUCKET_ID]/links/[LINK_ID]" For example:`projects/my-
   * project/locations/global/buckets/my-bucket/links/my_link
   *
   * @var string
   */
  public $name;

  /**
   * Optional. The information of a BigQuery Dataset. When a link is created, a
   * BigQuery dataset is created along with it, in the same project as the
   * LogBucket it's linked to. This dataset will also have BigQuery Views
   * corresponding to the LogViews in the bucket.
   *
   * @param BigQueryDataset $bigqueryDataset
   */
  public function setBigqueryDataset(BigQueryDataset $bigqueryDataset)
  {
    $this->bigqueryDataset = $bigqueryDataset;
  }
  /**
   * @return BigQueryDataset
   */
  public function getBigqueryDataset()
  {
    return $this->bigqueryDataset;
  }
  /**
   * Output only. The creation timestamp of the link.
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
   * Optional. Describes this link.The maximum length of the description is 8000
   * characters.
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
   * Output only. The resource lifecycle state.
   *
   * Accepted values: LIFECYCLE_STATE_UNSPECIFIED, ACTIVE, DELETE_REQUESTED,
   * UPDATING, CREATING, FAILED
   *
   * @param self::LIFECYCLE_STATE_* $lifecycleState
   */
  public function setLifecycleState($lifecycleState)
  {
    $this->lifecycleState = $lifecycleState;
  }
  /**
   * @return self::LIFECYCLE_STATE_*
   */
  public function getLifecycleState()
  {
    return $this->lifecycleState;
  }
  /**
   * Output only. The resource name of the link. The name can have up to 100
   * characters. A valid link id (at the end of the link name) must only have
   * alphanumeric characters and underscores within it. "projects/[PROJECT_ID]/l
   * ocations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK_ID]" "organizations/
   * [ORGANIZATION_ID]/locations/[LOCATION_ID]/buckets/[BUCKET_ID]/links/[LINK_I
   * D]" "billingAccounts/[BILLING_ACCOUNT_ID]/locations/[LOCATION_ID]/buckets/[
   * BUCKET_ID]/links/[LINK_ID]" "folders/[FOLDER_ID]/locations/[LOCATION_ID]/bu
   * ckets/[BUCKET_ID]/links/[LINK_ID]" For example:`projects/my-
   * project/locations/global/buckets/my-bucket/links/my_link
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Link::class, 'Google_Service_Logging_Link');
