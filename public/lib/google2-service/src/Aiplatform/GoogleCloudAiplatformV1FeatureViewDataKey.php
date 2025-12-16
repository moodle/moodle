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

class GoogleCloudAiplatformV1FeatureViewDataKey extends \Google\Model
{
  protected $compositeKeyType = GoogleCloudAiplatformV1FeatureViewDataKeyCompositeKey::class;
  protected $compositeKeyDataType = '';
  /**
   * String key to use for lookup.
   *
   * @var string
   */
  public $key;

  /**
   * The actual Entity ID will be composed from this struct. This should match
   * with the way ID is defined in the FeatureView spec.
   *
   * @param GoogleCloudAiplatformV1FeatureViewDataKeyCompositeKey $compositeKey
   */
  public function setCompositeKey(GoogleCloudAiplatformV1FeatureViewDataKeyCompositeKey $compositeKey)
  {
    $this->compositeKey = $compositeKey;
  }
  /**
   * @return GoogleCloudAiplatformV1FeatureViewDataKeyCompositeKey
   */
  public function getCompositeKey()
  {
    return $this->compositeKey;
  }
  /**
   * String key to use for lookup.
   *
   * @param string $key
   */
  public function setKey($key)
  {
    $this->key = $key;
  }
  /**
   * @return string
   */
  public function getKey()
  {
    return $this->key;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1FeatureViewDataKey::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1FeatureViewDataKey');
