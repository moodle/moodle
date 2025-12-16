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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaChangeHistoryChange extends \Google\Model
{
  /**
   * Action type unknown or not specified.
   */
  public const ACTION_ACTION_TYPE_UNSPECIFIED = 'ACTION_TYPE_UNSPECIFIED';
  /**
   * Resource was created in this change.
   */
  public const ACTION_CREATED = 'CREATED';
  /**
   * Resource was updated in this change.
   */
  public const ACTION_UPDATED = 'UPDATED';
  /**
   * Resource was deleted in this change.
   */
  public const ACTION_DELETED = 'DELETED';
  /**
   * The type of action that changed this resource.
   *
   * @var string
   */
  public $action;
  /**
   * Resource name of the resource whose changes are described by this entry.
   *
   * @var string
   */
  public $resource;
  protected $resourceAfterChangeType = GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource::class;
  protected $resourceAfterChangeDataType = '';
  protected $resourceBeforeChangeType = GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource::class;
  protected $resourceBeforeChangeDataType = '';

  /**
   * The type of action that changed this resource.
   *
   * Accepted values: ACTION_TYPE_UNSPECIFIED, CREATED, UPDATED, DELETED
   *
   * @param self::ACTION_* $action
   */
  public function setAction($action)
  {
    $this->action = $action;
  }
  /**
   * @return self::ACTION_*
   */
  public function getAction()
  {
    return $this->action;
  }
  /**
   * Resource name of the resource whose changes are described by this entry.
   *
   * @param string $resource
   */
  public function setResource($resource)
  {
    $this->resource = $resource;
  }
  /**
   * @return string
   */
  public function getResource()
  {
    return $this->resource;
  }
  /**
   * Resource contents from after the change was made. If this resource was
   * deleted in this change, this field will be missing.
   *
   * @param GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource $resourceAfterChange
   */
  public function setResourceAfterChange(GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource $resourceAfterChange)
  {
    $this->resourceAfterChange = $resourceAfterChange;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource
   */
  public function getResourceAfterChange()
  {
    return $this->resourceAfterChange;
  }
  /**
   * Resource contents from before the change was made. If this resource was
   * created in this change, this field will be missing.
   *
   * @param GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource $resourceBeforeChange
   */
  public function setResourceBeforeChange(GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource $resourceBeforeChange)
  {
    $this->resourceBeforeChange = $resourceBeforeChange;
  }
  /**
   * @return GoogleAnalyticsAdminV1betaChangeHistoryChangeChangeHistoryResource
   */
  public function getResourceBeforeChange()
  {
    return $this->resourceBeforeChange;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaChangeHistoryChange::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaChangeHistoryChange');
