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

class GoogleCloudDialogflowCxV3Webhook extends \Google\Model
{
  /**
   * Indicates whether the webhook is disabled.
   *
   * @var bool
   */
  public $disabled;
  /**
   * Required. The human-readable name of the webhook, unique within the agent.
   *
   * @var string
   */
  public $displayName;
  protected $genericWebServiceType = GoogleCloudDialogflowCxV3WebhookGenericWebService::class;
  protected $genericWebServiceDataType = '';
  /**
   * The unique identifier of the webhook. Required for the
   * Webhooks.UpdateWebhook method. Webhooks.CreateWebhook populates the name
   * automatically. Format: `projects//locations//agents//webhooks/`.
   *
   * @var string
   */
  public $name;
  protected $serviceDirectoryType = GoogleCloudDialogflowCxV3WebhookServiceDirectoryConfig::class;
  protected $serviceDirectoryDataType = '';
  /**
   * Webhook execution timeout. Execution is considered failed if Dialogflow
   * doesn't receive a response from webhook at the end of the timeout period.
   * Defaults to 5 seconds, maximum allowed timeout is 30 seconds.
   *
   * @var string
   */
  public $timeout;

  /**
   * Indicates whether the webhook is disabled.
   *
   * @param bool $disabled
   */
  public function setDisabled($disabled)
  {
    $this->disabled = $disabled;
  }
  /**
   * @return bool
   */
  public function getDisabled()
  {
    return $this->disabled;
  }
  /**
   * Required. The human-readable name of the webhook, unique within the agent.
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
   * Configuration for a generic web service.
   *
   * @param GoogleCloudDialogflowCxV3WebhookGenericWebService $genericWebService
   */
  public function setGenericWebService(GoogleCloudDialogflowCxV3WebhookGenericWebService $genericWebService)
  {
    $this->genericWebService = $genericWebService;
  }
  /**
   * @return GoogleCloudDialogflowCxV3WebhookGenericWebService
   */
  public function getGenericWebService()
  {
    return $this->genericWebService;
  }
  /**
   * The unique identifier of the webhook. Required for the
   * Webhooks.UpdateWebhook method. Webhooks.CreateWebhook populates the name
   * automatically. Format: `projects//locations//agents//webhooks/`.
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
   * Configuration for a [Service Directory](https://cloud.google.com/service-
   * directory) service.
   *
   * @param GoogleCloudDialogflowCxV3WebhookServiceDirectoryConfig $serviceDirectory
   */
  public function setServiceDirectory(GoogleCloudDialogflowCxV3WebhookServiceDirectoryConfig $serviceDirectory)
  {
    $this->serviceDirectory = $serviceDirectory;
  }
  /**
   * @return GoogleCloudDialogflowCxV3WebhookServiceDirectoryConfig
   */
  public function getServiceDirectory()
  {
    return $this->serviceDirectory;
  }
  /**
   * Webhook execution timeout. Execution is considered failed if Dialogflow
   * doesn't receive a response from webhook at the end of the timeout period.
   * Defaults to 5 seconds, maximum allowed timeout is 30 seconds.
   *
   * @param string $timeout
   */
  public function setTimeout($timeout)
  {
    $this->timeout = $timeout;
  }
  /**
   * @return string
   */
  public function getTimeout()
  {
    return $this->timeout;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDialogflowCxV3Webhook::class, 'Google_Service_Dialogflow_GoogleCloudDialogflowCxV3Webhook');
