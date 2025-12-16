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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV1p1beta1Finding extends \Google\Model
{
  /**
   * No severity specified. The default value.
   */
  public const SEVERITY_SEVERITY_UNSPECIFIED = 'SEVERITY_UNSPECIFIED';
  /**
   * Critical severity.
   */
  public const SEVERITY_CRITICAL = 'CRITICAL';
  /**
   * High severity.
   */
  public const SEVERITY_HIGH = 'HIGH';
  /**
   * Medium severity.
   */
  public const SEVERITY_MEDIUM = 'MEDIUM';
  /**
   * Low severity.
   */
  public const SEVERITY_LOW = 'LOW';
  /**
   * Unspecified state.
   */
  public const STATE_STATE_UNSPECIFIED = 'STATE_UNSPECIFIED';
  /**
   * The finding requires attention and has not been addressed yet.
   */
  public const STATE_ACTIVE = 'ACTIVE';
  /**
   * The finding has been fixed, triaged as a non-issue or otherwise addressed
   * and is no longer active.
   */
  public const STATE_INACTIVE = 'INACTIVE';
  /**
   * The canonical name of the finding. It's either "organizations/{organization
   * _id}/sources/{source_id}/findings/{finding_id}",
   * "folders/{folder_id}/sources/{source_id}/findings/{finding_id}" or
   * "projects/{project_number}/sources/{source_id}/findings/{finding_id}",
   * depending on the closest CRM ancestor of the resource associated with the
   * finding.
   *
   * @var string
   */
  public $canonicalName;
  /**
   * The additional taxonomy group within findings from a given source. This
   * field is immutable after creation time. Example: "XSS_FLASH_INJECTION"
   *
   * @var string
   */
  public $category;
  /**
   * The time at which the finding was created in Security Command Center.
   *
   * @var string
   */
  public $createTime;
  /**
   * The time at which the event took place, or when an update to the finding
   * occurred. For example, if the finding represents an open firewall it would
   * capture the time the detector believes the firewall became open. The
   * accuracy is determined by the detector. If the finding were to be resolved
   * afterward, this time would reflect when the finding was resolved. Must not
   * be set to a value greater than the current timestamp.
   *
   * @var string
   */
  public $eventTime;
  /**
   * The URI that, if available, points to a web page outside of Security
   * Command Center where additional information about the finding can be found.
   * This field is guaranteed to be either empty or a well formed URL.
   *
   * @var string
   */
  public $externalUri;
  /**
   * The relative resource name of this finding. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example:
   * "organizations/{organization_id}/sources/{source_id}/findings/{finding_id}"
   *
   * @var string
   */
  public $name;
  /**
   * The relative resource name of the source the finding belongs to. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * This field is immutable after creation time. For example:
   * "organizations/{organization_id}/sources/{source_id}"
   *
   * @var string
   */
  public $parent;
  /**
   * For findings on Google Cloud resources, the full resource name of the
   * Google Cloud resource this finding is for. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name When
   * the finding is for a non-Google Cloud resource, the resourceName can be a
   * customer or partner defined string. This field is immutable after creation
   * time.
   *
   * @var string
   */
  public $resourceName;
  protected $securityMarksType = GoogleCloudSecuritycenterV1p1beta1SecurityMarks::class;
  protected $securityMarksDataType = '';
  /**
   * The severity of the finding. This field is managed by the source that
   * writes the finding.
   *
   * @var string
   */
  public $severity;
  /**
   * Source specific properties. These properties are managed by the source that
   * writes the finding. The key names in the source_properties map must be
   * between 1 and 255 characters, and must start with a letter and contain
   * alphanumeric characters or underscores only.
   *
   * @var array[]
   */
  public $sourceProperties;
  /**
   * The state of the finding.
   *
   * @var string
   */
  public $state;

  /**
   * The canonical name of the finding. It's either "organizations/{organization
   * _id}/sources/{source_id}/findings/{finding_id}",
   * "folders/{folder_id}/sources/{source_id}/findings/{finding_id}" or
   * "projects/{project_number}/sources/{source_id}/findings/{finding_id}",
   * depending on the closest CRM ancestor of the resource associated with the
   * finding.
   *
   * @param string $canonicalName
   */
  public function setCanonicalName($canonicalName)
  {
    $this->canonicalName = $canonicalName;
  }
  /**
   * @return string
   */
  public function getCanonicalName()
  {
    return $this->canonicalName;
  }
  /**
   * The additional taxonomy group within findings from a given source. This
   * field is immutable after creation time. Example: "XSS_FLASH_INJECTION"
   *
   * @param string $category
   */
  public function setCategory($category)
  {
    $this->category = $category;
  }
  /**
   * @return string
   */
  public function getCategory()
  {
    return $this->category;
  }
  /**
   * The time at which the finding was created in Security Command Center.
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
   * The time at which the event took place, or when an update to the finding
   * occurred. For example, if the finding represents an open firewall it would
   * capture the time the detector believes the firewall became open. The
   * accuracy is determined by the detector. If the finding were to be resolved
   * afterward, this time would reflect when the finding was resolved. Must not
   * be set to a value greater than the current timestamp.
   *
   * @param string $eventTime
   */
  public function setEventTime($eventTime)
  {
    $this->eventTime = $eventTime;
  }
  /**
   * @return string
   */
  public function getEventTime()
  {
    return $this->eventTime;
  }
  /**
   * The URI that, if available, points to a web page outside of Security
   * Command Center where additional information about the finding can be found.
   * This field is guaranteed to be either empty or a well formed URL.
   *
   * @param string $externalUri
   */
  public function setExternalUri($externalUri)
  {
    $this->externalUri = $externalUri;
  }
  /**
   * @return string
   */
  public function getExternalUri()
  {
    return $this->externalUri;
  }
  /**
   * The relative resource name of this finding. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * Example:
   * "organizations/{organization_id}/sources/{source_id}/findings/{finding_id}"
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
   * The relative resource name of the source the finding belongs to. See:
   * https://cloud.google.com/apis/design/resource_names#relative_resource_name
   * This field is immutable after creation time. For example:
   * "organizations/{organization_id}/sources/{source_id}"
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * For findings on Google Cloud resources, the full resource name of the
   * Google Cloud resource this finding is for. See:
   * https://cloud.google.com/apis/design/resource_names#full_resource_name When
   * the finding is for a non-Google Cloud resource, the resourceName can be a
   * customer or partner defined string. This field is immutable after creation
   * time.
   *
   * @param string $resourceName
   */
  public function setResourceName($resourceName)
  {
    $this->resourceName = $resourceName;
  }
  /**
   * @return string
   */
  public function getResourceName()
  {
    return $this->resourceName;
  }
  /**
   * Output only. User specified security marks. These marks are entirely
   * managed by the user and come from the SecurityMarks resource that belongs
   * to the finding.
   *
   * @param GoogleCloudSecuritycenterV1p1beta1SecurityMarks $securityMarks
   */
  public function setSecurityMarks(GoogleCloudSecuritycenterV1p1beta1SecurityMarks $securityMarks)
  {
    $this->securityMarks = $securityMarks;
  }
  /**
   * @return GoogleCloudSecuritycenterV1p1beta1SecurityMarks
   */
  public function getSecurityMarks()
  {
    return $this->securityMarks;
  }
  /**
   * The severity of the finding. This field is managed by the source that
   * writes the finding.
   *
   * Accepted values: SEVERITY_UNSPECIFIED, CRITICAL, HIGH, MEDIUM, LOW
   *
   * @param self::SEVERITY_* $severity
   */
  public function setSeverity($severity)
  {
    $this->severity = $severity;
  }
  /**
   * @return self::SEVERITY_*
   */
  public function getSeverity()
  {
    return $this->severity;
  }
  /**
   * Source specific properties. These properties are managed by the source that
   * writes the finding. The key names in the source_properties map must be
   * between 1 and 255 characters, and must start with a letter and contain
   * alphanumeric characters or underscores only.
   *
   * @param array[] $sourceProperties
   */
  public function setSourceProperties($sourceProperties)
  {
    $this->sourceProperties = $sourceProperties;
  }
  /**
   * @return array[]
   */
  public function getSourceProperties()
  {
    return $this->sourceProperties;
  }
  /**
   * The state of the finding.
   *
   * Accepted values: STATE_UNSPECIFIED, ACTIVE, INACTIVE
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
class_alias(GoogleCloudSecuritycenterV1p1beta1Finding::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1p1beta1Finding');
