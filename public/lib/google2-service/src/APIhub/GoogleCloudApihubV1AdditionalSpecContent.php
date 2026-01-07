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

namespace Google\Service\APIhub;

class GoogleCloudApihubV1AdditionalSpecContent extends \Google\Model
{
  /**
   * Unspecified spec content type. Defaults to spec content uploaded by the
   * user.
   */
  public const SPEC_CONTENT_TYPE_SPEC_CONTENT_TYPE_UNSPECIFIED = 'SPEC_CONTENT_TYPE_UNSPECIFIED';
  /**
   * The spec content type for boosted spec.
   */
  public const SPEC_CONTENT_TYPE_BOOSTED_SPEC_CONTENT = 'BOOSTED_SPEC_CONTENT';
  /**
   * The spec content type for OpenAPI spec. This enum is used for OpenAPI specs
   * ingested via APIGEE X Gateway.
   */
  public const SPEC_CONTENT_TYPE_GATEWAY_OPEN_API_SPEC = 'GATEWAY_OPEN_API_SPEC';
  /**
   * Output only. The time at which the spec content was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The labels of the spec content e.g. specboost addon version.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Required. The type of the spec content.
   *
   * @var string
   */
  public $specContentType;
  protected $specContentsType = GoogleCloudApihubV1SpecContents::class;
  protected $specContentsDataType = '';
  /**
   * Output only. The time at which the spec content was last updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Output only. The time at which the spec content was created.
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
   * Optional. The labels of the spec content e.g. specboost addon version.
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
   * Required. The type of the spec content.
   *
   * Accepted values: SPEC_CONTENT_TYPE_UNSPECIFIED, BOOSTED_SPEC_CONTENT,
   * GATEWAY_OPEN_API_SPEC
   *
   * @param self::SPEC_CONTENT_TYPE_* $specContentType
   */
  public function setSpecContentType($specContentType)
  {
    $this->specContentType = $specContentType;
  }
  /**
   * @return self::SPEC_CONTENT_TYPE_*
   */
  public function getSpecContentType()
  {
    return $this->specContentType;
  }
  /**
   * Optional. The additional spec contents.
   *
   * @param GoogleCloudApihubV1SpecContents $specContents
   */
  public function setSpecContents(GoogleCloudApihubV1SpecContents $specContents)
  {
    $this->specContents = $specContents;
  }
  /**
   * @return GoogleCloudApihubV1SpecContents
   */
  public function getSpecContents()
  {
    return $this->specContents;
  }
  /**
   * Output only. The time at which the spec content was last updated.
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
class_alias(GoogleCloudApihubV1AdditionalSpecContent::class, 'Google_Service_APIhub_GoogleCloudApihubV1AdditionalSpecContent');
