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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SecurityFeedback extends \Google\Collection
{
  /**
   * Unspecified feedback type.
   */
  public const FEEDBACK_TYPE_FEEDBACK_TYPE_UNSPECIFIED = 'FEEDBACK_TYPE_UNSPECIFIED';
  /**
   * Feedback identifying attributes to be excluded from detections.
   */
  public const FEEDBACK_TYPE_EXCLUDED_DETECTION = 'EXCLUDED_DETECTION';
  /**
   * Unspecified reason.
   */
  public const REASON_REASON_UNSPECIFIED = 'REASON_UNSPECIFIED';
  /**
   * The feedback is created for an internal system.
   */
  public const REASON_INTERNAL_SYSTEM = 'INTERNAL_SYSTEM';
  /**
   * The feedback is created for a non-risk client.
   */
  public const REASON_NON_RISK_CLIENT = 'NON_RISK_CLIENT';
  /**
   * The feedback is created for to label NAT.
   */
  public const REASON_NAT = 'NAT';
  /**
   * The feedback is created for a penetration test.
   */
  public const REASON_PENETRATION_TEST = 'PENETRATION_TEST';
  /**
   * The feedback is created for other reasons.
   */
  public const REASON_OTHER = 'OTHER';
  protected $collection_key = 'feedbackContexts';
  /**
   * Optional. Optional text the user can provide for additional, unstructured
   * context.
   *
   * @var string
   */
  public $comment;
  /**
   * Output only. The time when this specific feedback id was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The display name of the feedback.
   *
   * @var string
   */
  public $displayName;
  protected $feedbackContextsType = GoogleCloudApigeeV1SecurityFeedbackFeedbackContext::class;
  protected $feedbackContextsDataType = 'array';
  /**
   * Required. The type of feedback being submitted.
   *
   * @var string
   */
  public $feedbackType;
  /**
   * Output only. Identifier. The feedback name is intended to be a system-
   * generated uuid.
   *
   * @var string
   */
  public $name;
  /**
   * Optional. The reason for the feedback.
   *
   * @var string
   */
  public $reason;
  /**
   * Output only. The time when this specific feedback id was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Optional text the user can provide for additional, unstructured
   * context.
   *
   * @param string $comment
   */
  public function setComment($comment)
  {
    $this->comment = $comment;
  }
  /**
   * @return string
   */
  public function getComment()
  {
    return $this->comment;
  }
  /**
   * Output only. The time when this specific feedback id was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The display name of the feedback.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Required. One or more attribute/value pairs for constraining the feedback.
   *
   * @param GoogleCloudApigeeV1SecurityFeedbackFeedbackContext[] $feedbackContexts
   */
  public function setFeedbackContexts($feedbackContexts)
  {
    $this->feedbackContexts = $feedbackContexts;
  }
  /**
   * @return GoogleCloudApigeeV1SecurityFeedbackFeedbackContext[]
   */
  public function getFeedbackContexts()
  {
    return $this->feedbackContexts;
  }
  /**
   * Required. The type of feedback being submitted.
   *
   * Accepted values: FEEDBACK_TYPE_UNSPECIFIED, EXCLUDED_DETECTION
   *
   * @param self::FEEDBACK_TYPE_* $feedbackType
   */
  public function setFeedbackType($feedbackType)
  {
    $this->feedbackType = $feedbackType;
  }
  /**
   * @return self::FEEDBACK_TYPE_*
   */
  public function getFeedbackType()
  {
    return $this->feedbackType;
  }
  /**
   * Output only. Identifier. The feedback name is intended to be a system-
   * generated uuid.
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Optional. The reason for the feedback.
   *
   * Accepted values: REASON_UNSPECIFIED, INTERNAL_SYSTEM, NON_RISK_CLIENT, NAT,
   * PENETRATION_TEST, OTHER
   *
   * @param self::REASON_* $reason
   */
  public function setReason($reason)
  {
    $this->reason = $reason;
  }
  /**
   * @return self::REASON_*
   */
  public function getReason()
  {
    return $this->reason;
  }
  /**
   * Output only. The time when this specific feedback id was updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SecurityFeedback::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SecurityFeedback');
