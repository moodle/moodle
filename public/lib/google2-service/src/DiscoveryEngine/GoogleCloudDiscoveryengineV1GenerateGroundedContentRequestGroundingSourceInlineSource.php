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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource extends \Google\Collection
{
  protected $collection_key = 'groundingFacts';
  /**
   * @var string[]
   */
  public $attributes;
  protected $groundingFactsType = GoogleCloudDiscoveryengineV1GroundingFact::class;
  protected $groundingFactsDataType = 'array';

  /**
   * @param string[]
   */
  public function setAttributes($attributes)
  {
    $this->attributes = $attributes;
  }
  /**
   * @return string[]
   */
  public function getAttributes()
  {
    return $this->attributes;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1GroundingFact[]
   */
  public function setGroundingFacts($groundingFacts)
  {
    $this->groundingFacts = $groundingFacts;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GroundingFact[]
   */
  public function getGroundingFacts()
  {
    return $this->groundingFacts;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1GenerateGroundedContentRequestGroundingSourceInlineSource');
