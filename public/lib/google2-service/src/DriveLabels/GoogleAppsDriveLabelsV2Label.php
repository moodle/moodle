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

namespace Google\Service\DriveLabels;

class GoogleAppsDriveLabelsV2Label extends \Google\Collection
{
  /**
   * Unknown label type.
   */
  public const LABEL_TYPE_LABEL_TYPE_UNSPECIFIED = 'LABEL_TYPE_UNSPECIFIED';
  /**
   * Shared labels may be shared with users to apply to Drive items.
   */
  public const LABEL_TYPE_SHARED = 'SHARED';
  /**
   * Admin-owned label. Only creatable and editable by admins. Supports some
   * additional admin-only features.
   */
  public const LABEL_TYPE_ADMIN = 'ADMIN';
  /**
   * A label owned by an internal Google application rather than a customer.
   * These labels are read-only.
   */
  public const LABEL_TYPE_GOOGLE_APP = 'GOOGLE_APP';
  protected $collection_key = 'fields';
  protected $appliedCapabilitiesType = GoogleAppsDriveLabelsV2LabelAppliedCapabilities::class;
  protected $appliedCapabilitiesDataType = '';
  protected $appliedLabelPolicyType = GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy::class;
  protected $appliedLabelPolicyDataType = '';
  /**
   * Output only. The time this label was created.
   *
   * @var string
   */
  public $createTime;
  protected $creatorType = GoogleAppsDriveLabelsV2UserInfo::class;
  protected $creatorDataType = '';
  /**
   * Output only. The customer this label belongs to. For example:
   * `customers/123abc789`.
   *
   * @var string
   */
  public $customer;
  /**
   * Output only. The time this label was disabled. This value has no meaning
   * when the label isn't disabled.
   *
   * @var string
   */
  public $disableTime;
  protected $disablerType = GoogleAppsDriveLabelsV2UserInfo::class;
  protected $disablerDataType = '';
  protected $displayHintsType = GoogleAppsDriveLabelsV2LabelDisplayHints::class;
  protected $displayHintsDataType = '';
  protected $enabledAppSettingsType = GoogleAppsDriveLabelsV2LabelEnabledAppSettings::class;
  protected $enabledAppSettingsDataType = '';
  protected $fieldsType = GoogleAppsDriveLabelsV2Field::class;
  protected $fieldsDataType = 'array';
  /**
   * Output only. Globally unique identifier of this label. ID makes up part of
   * the label `name`, but unlike `name`, ID is consistent between revisions.
   * Matches the regex: `([a-zA-Z0-9])+`.
   *
   * @var string
   */
  public $id;
  /**
   * Required. The type of label.
   *
   * @var string
   */
  public $labelType;
  /**
   * Custom URL to present to users to allow them to learn more about this label
   * and how it should be used.
   *
   * @var string
   */
  public $learnMoreUri;
  protected $lifecycleType = GoogleAppsDriveLabelsV2Lifecycle::class;
  protected $lifecycleDataType = '';
  protected $lockStatusType = GoogleAppsDriveLabelsV2LockStatus::class;
  protected $lockStatusDataType = '';
  /**
   * Output only. Resource name of the label. Will be in the form of either:
   * `labels/{id}` or `labels/{id}@{revision_id}` depending on the request. See
   * `id` and `revision_id` below.
   *
   * @var string
   */
  public $name;
  protected $propertiesType = GoogleAppsDriveLabelsV2LabelProperties::class;
  protected $propertiesDataType = '';
  /**
   * Output only. The time this label was published. This value has no meaning
   * when the label isn't published.
   *
   * @var string
   */
  public $publishTime;
  protected $publisherType = GoogleAppsDriveLabelsV2UserInfo::class;
  protected $publisherDataType = '';
  /**
   * Output only. The time this label revision was created.
   *
   * @var string
   */
  public $revisionCreateTime;
  protected $revisionCreatorType = GoogleAppsDriveLabelsV2UserInfo::class;
  protected $revisionCreatorDataType = '';
  /**
   * Output only. Revision ID of the label. Revision ID might be part of the
   * label `name` depending on the request issued. A new revision is created
   * whenever revisioned properties of a label are changed. Matches the regex:
   * `([a-zA-Z0-9])+`.
   *
   * @var string
   */
  public $revisionId;
  protected $schemaCapabilitiesType = GoogleAppsDriveLabelsV2LabelSchemaCapabilities::class;
  protected $schemaCapabilitiesDataType = '';

  /**
   * Output only. The capabilities related to this label on applied metadata.
   *
   * @param GoogleAppsDriveLabelsV2LabelAppliedCapabilities $appliedCapabilities
   */
  public function setAppliedCapabilities(GoogleAppsDriveLabelsV2LabelAppliedCapabilities $appliedCapabilities)
  {
    $this->appliedCapabilities = $appliedCapabilities;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelAppliedCapabilities
   */
  public function getAppliedCapabilities()
  {
    return $this->appliedCapabilities;
  }
  /**
   * Output only. Behavior of this label when it's applied to Drive items.
   *
   * @param GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy $appliedLabelPolicy
   */
  public function setAppliedLabelPolicy(GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy $appliedLabelPolicy)
  {
    $this->appliedLabelPolicy = $appliedLabelPolicy;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelAppliedLabelPolicy
   */
  public function getAppliedLabelPolicy()
  {
    return $this->appliedLabelPolicy;
  }
  /**
   * Output only. The time this label was created.
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
   * Output only. The user who created this label.
   *
   * @param GoogleAppsDriveLabelsV2UserInfo $creator
   */
  public function setCreator(GoogleAppsDriveLabelsV2UserInfo $creator)
  {
    $this->creator = $creator;
  }
  /**
   * @return GoogleAppsDriveLabelsV2UserInfo
   */
  public function getCreator()
  {
    return $this->creator;
  }
  /**
   * Output only. The customer this label belongs to. For example:
   * `customers/123abc789`.
   *
   * @param string $customer
   */
  public function setCustomer($customer)
  {
    $this->customer = $customer;
  }
  /**
   * @return string
   */
  public function getCustomer()
  {
    return $this->customer;
  }
  /**
   * Output only. The time this label was disabled. This value has no meaning
   * when the label isn't disabled.
   *
   * @param string $disableTime
   */
  public function setDisableTime($disableTime)
  {
    $this->disableTime = $disableTime;
  }
  /**
   * @return string
   */
  public function getDisableTime()
  {
    return $this->disableTime;
  }
  /**
   * Output only. The user who disabled this label. This value has no meaning
   * when the label isn't disabled.
   *
   * @param GoogleAppsDriveLabelsV2UserInfo $disabler
   */
  public function setDisabler(GoogleAppsDriveLabelsV2UserInfo $disabler)
  {
    $this->disabler = $disabler;
  }
  /**
   * @return GoogleAppsDriveLabelsV2UserInfo
   */
  public function getDisabler()
  {
    return $this->disabler;
  }
  /**
   * Output only. UI display hints for rendering the label.
   *
   * @param GoogleAppsDriveLabelsV2LabelDisplayHints $displayHints
   */
  public function setDisplayHints(GoogleAppsDriveLabelsV2LabelDisplayHints $displayHints)
  {
    $this->displayHints = $displayHints;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelDisplayHints
   */
  public function getDisplayHints()
  {
    return $this->displayHints;
  }
  /**
   * Optional. The `EnabledAppSettings` for this Label.
   *
   * @param GoogleAppsDriveLabelsV2LabelEnabledAppSettings $enabledAppSettings
   */
  public function setEnabledAppSettings(GoogleAppsDriveLabelsV2LabelEnabledAppSettings $enabledAppSettings)
  {
    $this->enabledAppSettings = $enabledAppSettings;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelEnabledAppSettings
   */
  public function getEnabledAppSettings()
  {
    return $this->enabledAppSettings;
  }
  /**
   * List of fields in descending priority order.
   *
   * @param GoogleAppsDriveLabelsV2Field[] $fields
   */
  public function setFields($fields)
  {
    $this->fields = $fields;
  }
  /**
   * @return GoogleAppsDriveLabelsV2Field[]
   */
  public function getFields()
  {
    return $this->fields;
  }
  /**
   * Output only. Globally unique identifier of this label. ID makes up part of
   * the label `name`, but unlike `name`, ID is consistent between revisions.
   * Matches the regex: `([a-zA-Z0-9])+`.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Required. The type of label.
   *
   * Accepted values: LABEL_TYPE_UNSPECIFIED, SHARED, ADMIN, GOOGLE_APP
   *
   * @param self::LABEL_TYPE_* $labelType
   */
  public function setLabelType($labelType)
  {
    $this->labelType = $labelType;
  }
  /**
   * @return self::LABEL_TYPE_*
   */
  public function getLabelType()
  {
    return $this->labelType;
  }
  /**
   * Custom URL to present to users to allow them to learn more about this label
   * and how it should be used.
   *
   * @param string $learnMoreUri
   */
  public function setLearnMoreUri($learnMoreUri)
  {
    $this->learnMoreUri = $learnMoreUri;
  }
  /**
   * @return string
   */
  public function getLearnMoreUri()
  {
    return $this->learnMoreUri;
  }
  /**
   * Output only. The lifecycle state of the label including whether it's
   * published, deprecated, and has draft changes.
   *
   * @param GoogleAppsDriveLabelsV2Lifecycle $lifecycle
   */
  public function setLifecycle(GoogleAppsDriveLabelsV2Lifecycle $lifecycle)
  {
    $this->lifecycle = $lifecycle;
  }
  /**
   * @return GoogleAppsDriveLabelsV2Lifecycle
   */
  public function getLifecycle()
  {
    return $this->lifecycle;
  }
  /**
   * Output only. The `LockStatus` of this label.
   *
   * @param GoogleAppsDriveLabelsV2LockStatus $lockStatus
   */
  public function setLockStatus(GoogleAppsDriveLabelsV2LockStatus $lockStatus)
  {
    $this->lockStatus = $lockStatus;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LockStatus
   */
  public function getLockStatus()
  {
    return $this->lockStatus;
  }
  /**
   * Output only. Resource name of the label. Will be in the form of either:
   * `labels/{id}` or `labels/{id}@{revision_id}` depending on the request. See
   * `id` and `revision_id` below.
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
   * Required. The basic properties of the label.
   *
   * @param GoogleAppsDriveLabelsV2LabelProperties $properties
   */
  public function setProperties(GoogleAppsDriveLabelsV2LabelProperties $properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelProperties
   */
  public function getProperties()
  {
    return $this->properties;
  }
  /**
   * Output only. The time this label was published. This value has no meaning
   * when the label isn't published.
   *
   * @param string $publishTime
   */
  public function setPublishTime($publishTime)
  {
    $this->publishTime = $publishTime;
  }
  /**
   * @return string
   */
  public function getPublishTime()
  {
    return $this->publishTime;
  }
  /**
   * Output only. The user who published this label. This value has no meaning
   * when the label isn't published.>>
   *
   * @param GoogleAppsDriveLabelsV2UserInfo $publisher
   */
  public function setPublisher(GoogleAppsDriveLabelsV2UserInfo $publisher)
  {
    $this->publisher = $publisher;
  }
  /**
   * @return GoogleAppsDriveLabelsV2UserInfo
   */
  public function getPublisher()
  {
    return $this->publisher;
  }
  /**
   * Output only. The time this label revision was created.
   *
   * @param string $revisionCreateTime
   */
  public function setRevisionCreateTime($revisionCreateTime)
  {
    $this->revisionCreateTime = $revisionCreateTime;
  }
  /**
   * @return string
   */
  public function getRevisionCreateTime()
  {
    return $this->revisionCreateTime;
  }
  /**
   * Output only. The user who created this label revision.
   *
   * @param GoogleAppsDriveLabelsV2UserInfo $revisionCreator
   */
  public function setRevisionCreator(GoogleAppsDriveLabelsV2UserInfo $revisionCreator)
  {
    $this->revisionCreator = $revisionCreator;
  }
  /**
   * @return GoogleAppsDriveLabelsV2UserInfo
   */
  public function getRevisionCreator()
  {
    return $this->revisionCreator;
  }
  /**
   * Output only. Revision ID of the label. Revision ID might be part of the
   * label `name` depending on the request issued. A new revision is created
   * whenever revisioned properties of a label are changed. Matches the regex:
   * `([a-zA-Z0-9])+`.
   *
   * @param string $revisionId
   */
  public function setRevisionId($revisionId)
  {
    $this->revisionId = $revisionId;
  }
  /**
   * @return string
   */
  public function getRevisionId()
  {
    return $this->revisionId;
  }
  /**
   * Output only. The capabilities the user has on this label.
   *
   * @param GoogleAppsDriveLabelsV2LabelSchemaCapabilities $schemaCapabilities
   */
  public function setSchemaCapabilities(GoogleAppsDriveLabelsV2LabelSchemaCapabilities $schemaCapabilities)
  {
    $this->schemaCapabilities = $schemaCapabilities;
  }
  /**
   * @return GoogleAppsDriveLabelsV2LabelSchemaCapabilities
   */
  public function getSchemaCapabilities()
  {
    return $this->schemaCapabilities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAppsDriveLabelsV2Label::class, 'Google_Service_DriveLabels_GoogleAppsDriveLabelsV2Label');
