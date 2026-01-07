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

namespace Google\Service\Integrations;

class GoogleCloudIntegrationsV1alphaCloudSchedulerConfig extends \Google\Model
{
  /**
   * Required. The cron tab of cloud scheduler trigger.
   *
   * @var string
   */
  public $cronTab;
  /**
   * Optional. When the job was deleted from Pantheon UI, error_message will be
   * populated when Get/List integrations
   *
   * @var string
   */
  public $errorMessage;
  /**
   * Required. The location where associated cloud scheduler job will be created
   *
   * @var string
   */
  public $location;
  /**
   * Required. Service account used by Cloud Scheduler to trigger the
   * integration at scheduled time
   *
   * @var string
   */
  public $serviceAccountEmail;

  /**
   * Required. The cron tab of cloud scheduler trigger.
   *
   * @param string $cronTab
   */
  public function setCronTab($cronTab)
  {
    $this->cronTab = $cronTab;
  }
  /**
   * @return string
   */
  public function getCronTab()
  {
    return $this->cronTab;
  }
  /**
   * Optional. When the job was deleted from Pantheon UI, error_message will be
   * populated when Get/List integrations
   *
   * @param string $errorMessage
   */
  public function setErrorMessage($errorMessage)
  {
    $this->errorMessage = $errorMessage;
  }
  /**
   * @return string
   */
  public function getErrorMessage()
  {
    return $this->errorMessage;
  }
  /**
   * Required. The location where associated cloud scheduler job will be created
   *
   * @param string $location
   */
  public function setLocation($location)
  {
    $this->location = $location;
  }
  /**
   * @return string
   */
  public function getLocation()
  {
    return $this->location;
  }
  /**
   * Required. Service account used by Cloud Scheduler to trigger the
   * integration at scheduled time
   *
   * @param string $serviceAccountEmail
   */
  public function setServiceAccountEmail($serviceAccountEmail)
  {
    $this->serviceAccountEmail = $serviceAccountEmail;
  }
  /**
   * @return string
   */
  public function getServiceAccountEmail()
  {
    return $this->serviceAccountEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaCloudSchedulerConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaCloudSchedulerConfig');
