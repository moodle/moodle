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

class GoogleCloudDialogflowCxV3beta1SessionInfo extends \Google\Model
{
  /**
   * Optional for WebhookRequest. Optional for WebhookResponse. All parameters
   * collected from forms and intents during the session. Parameters can be
   * created, updated, or removed by the webhook. To remove a parameter from the
   * session, the webhook should explicitly set the parameter value to null in
   * WebhookResponse. The map is keyed by parameters' display names.
   *
   * @var array[]
   */
  public $parameters;
  /**
   * Always present for WebhookRequest. Ignored for WebhookResponse. The unique
   * identifier of the session. This field can be used by the webhook to
   * identify a session. Format: `projects//locations//agents//sessions/` or
   * `projects//locations//agents//environments//sessions/` if environment is
   * specified.
   *
   * @var string
   */
  public $session;

  /**
   * Optional for WebhookRequest. Optional for WebhookResponse. All parameters
   * collected from forms and intents during the session. Parameters can be
   * created, updated, or removed by the webhook. To remove a parameter from the
   * session, the webhook should explicitly set the parameter value to null in
   * WebhookResponse. The map is keyed by parameters' display names.
   *
   * @param array[] $parameters
   */
  public function setParameters($parameters)
  {
    $this->parameters = $parameters;
  }
  /**
   * @return array[]
   */
  public function getParameters()
  {
    return $this->parameters;
  }
  /**
   * Always present for WebhookRequest. Ignored for WebhookResponse. The unique
   * identifier of the session. This field can be used by the webhook to
   * identify a session. Format: `projects//locations//agents//sessions/` or
   * `projects//locations//agents//environments//sessions/` if environment is
   * specified.
   *
   * @param string $session
   */
  public function setSession($session)
  {
    $this->session = $session;
  }
  /**
   * @return string
   */
  public function getSession()
  {
    return $this->session;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3beta1SessionInfo::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3beta1SessionInfo');
