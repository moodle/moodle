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

namespace Google\Service\Dfareporting;

class Element extends \Google\Collection
{
  protected $collection_key = 'feedFields';
  /**
   * Optional. The field ID to specify the active field in the feed.
   *
   * @var int
   */
  public $activeFieldId;
  protected $createInfoType = LastModifiedInfo::class;
  protected $createInfoDataType = '';
  /**
   * Optional. The field ID to specify the field that represents the default
   * field in the feed.
   *
   * @var int
   */
  public $defaultFieldId;
  /**
   * Optional. The name of the element. It is defaulted to resource file name if
   * not provided.
   *
   * @var string
   */
  public $elementName;
  /**
   * Optional. The field ID to specify the field that represents the end
   * timestamp. Only applicable if you're planning to use scheduling in your
   * dynamic creative.
   *
   * @var int
   */
  public $endTimestampFieldId;
  /**
   * Required. The field ID to specify the field used for uniquely identifying
   * the feed row. This is a required field.
   *
   * @var int
   */
  public $externalIdFieldId;
  protected $feedFieldsType = FeedField::class;
  protected $feedFieldsDataType = 'array';
  /**
   * Optional. Whether the start and end timestamp is local timestamp. The
   * default value is false which means start and end timestamp is in UTC.
   *
   * @var bool
   */
  public $isLocalTimestamp;
  protected $lastModifiedInfoType = LastModifiedInfo::class;
  protected $lastModifiedInfoDataType = '';
  /**
   * Optional. The field ID that specify field used for proximity targeting.
   *
   * @var int
   */
  public $proximityTargetingFieldId;
  /**
   * Required. The field ID to specify the field used for dynamic reporting in
   * Campaign Manager 360.
   *
   * @var int
   */
  public $reportingLabelFieldId;
  /**
   * Optional. The field ID to specify the field that represents the start
   * timestamp. Only applicable if you're planning to use scheduling in your
   * dynamic creative.
   *
   * @var int
   */
  public $startTimestampFieldId;

  /**
   * Optional. The field ID to specify the active field in the feed.
   *
   * @param int $activeFieldId
   */
  public function setActiveFieldId($activeFieldId)
  {
    $this->activeFieldId = $activeFieldId;
  }
  /**
   * @return int
   */
  public function getActiveFieldId()
  {
    return $this->activeFieldId;
  }
  /**
   * Output only. The creation timestamp of the element. This is a read-only
   * field.
   *
   * @param LastModifiedInfo $createInfo
   */
  public function setCreateInfo(LastModifiedInfo $createInfo)
  {
    $this->createInfo = $createInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getCreateInfo()
  {
    return $this->createInfo;
  }
  /**
   * Optional. The field ID to specify the field that represents the default
   * field in the feed.
   *
   * @param int $defaultFieldId
   */
  public function setDefaultFieldId($defaultFieldId)
  {
    $this->defaultFieldId = $defaultFieldId;
  }
  /**
   * @return int
   */
  public function getDefaultFieldId()
  {
    return $this->defaultFieldId;
  }
  /**
   * Optional. The name of the element. It is defaulted to resource file name if
   * not provided.
   *
   * @param string $elementName
   */
  public function setElementName($elementName)
  {
    $this->elementName = $elementName;
  }
  /**
   * @return string
   */
  public function getElementName()
  {
    return $this->elementName;
  }
  /**
   * Optional. The field ID to specify the field that represents the end
   * timestamp. Only applicable if you're planning to use scheduling in your
   * dynamic creative.
   *
   * @param int $endTimestampFieldId
   */
  public function setEndTimestampFieldId($endTimestampFieldId)
  {
    $this->endTimestampFieldId = $endTimestampFieldId;
  }
  /**
   * @return int
   */
  public function getEndTimestampFieldId()
  {
    return $this->endTimestampFieldId;
  }
  /**
   * Required. The field ID to specify the field used for uniquely identifying
   * the feed row. This is a required field.
   *
   * @param int $externalIdFieldId
   */
  public function setExternalIdFieldId($externalIdFieldId)
  {
    $this->externalIdFieldId = $externalIdFieldId;
  }
  /**
   * @return int
   */
  public function getExternalIdFieldId()
  {
    return $this->externalIdFieldId;
  }
  /**
   * Required. The list of fields of the element. The field order and name
   * should match the meta data in the content source source.
   *
   * @param FeedField[] $feedFields
   */
  public function setFeedFields($feedFields)
  {
    $this->feedFields = $feedFields;
  }
  /**
   * @return FeedField[]
   */
  public function getFeedFields()
  {
    return $this->feedFields;
  }
  /**
   * Optional. Whether the start and end timestamp is local timestamp. The
   * default value is false which means start and end timestamp is in UTC.
   *
   * @param bool $isLocalTimestamp
   */
  public function setIsLocalTimestamp($isLocalTimestamp)
  {
    $this->isLocalTimestamp = $isLocalTimestamp;
  }
  /**
   * @return bool
   */
  public function getIsLocalTimestamp()
  {
    return $this->isLocalTimestamp;
  }
  /**
   * Output only. The last modified timestamp of the element. This is a read-
   * only field.
   *
   * @param LastModifiedInfo $lastModifiedInfo
   */
  public function setLastModifiedInfo(LastModifiedInfo $lastModifiedInfo)
  {
    $this->lastModifiedInfo = $lastModifiedInfo;
  }
  /**
   * @return LastModifiedInfo
   */
  public function getLastModifiedInfo()
  {
    return $this->lastModifiedInfo;
  }
  /**
   * Optional. The field ID that specify field used for proximity targeting.
   *
   * @param int $proximityTargetingFieldId
   */
  public function setProximityTargetingFieldId($proximityTargetingFieldId)
  {
    $this->proximityTargetingFieldId = $proximityTargetingFieldId;
  }
  /**
   * @return int
   */
  public function getProximityTargetingFieldId()
  {
    return $this->proximityTargetingFieldId;
  }
  /**
   * Required. The field ID to specify the field used for dynamic reporting in
   * Campaign Manager 360.
   *
   * @param int $reportingLabelFieldId
   */
  public function setReportingLabelFieldId($reportingLabelFieldId)
  {
    $this->reportingLabelFieldId = $reportingLabelFieldId;
  }
  /**
   * @return int
   */
  public function getReportingLabelFieldId()
  {
    return $this->reportingLabelFieldId;
  }
  /**
   * Optional. The field ID to specify the field that represents the start
   * timestamp. Only applicable if you're planning to use scheduling in your
   * dynamic creative.
   *
   * @param int $startTimestampFieldId
   */
  public function setStartTimestampFieldId($startTimestampFieldId)
  {
    $this->startTimestampFieldId = $startTimestampFieldId;
  }
  /**
   * @return int
   */
  public function getStartTimestampFieldId()
  {
    return $this->startTimestampFieldId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Element::class, 'Google_Service_Dfareporting_Element');
