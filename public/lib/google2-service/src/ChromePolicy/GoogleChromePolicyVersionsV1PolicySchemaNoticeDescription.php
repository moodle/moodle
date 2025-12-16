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

namespace Google\Service\ChromePolicy;

class GoogleChromePolicyVersionsV1PolicySchemaNoticeDescription extends \Google\Model
{
  /**
   * Output only. Whether the user needs to acknowledge the notice message
   * before the value can be set.
   *
   * @var bool
   */
  public $acknowledgementRequired;
  /**
   * Output only. The field name associated with the notice.
   *
   * @var string
   */
  public $field;
  /**
   * Output only. The notice message associate with the value of the field.
   *
   * @var string
   */
  public $noticeMessage;
  /**
   * Output only. The value of the field that has a notice. When setting the
   * field to this value, the user may be required to acknowledge the notice
   * message in order for the value to be set.
   *
   * @var string
   */
  public $noticeValue;

  /**
   * Output only. Whether the user needs to acknowledge the notice message
   * before the value can be set.
   *
   * @param bool $acknowledgementRequired
   */
  public function setAcknowledgementRequired($acknowledgementRequired)
  {
    $this->acknowledgementRequired = $acknowledgementRequired;
  }
  /**
   * @return bool
   */
  public function getAcknowledgementRequired()
  {
    return $this->acknowledgementRequired;
  }
  /**
   * Output only. The field name associated with the notice.
   *
   * @param string $field
   */
  public function setField($field)
  {
    $this->field = $field;
  }
  /**
   * @return string
   */
  public function getField()
  {
    return $this->field;
  }
  /**
   * Output only. The notice message associate with the value of the field.
   *
   * @param string $noticeMessage
   */
  public function setNoticeMessage($noticeMessage)
  {
    $this->noticeMessage = $noticeMessage;
  }
  /**
   * @return string
   */
  public function getNoticeMessage()
  {
    return $this->noticeMessage;
  }
  /**
   * Output only. The value of the field that has a notice. When setting the
   * field to this value, the user may be required to acknowledge the notice
   * message in order for the value to be set.
   *
   * @param string $noticeValue
   */
  public function setNoticeValue($noticeValue)
  {
    $this->noticeValue = $noticeValue;
  }
  /**
   * @return string
   */
  public function getNoticeValue()
  {
    return $this->noticeValue;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromePolicyVersionsV1PolicySchemaNoticeDescription::class, 'Google_Service_ChromePolicy_GoogleChromePolicyVersionsV1PolicySchemaNoticeDescription');
