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

class GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfoQueryClassificationInfo extends \Google\Model
{
  /**
   * Unspecified query classification type.
   */
  public const TYPE_TYPE_UNSPECIFIED = 'TYPE_UNSPECIFIED';
  /**
   * Adversarial query classification type.
   */
  public const TYPE_ADVERSARIAL_QUERY = 'ADVERSARIAL_QUERY';
  /**
   * Non-answer-seeking query classification type, for chit chat.
   */
  public const TYPE_NON_ANSWER_SEEKING_QUERY = 'NON_ANSWER_SEEKING_QUERY';
  /**
   * Jail-breaking query classification type.
   */
  public const TYPE_JAIL_BREAKING_QUERY = 'JAIL_BREAKING_QUERY';
  /**
   * Non-answer-seeking query classification type, for no clear intent.
   */
  public const TYPE_NON_ANSWER_SEEKING_QUERY_V2 = 'NON_ANSWER_SEEKING_QUERY_V2';
  /**
   * User defined query classification type.
   */
  public const TYPE_USER_DEFINED_CLASSIFICATION_QUERY = 'USER_DEFINED_CLASSIFICATION_QUERY';
  /**
   * Classification output.
   *
   * @var bool
   */
  public $positive;
  /**
   * Query classification type.
   *
   * @var string
   */
  public $type;

  /**
   * Classification output.
   *
   * @param bool $positive
   */
  public function setPositive($positive)
  {
    $this->positive = $positive;
  }
  /**
   * @return bool
   */
  public function getPositive()
  {
    return $this->positive;
  }
  /**
   * Query classification type.
   *
   * Accepted values: TYPE_UNSPECIFIED, ADVERSARIAL_QUERY,
   * NON_ANSWER_SEEKING_QUERY, JAIL_BREAKING_QUERY, NON_ANSWER_SEEKING_QUERY_V2,
   * USER_DEFINED_CLASSIFICATION_QUERY
   *
   * @param self::TYPE_* $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return self::TYPE_*
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfoQueryClassificationInfo::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1alphaAnswerQueryUnderstandingInfoQueryClassificationInfo');
