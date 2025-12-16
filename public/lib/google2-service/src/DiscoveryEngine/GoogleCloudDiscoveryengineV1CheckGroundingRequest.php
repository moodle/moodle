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

class GoogleCloudDiscoveryengineV1CheckGroundingRequest extends \Google\Collection
{
  protected $collection_key = 'facts';
  /**
   * Answer candidate to check. It can have a maximum length of 4096 tokens.
   *
   * @var string
   */
  public $answerCandidate;
  protected $factsType = GoogleCloudDiscoveryengineV1GroundingFact::class;
  protected $factsDataType = 'array';
  protected $groundingSpecType = GoogleCloudDiscoveryengineV1CheckGroundingSpec::class;
  protected $groundingSpecDataType = '';
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @var string[]
   */
  public $userLabels;

  /**
   * Answer candidate to check. It can have a maximum length of 4096 tokens.
   *
   * @param string $answerCandidate
   */
  public function setAnswerCandidate($answerCandidate)
  {
    $this->answerCandidate = $answerCandidate;
  }
  /**
   * @return string
   */
  public function getAnswerCandidate()
  {
    return $this->answerCandidate;
  }
  /**
   * List of facts for the grounding check. We support up to 200 facts.
   *
   * @param GoogleCloudDiscoveryengineV1GroundingFact[] $facts
   */
  public function setFacts($facts)
  {
    $this->facts = $facts;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1GroundingFact[]
   */
  public function getFacts()
  {
    return $this->facts;
  }
  /**
   * Configuration of the grounding check.
   *
   * @param GoogleCloudDiscoveryengineV1CheckGroundingSpec $groundingSpec
   */
  public function setGroundingSpec(GoogleCloudDiscoveryengineV1CheckGroundingSpec $groundingSpec)
  {
    $this->groundingSpec = $groundingSpec;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1CheckGroundingSpec
   */
  public function getGroundingSpec()
  {
    return $this->groundingSpec;
  }
  /**
   * The user labels applied to a resource must meet the following requirements:
   * * Each resource can have multiple labels, up to a maximum of 64. * Each
   * label must be a key-value pair. * Keys have a minimum length of 1 character
   * and a maximum length of 63 characters and cannot be empty. Values can be
   * empty and have a maximum length of 63 characters. * Keys and values can
   * contain only lowercase letters, numeric characters, underscores, and
   * dashes. All characters must use UTF-8 encoding, and international
   * characters are allowed. * The key portion of a label must be unique.
   * However, you can use the same key with multiple resources. * Keys must
   * start with a lowercase letter or international character. See [Google Cloud
   * Document](https://cloud.google.com/resource-manager/docs/creating-managing-
   * labels#requirements) for more details.
   *
   * @param string[] $userLabels
   */
  public function setUserLabels($userLabels)
  {
    $this->userLabels = $userLabels;
  }
  /**
   * @return string[]
   */
  public function getUserLabels()
  {
    return $this->userLabels;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1CheckGroundingRequest::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1CheckGroundingRequest');
