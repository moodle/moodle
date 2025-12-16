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

class GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig extends \Google\Collection
{
  protected $collection_key = 'emailAddresses';
  /**
   * Information to provide for recipients.
   *
   * @var string
   */
  public $customMessage;
  /**
   * Email addresses to send approval request to.
   *
   * @var string[]
   */
  public $emailAddresses;
  protected $expirationType = GoogleCloudIntegrationsV1alphaSuspensionApprovalExpiration::class;
  protected $expirationDataType = '';

  /**
   * Information to provide for recipients.
   *
   * @param string $customMessage
   */
  public function setCustomMessage($customMessage)
  {
    $this->customMessage = $customMessage;
  }
  /**
   * @return string
   */
  public function getCustomMessage()
  {
    return $this->customMessage;
  }
  /**
   * Email addresses to send approval request to.
   *
   * @param string[] $emailAddresses
   */
  public function setEmailAddresses($emailAddresses)
  {
    $this->emailAddresses = $emailAddresses;
  }
  /**
   * @return string[]
   */
  public function getEmailAddresses()
  {
    return $this->emailAddresses;
  }
  /**
   * Indicates the next steps when no external actions happen on the suspension.
   *
   * @param GoogleCloudIntegrationsV1alphaSuspensionApprovalExpiration $expiration
   */
  public function setExpiration(GoogleCloudIntegrationsV1alphaSuspensionApprovalExpiration $expiration)
  {
    $this->expiration = $expiration;
  }
  /**
   * @return GoogleCloudIntegrationsV1alphaSuspensionApprovalExpiration
   */
  public function getExpiration()
  {
    return $this->expiration;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig::class, 'Google_Service_Integrations_GoogleCloudIntegrationsV1alphaSuspensionApprovalConfig');
