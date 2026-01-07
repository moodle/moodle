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

namespace Google\Service\Contactcenterinsights;

class GoogleCloudContactcenterinsightsV1alpha1IssueModelInputDataConfig extends \Google\Model
{
  /**
   * Default value, if unspecified will default to PHONE_CALL.
   */
  public const MEDIUM_MEDIUM_UNSPECIFIED = 'MEDIUM_UNSPECIFIED';
  /**
   * The format for conversations that took place over the phone.
   */
  public const MEDIUM_PHONE_CALL = 'PHONE_CALL';
  /**
   * The format for conversations that took place over chat.
   */
  public const MEDIUM_CHAT = 'CHAT';
  /**
   * A filter to reduce the conversations used for training the model to a
   * specific subset. Refer to https://cloud.google.com/contact-
   * center/insights/docs/filtering for details.
   *
   * @var string
   */
  public $filter;
  /**
   * Medium of conversations used in training data. This field is being
   * deprecated. To specify the medium to be used in training a new issue model,
   * set the `medium` field on `filter`.
   *
   * @deprecated
   * @var string
   */
  public $medium;
  /**
   * Output only. Number of conversations used in training. Output only.
   *
   * @var string
   */
  public $trainingConversationsCount;

  /**
   * A filter to reduce the conversations used for training the model to a
   * specific subset. Refer to https://cloud.google.com/contact-
   * center/insights/docs/filtering for details.
   *
   * @param string $filter
   */
  public function setFilter($filter)
  {
    $this->filter = $filter;
  }
  /**
   * @return string
   */
  public function getFilter()
  {
    return $this->filter;
  }
  /**
   * Medium of conversations used in training data. This field is being
   * deprecated. To specify the medium to be used in training a new issue model,
   * set the `medium` field on `filter`.
   *
   * Accepted values: MEDIUM_UNSPECIFIED, PHONE_CALL, CHAT
   *
   * @deprecated
   * @param self::MEDIUM_* $medium
   */
  public function setMedium($medium)
  {
    $this->medium = $medium;
  }
  /**
   * @deprecated
   * @return self::MEDIUM_*
   */
  public function getMedium()
  {
    return $this->medium;
  }
  /**
   * Output only. Number of conversations used in training. Output only.
   *
   * @param string $trainingConversationsCount
   */
  public function setTrainingConversationsCount($trainingConversationsCount)
  {
    $this->trainingConversationsCount = $trainingConversationsCount;
  }
  /**
   * @return string
   */
  public function getTrainingConversationsCount()
  {
    return $this->trainingConversationsCount;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudContactcenterinsightsV1alpha1IssueModelInputDataConfig::class, 'Google_Service_Contactcenterinsights_GoogleCloudContactcenterinsightsV1alpha1IssueModelInputDataConfig');
