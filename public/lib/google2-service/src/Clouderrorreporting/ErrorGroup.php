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

namespace Google\Service\Clouderrorreporting;

class ErrorGroup extends \Google\Collection
{
  /**
   * Status is unknown. When left unspecified in requests, it is treated like
   * OPEN.
   */
  public const RESOLUTION_STATUS_RESOLUTION_STATUS_UNSPECIFIED = 'RESOLUTION_STATUS_UNSPECIFIED';
  /**
   * The error group is not being addressed. This is the default for new groups.
   * It is also used for errors re-occurring after marked RESOLVED.
   */
  public const RESOLUTION_STATUS_OPEN = 'OPEN';
  /**
   * Error Group manually acknowledged, it can have an issue link attached.
   */
  public const RESOLUTION_STATUS_ACKNOWLEDGED = 'ACKNOWLEDGED';
  /**
   * Error Group manually resolved, more events for this group are not expected
   * to occur.
   */
  public const RESOLUTION_STATUS_RESOLVED = 'RESOLVED';
  /**
   * The error group is muted and excluded by default on group stats requests.
   */
  public const RESOLUTION_STATUS_MUTED = 'MUTED';
  protected $collection_key = 'trackingIssues';
  /**
   * An opaque identifier of the group. This field is assigned by the Error
   * Reporting system and always populated. In the group resource name, the
   * `group_id` is a unique identifier for a particular error group. The
   * identifier is derived from key parts of the error-log content and is
   * treated as Service Data. For information about how Service Data is handled,
   * see [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-
   * privacy-notice).
   *
   * @var string
   */
  public $groupId;
  /**
   * The group resource name. Written as
   * `projects/{projectID}/groups/{group_id}` or
   * `projects/{projectID}/locations/{location}/groups/{group_id}` Examples:
   * `projects/my-project-123/groups/my-group`, `projects/my-
   * project-123/locations/us-central1/groups/my-group` In the group resource
   * name, the `group_id` is a unique identifier for a particular error group.
   * The identifier is derived from key parts of the error-log content and is
   * treated as Service Data. For information about how Service Data is handled,
   * see [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-
   * privacy-notice). For a list of supported locations, see [Supported
   * Regions](https://cloud.google.com/logging/docs/region-support). `global` is
   * the default when unspecified.
   *
   * @var string
   */
  public $name;
  /**
   * Error group's resolution status. An unspecified resolution status will be
   * interpreted as OPEN
   *
   * @var string
   */
  public $resolutionStatus;
  protected $trackingIssuesType = TrackingIssue::class;
  protected $trackingIssuesDataType = 'array';

  /**
   * An opaque identifier of the group. This field is assigned by the Error
   * Reporting system and always populated. In the group resource name, the
   * `group_id` is a unique identifier for a particular error group. The
   * identifier is derived from key parts of the error-log content and is
   * treated as Service Data. For information about how Service Data is handled,
   * see [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-
   * privacy-notice).
   *
   * @param string $groupId
   */
  public function setGroupId($groupId)
  {
    $this->groupId = $groupId;
  }
  /**
   * @return string
   */
  public function getGroupId()
  {
    return $this->groupId;
  }
  /**
   * The group resource name. Written as
   * `projects/{projectID}/groups/{group_id}` or
   * `projects/{projectID}/locations/{location}/groups/{group_id}` Examples:
   * `projects/my-project-123/groups/my-group`, `projects/my-
   * project-123/locations/us-central1/groups/my-group` In the group resource
   * name, the `group_id` is a unique identifier for a particular error group.
   * The identifier is derived from key parts of the error-log content and is
   * treated as Service Data. For information about how Service Data is handled,
   * see [Google Cloud Privacy Notice](https://cloud.google.com/terms/cloud-
   * privacy-notice). For a list of supported locations, see [Supported
   * Regions](https://cloud.google.com/logging/docs/region-support). `global` is
   * the default when unspecified.
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
   * Error group's resolution status. An unspecified resolution status will be
   * interpreted as OPEN
   *
   * Accepted values: RESOLUTION_STATUS_UNSPECIFIED, OPEN, ACKNOWLEDGED,
   * RESOLVED, MUTED
   *
   * @param self::RESOLUTION_STATUS_* $resolutionStatus
   */
  public function setResolutionStatus($resolutionStatus)
  {
    $this->resolutionStatus = $resolutionStatus;
  }
  /**
   * @return self::RESOLUTION_STATUS_*
   */
  public function getResolutionStatus()
  {
    return $this->resolutionStatus;
  }
  /**
   * Associated tracking issues.
   *
   * @param TrackingIssue[] $trackingIssues
   */
  public function setTrackingIssues($trackingIssues)
  {
    $this->trackingIssues = $trackingIssues;
  }
  /**
   * @return TrackingIssue[]
   */
  public function getTrackingIssues()
  {
    return $this->trackingIssues;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ErrorGroup::class, 'Google_Service_Clouderrorreporting_ErrorGroup');
