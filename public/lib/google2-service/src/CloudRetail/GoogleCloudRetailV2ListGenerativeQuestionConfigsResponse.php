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

class GoogleCloudRetailV2ListGenerativeQuestionConfigsResponse extends \Google\Collection
{
  protected $collection_key = 'generativeQuestionConfigs';
  protected $generativeQuestionConfigsType = GoogleCloudRetailV2GenerativeQuestionConfig::class;
  protected $generativeQuestionConfigsDataType = 'array';

  /**
   * All the questions for a given catalog.
   *
   * @param GoogleCloudRetailV2GenerativeQuestionConfig[] $generativeQuestionConfigs
   */
  public function setGenerativeQuestionConfigs($generativeQuestionConfigs)
  {
    $this->generativeQuestionConfigs = $generativeQuestionConfigs;
  }
  /**
   * @return GoogleCloudRetailV2GenerativeQuestionConfig[]
   */
  public function getGenerativeQuestionConfigs()
  {
    return $this->generativeQuestionConfigs;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRetailV2ListGenerativeQuestionConfigsResponse::class, 'Google_Service_CloudRetail_GoogleCloudRetailV2ListGenerativeQuestionConfigsResponse');
