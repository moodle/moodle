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

class GoogleCloudAiplatformV1FetchFeatureValuesResponse extends \Google\Model
{
  protected $dataKeyType = GoogleCloudAiplatformV1FeatureViewDataKey::class;
  protected $dataKeyDataType = '';
  protected $keyValuesType = GoogleCloudAiplatformV1FetchFeatureValuesResponseFeatureNameValuePairList::class;
  protected $keyValuesDataType = '';
  /**
   * Feature values in proto Struct format.
   *
   * @var array[]
   */
  public $protoStruct;

  /**
   * The data key associated with this response. Will only be populated for
   * FeatureOnlineStoreService.StreamingFetchFeatureValues RPCs.
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
  /**
   * Feature values in KeyValue format.
   *
   * @param GoogleCloudAiplatformV1FetchFeatureValuesResponseFeatureNameValuePairList $keyValues
   */
  public function setKeyValues(GoogleCloudAiplatformV1FetchFeatureValuesResponseFeatureNameValuePairList $keyValues)
  {
    $this->keyValues = $keyValues;
  }
  /**
   * @return GoogleCloudAiplatformV1FetchFeatureValuesResponseFeatureNameValuePairList
   */
  public function getKeyValues()
  {
    return $this->keyValues;
  }
  /**
   * Feature values in proto Struct format.
   *
   * @param array[] $protoStruct
   */
  public function setProtoStruct($protoStruct)
  {
    $this->protoStruct = $protoStruct;
  }
  /**
   * @return array[]
   */
  public function getProtoStruct()
  {
    return $this->protoStruct;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FetchFeatureValuesResponse::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FetchFeatureValuesResponse');
