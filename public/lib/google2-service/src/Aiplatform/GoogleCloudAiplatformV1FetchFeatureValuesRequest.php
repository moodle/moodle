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

class GoogleCloudAiplatformV1FetchFeatureValuesRequest extends \Google\Model
{
  /**
   * Not set. Will be treated as the KeyValue format.
   */
  public const DATA_FORMAT_FEATURE_VIEW_DATA_FORMAT_UNSPECIFIED = 'FEATURE_VIEW_DATA_FORMAT_UNSPECIFIED';
  /**
   * Return response data in key-value format.
   */
  public const DATA_FORMAT_KEY_VALUE = 'KEY_VALUE';
  /**
   * Return response data in proto Struct format.
   */
  public const DATA_FORMAT_PROTO_STRUCT = 'PROTO_STRUCT';
  /**
   * Optional. Response data format. If not set, FeatureViewDataFormat.KEY_VALUE
   * will be used.
   *
   * @var string
   */
  public $dataFormat;
  protected $dataKeyType = GoogleCloudAiplatformV1FeatureViewDataKey::class;
  protected $dataKeyDataType = '';

  /**
   * Optional. Response data format. If not set, FeatureViewDataFormat.KEY_VALUE
   * will be used.
   *
   * Accepted values: FEATURE_VIEW_DATA_FORMAT_UNSPECIFIED, KEY_VALUE,
   * PROTO_STRUCT
   *
   * @param self::DATA_FORMAT_* $dataFormat
   */
  public function setDataFormat($dataFormat)
  {
    $this->dataFormat = $dataFormat;
  }
  /**
   * @return self::DATA_FORMAT_*
   */
  public function getDataFormat()
  {
    return $this->dataFormat;
  }
  /**
   * Optional. The request key to fetch feature values for.
   *
   * @param GoogleCloudAiplatformV1FeatureViewDataKey $dataKey
   */
  public function setDataKey(GoogleCloudAiplatformV1FeatureViewDataKey $dataKey)
  {
    $this->dataKey = $dataKey;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewDataKey
   */
  public function getDataKey()
  {
    return $this->dataKey;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FetchFeatureValuesRequest::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FetchFeatureValuesRequest');
