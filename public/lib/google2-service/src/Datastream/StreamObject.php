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

namespace Google\Service\Datastream;

class StreamObject extends \Google\Collection
{
  protected $collection_key = 'errors';
  protected $backfillJobType = BackfillJob::class;
  protected $backfillJobDataType = '';
  /**
   * Output only. The creation time of the object.
   *
   * @var string
   */
  public $createTime;
  protected $customizationRulesType = CustomizationRule::class;
  protected $customizationRulesDataType = 'array';
  /**
   * Required. Display name.
   *
   * @var string
   */
  public $displayName;
  protected $errorsType = Error::class;
  protected $errorsDataType = 'array';
  /**
   * Output only. Identifier. The object resource's name.
   *
   * @var string
   */
  public $name;
  protected $sourceObjectType = SourceObjectIdentifier::class;
  protected $sourceObjectDataType = '';
  /**
   * Output only. The last update time of the object.
   *
   * @var string
   */
  public $updateTime;

  /**
   * The latest backfill job that was initiated for the stream object.
   *
   * @param BackfillJob $backfillJob
   */
  public function setBackfillJob(BackfillJob $backfillJob)
  {
    $this->backfillJob = $backfillJob;
  }
  /**
   * @return BackfillJob
   */
  public function getBackfillJob()
  {
    return $this->backfillJob;
  }
  /**
   * Output only. The creation time of the object.
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
   * Output only. The customization rules for the object. These rules are
   * derived from the parent Stream's `rule_sets` and represent the intended
   * configuration for the object.
   *
   * @param CustomizationRule[] $customizationRules
   */
  public function setCustomizationRules($customizationRules)
  {
    $this->customizationRules = $customizationRules;
  }
  /**
   * @return CustomizationRule[]
   */
  public function getCustomizationRules()
  {
    return $this->customizationRules;
  }
  /**
   * Required. Display name.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. Active errors on the object.
   *
   * @param Error[] $errors
   */
  public function setErrors($errors)
  {
    $this->errors = $errors;
  }
  /**
   * @return Error[]
   */
  public function getErrors()
  {
    return $this->errors;
  }
  /**
   * Output only. Identifier. The object resource's name.
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
   * The object identifier in the data source.
   *
   * @param SourceObjectIdentifier $sourceObject
   */
  public function setSourceObject(SourceObjectIdentifier $sourceObject)
  {
    $this->sourceObject = $sourceObject;
  }
  /**
   * @return SourceObjectIdentifier
   */
  public function getSourceObject()
  {
    return $this->sourceObject;
  }
  /**
   * Output only. The last update time of the object.
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
class_alias(StreamObject::class, 'Google_Service_Datastream_StreamObject');
