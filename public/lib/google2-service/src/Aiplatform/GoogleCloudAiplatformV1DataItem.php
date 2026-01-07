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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DataItem extends \Google\Model
{
  /**
   * Output only. Timestamp when this DataItem was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. The labels with user-defined metadata to organize your DataItems.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. No more than
   * 64 user labels can be associated with one DataItem(System labels are
   * excluded). See https://goo.gl/xmQnxf for more information and examples of
   * labels. System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The resource name of the DataItem.
   *
   * @var string
   */
  public $name;
  /**
   * Required. The data that the DataItem represents (for example, an image or a
   * text snippet). The schema of the payload is stored in the parent Dataset's
   * metadata schema's dataItemSchemaUri field.
   *
   * @var array
   */
  public $payload;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzi;
  /**
   * Output only. Reserved for future use.
   *
   * @var bool
   */
  public $satisfiesPzs;
  /**
   * Output only. Timestamp when this DataItem was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. Timestamp when this DataItem was created.
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
   * Optional. Used to perform consistent read-modify-write updates. If not set,
   * a blind "overwrite" update happens.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. The labels with user-defined metadata to organize your DataItems.
   * Label keys and values can be no longer than 64 characters (Unicode
   * codepoints), can only contain lowercase letters, numeric characters,
   * underscores and dashes. International characters are allowed. No more than
   * 64 user labels can be associated with one DataItem(System labels are
   * excluded). See https://goo.gl/xmQnxf for more information and examples of
   * labels. System reserved label keys are prefixed with
   * "aiplatform.googleapis.com/" and are immutable.
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
   * Output only. The resource name of the DataItem.
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
   * Required. The data that the DataItem represents (for example, an image or a
   * text snippet). The schema of the payload is stored in the parent Dataset's
   * metadata schema's dataItemSchemaUri field.
   *
   * @param array $payload
   */
  public function setPayload($payload)
  {
    $this->payload = $payload;
  }
  /**
   * @return array
   */
  public function getPayload()
  {
    return $this->payload;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzi
   */
  public function setSatisfiesPzi($satisfiesPzi)
  {
    $this->satisfiesPzi = $satisfiesPzi;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzi()
  {
    return $this->satisfiesPzi;
  }
  /**
   * Output only. Reserved for future use.
   *
   * @param bool $satisfiesPzs
   */
  public function setSatisfiesPzs($satisfiesPzs)
  {
    $this->satisfiesPzs = $satisfiesPzs;
  }
  /**
   * @return bool
   */
  public function getSatisfiesPzs()
  {
    return $this->satisfiesPzs;
  }
  /**
   * Output only. Timestamp when this DataItem was last updated.
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
class_alias(GoogleCloudAiplatformV1DataItem::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DataItem');
