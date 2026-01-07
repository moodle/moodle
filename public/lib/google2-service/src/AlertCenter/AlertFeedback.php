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

namespace Google\Service\AlertCenter;

class AlertFeedback extends \Google\Model
{
  /**
   * The feedback type is not specified.
   */
  public const TYPE_ALERT_FEEDBACK_TYPE_UNSPECIFIED = 'ALERT_FEEDBACK_TYPE_UNSPECIFIED';
  /**
   * The alert report is not useful.
   */
  public const TYPE_NOT_USEFUL = 'NOT_USEFUL';
  /**
   * The alert report is somewhat useful.
   */
  public const TYPE_SOMEWHAT_USEFUL = 'SOMEWHAT_USEFUL';
  /**
   * The alert report is very useful.
   */
  public const TYPE_VERY_USEFUL = 'VERY_USEFUL';
  /**
   * Output only. The alert identifier.
   *
   * @var string
   */
  public $alertId;
  /**
   * Output only. The time this feedback was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Output only. The unique identifier of the Google Workspace account of the
   * customer.
   *
   * @var string
   */
  public $customerId;
  /**
   * Output only. The email of the user that provided the feedback.
   *
   * @var string
   */
  public $email;
  /**
   * Output only. The unique identifier for the feedback.
   *
   * @var string
   */
  public $feedbackId;
  /**
   * Required. The type of the feedback.
   *
   * @var string
   */
  public $type;

  /**
   * Output only. The alert identifier.
   *
   * @param string $alertId
   */
  public function setAlertId($alertId)
  {
    $this->alertId = $alertId;
  }
  /**
   * @return string
   */
  public function getAlertId()
  {
    return $this->alertId;
  }
  /**
   * Output only. The time this feedback was created.
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
   * Output only. The unique identifier of the Google Workspace account of the
   * customer.
   *
   * @param string $customerId
   */
  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }
  /**
   * @return string
   */
  public function getCustomerId()
  {
    return $this->customerId;
  }
  /**
   * Output only. The email of the user that provided the feedback.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * Output only. The unique identifier for the feedback.
   *
   * @param string $feedbackId
   */
  public function setFeedbackId($feedbackId)
  {
    $this->feedbackId = $feedbackId;
  }
  /**
   * @return string
   */
  public function getFeedbackId()
  {
    return $this->feedbackId;
  }
  /**
   * Required. The type of the feedback.
   *
   * Accepted values: ALERT_FEEDBACK_TYPE_UNSPECIFIED, NOT_USEFUL,
   * SOMEWHAT_USEFUL, VERY_USEFUL
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
class_alias(AlertFeedback::class, 'Google_Service_AlertCenter_AlertFeedback');
