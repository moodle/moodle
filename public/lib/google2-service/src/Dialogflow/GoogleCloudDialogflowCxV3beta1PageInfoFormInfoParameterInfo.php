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

namespace Google\Service\Dialogflow;

class GoogleCloudDialogflowCxV3beta1PageInfoFormInfoParameterInfo extends \Google\Model
{
  /**
   * Not specified. This value should be never used.
   */
  public const STATE_PARAMETER_STATE_UNSPECIFIED = 'PARAMETER_STATE_UNSPECIFIED';
  /**
   * Indicates that the parameter does not have a value.
   */
  public const STATE_EMPTY = 'EMPTY';
  /**
   * Indicates that the parameter value is invalid. This field can be used by
   * the webhook to invalidate the parameter and ask the server to collect it
   * from the user again.
   */
  public const STATE_INVALID = 'INVALID';
  /**
   * Indicates that the parameter has a value.
   */
  public const STATE_FILLED = 'FILLED';
  /**
   * Always present for WebhookRequest. Required for WebhookResponse. The human-
   * readable name of the parameter, unique within the form. This field cannot
   * be modified by the webhook.
   *
   * @var string
   */
  public $displayName;
  /**
   * Optional for WebhookRequest. Ignored for WebhookResponse. Indicates if the
   * parameter value was just collected on the last conversation turn.
   *
   * @var bool
   */
  public $justCollected;
  /**
   * Optional for both WebhookRequest and WebhookResponse. Indicates whether the
   * parameter is required. Optional parameters will not trigger prompts;
   * however, they are filled if the user specifies them. Required parameters
   * must be filled before form filling concludes.
   *
   * @var bool
   */
  public $required;
  /**
   * Always present for WebhookRequest. Required for WebhookResponse. The state
   * of the parameter. This field can be set to INVALID by the webhook to
   * invalidate the parameter; other values set by the webhook will be ignored.
   *
   * @var string
   */
  public $state;
  /**
   * Optional for both WebhookRequest and WebhookResponse. The value of the
   * parameter. This field can be set by the webhook to change the parameter
   * value.
   *
   * @var array
   */
  public $value;

  /**
   * Always present for WebhookRequest. Required for WebhookResponse. The human-
   * readable name of the parameter, unique within the form. This field cannot
   * be modified by the webhook.
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
   * Optional for WebhookRequest. Ignored for WebhookResponse. Indicates if the
   * parameter value was just collected on the last conversation turn.
   *
   * @param bool $justCollected
   */
  public function setJustCollected($justCollected)
  {
    $this->justCollected = $justCollected;
  }
  /**
   * @return bool
   */
  public function getJustCollected()
  {
    return $this->justCollected;
  }
  /**
   * Optional for both WebhookRequest and WebhookResponse. Indicates whether the
   * parameter is required. Optional parameters will not trigger prompts;
   * however, they are filled if the user specifies them. Required parameters
   * must be filled before form filling concludes.
   *
   * @param bool $required
   */
  public function setRequired($required)
  {
    $this->required = $required;
  }
  /**
   * @return bool
   */
  public function getRequired()
  {
    return $this->required;
  }
  /**
   * Always present for WebhookRequest. Required for WebhookResponse. The state
   * of the parameter. This field can be set to INVALID by the webhook to
   * invalidate the parameter; other values set by the webhook will be ignored.
   *
   * Accepted values: PARAMETER_STATE_UNSPECIFIED, EMPTY, INVALID, FILLED
   *
   * @param self::STATE_* $state
   */
  public function setState($state)
  {
    $this->state = $state;
  }
  /**
   * @return self::STATE_*
   */
  public function getState()
  {
    return $this->state;
  }
  /**
   * Optional for both WebhookRequest and WebhookResponse. The value of the
   * parameter. This field can be set by the webhook to change the parameter
   * value.
   *
   * @param array $value
   */
  public function setValue($value)
  {
    $this->value = $value;
  }
  /**
   * @return array
   */
  public function getValue()
  {
    return $this->value;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1PageInfoFormInfoParameterInfo::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1PageInfoFormInfoParameterInfo');
