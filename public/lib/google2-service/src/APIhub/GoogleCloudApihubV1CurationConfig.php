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

class GoogleCloudApihubV1CurationConfig extends \Google\Model
{
  /**
   * Default unspecified curation type.
   */
  public const CURATION_TYPE_CURATION_TYPE_UNSPECIFIED = 'CURATION_TYPE_UNSPECIFIED';
  /**
   * Default curation for API metadata will be used.
   */
  public const CURATION_TYPE_DEFAULT_CURATION_FOR_API_METADATA = 'DEFAULT_CURATION_FOR_API_METADATA';
  /**
   * Custom curation for API metadata will be used.
   */
  public const CURATION_TYPE_CUSTOM_CURATION_FOR_API_METADATA = 'CUSTOM_CURATION_FOR_API_METADATA';
  /**
   * Required. The curation type for this plugin instance.
   *
   * @var string
   */
  public $curationType;
  protected $customCurationType = GoogleCloudApihubV1CustomCuration::class;
  protected $customCurationDataType = '';

  /**
   * Required. The curation type for this plugin instance.
   *
   * Accepted values: CURATION_TYPE_UNSPECIFIED,
   * DEFAULT_CURATION_FOR_API_METADATA, CUSTOM_CURATION_FOR_API_METADATA
   *
   * @param self::CURATION_TYPE_* $curationType
   */
  public function setCurationType($curationType)
  {
    $this->curationType = $curationType;
  }
  /**
   * @return self::CURATION_TYPE_*
   */
  public function getCurationType()
  {
    return $this->curationType;
  }
  /**
   * Optional. Custom curation information for this plugin instance.
   *
   * @param GoogleCloudApihubV1CustomCuration $customCuration
   */
  public function setCustomCuration(GoogleCloudApihubV1CustomCuration $customCuration)
  {
    $this->customCuration = $customCuration;
  }
  /**
   * @return GoogleCloudApihubV1CustomCuration
   */
  public function getCustomCuration()
  {
    return $this->customCuration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApihubV1CurationConfig::class, 'Google_Service_APIhub_GoogleCloudApihubV1CurationConfig');
