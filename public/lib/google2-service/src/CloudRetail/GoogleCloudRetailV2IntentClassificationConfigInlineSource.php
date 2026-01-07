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

namespace Google\Service\CloudRetail;

class GoogleCloudRetailV2IntentClassificationConfigInlineSource extends \Google\Collection
{
  protected $collection_key = 'inlineForceIntents';
  protected $inlineForceIntentsType = GoogleCloudRetailV2IntentClassificationConfigInlineForceIntent::class;
  protected $inlineForceIntentsDataType = 'array';

  /**
   * Optional. A list of inline force intent classifications.
   *
   * @param GoogleCloudRetailV2IntentClassificationConfigInlineForceIntent[] $inlineForceIntents
   */
  public function setInlineForceIntents($inlineForceIntents)
  {
    $this->inlineForceIntents = $inlineForceIntents;
  }
  /**
   * @return GoogleCloudRetailV2IntentClassificationConfigInlineForceIntent[]
   */
  public function getInlineForceIntents()
  {
    return $this->inlineForceIntents;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2IntentClassificationConfigInlineSource::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2IntentClassificationConfigInlineSource');
