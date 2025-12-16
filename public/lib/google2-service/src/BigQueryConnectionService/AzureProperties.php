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

namespace Google\Service\BigQueryConnectionService;

class AzureProperties extends \Google\Model
{
  /**
   * Output only. The name of the Azure Active Directory Application.
   *
   * @var string
   */
  public $application;
  /**
   * Output only. The client id of the Azure Active Directory Application.
   *
   * @var string
   */
  public $clientId;
  /**
   * The id of customer's directory that host the data.
   *
   * @var string
   */
  public $customerTenantId;
  /**
   * The client ID of the user's Azure Active Directory Application used for a
   * federated connection.
   *
   * @var string
   */
  public $federatedApplicationClientId;
  /**
   * Output only. A unique Google-owned and Google-generated identity for the
   * Connection. This identity will be used to access the user's Azure Active
   * Directory Application.
   *
   * @var string
   */
  public $identity;
  /**
   * Output only. The object id of the Azure Active Directory Application.
   *
   * @var string
   */
  public $objectId;
  /**
   * The URL user will be redirected to after granting consent during connection
   * setup.
   *
   * @var string
   */
  public $redirectUri;

  /**
   * Output only. The name of the Azure Active Directory Application.
   *
   * @param string $application
   */
  public function setApplication($application)
  {
    $this->application = $application;
  }
  /**
   * @return string
   */
  public function getApplication()
  {
    return $this->application;
  }
  /**
   * Output only. The client id of the Azure Active Directory Application.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * The id of customer's directory that host the data.
   *
   * @param string $customerTenantId
   */
  public function setCustomerTenantId($customerTenantId)
  {
    $this->customerTenantId = $customerTenantId;
  }
  /**
   * @return string
   */
  public function getCustomerTenantId()
  {
    return $this->customerTenantId;
  }
  /**
   * The client ID of the user's Azure Active Directory Application used for a
   * federated connection.
   *
   * @param string $federatedApplicationClientId
   */
  public function setFederatedApplicationClientId($federatedApplicationClientId)
  {
    $this->federatedApplicationClientId = $federatedApplicationClientId;
  }
  /**
   * @return string
   */
  public function getFederatedApplicationClientId()
  {
    return $this->federatedApplicationClientId;
  }
  /**
   * Output only. A unique Google-owned and Google-generated identity for the
   * Connection. This identity will be used to access the user's Azure Active
   * Directory Application.
   *
   * @param string $identity
   */
  public function setIdentity($identity)
  {
    $this->identity = $identity;
  }
  /**
   * @return string
   */
  public function getIdentity()
  {
    return $this->identity;
  }
  /**
   * Output only. The object id of the Azure Active Directory Application.
   *
   * @param string $objectId
   */
  public function setObjectId($objectId)
  {
    $this->objectId = $objectId;
  }
  /**
   * @return string
   */
  public function getObjectId()
  {
    return $this->objectId;
  }
  /**
   * The URL user will be redirected to after granting consent during connection
   * setup.
   *
   * @param string $redirectUri
   */
  public function setRedirectUri($redirectUri)
  {
    $this->redirectUri = $redirectUri;
  }
  /**
   * @return string
   */
  public function getRedirectUri()
  {
    return $this->redirectUri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AzureProperties::class, 'Google_Service_BigQueryConnectionService_AzureProperties');
