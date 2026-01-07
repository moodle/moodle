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

class ConnectorConfigurationAuthentication extends \Google\Model
{
  /**
   * Output only. Google-managed service account associated with this
   * connection, e.g., `service-{project_number}@gcp-sa-
   * bigqueryconnection.iam.gserviceaccount.com`. BigQuery jobs using this
   * connection will act as `service_account` identity while connecting to the
   * datasource.
   *
   * @var string
   */
  public $serviceAccount;
  protected $usernamePasswordType = ConnectorConfigurationUsernamePassword::class;
  protected $usernamePasswordDataType = '';

  /**
   * Output only. Google-managed service account associated with this
   * connection, e.g., `service-{project_number}@gcp-sa-
   * bigqueryconnection.iam.gserviceaccount.com`. BigQuery jobs using this
   * connection will act as `service_account` identity while connecting to the
   * datasource.
   *
   * @param string $serviceAccount
   */
  public function setServiceAccount($serviceAccount)
  {
    $this->serviceAccount = $serviceAccount;
  }
  /**
   * @return string
   */
  public function getServiceAccount()
  {
    return $this->serviceAccount;
  }
  /**
   * Username/password authentication.
   *
   * @param ConnectorConfigurationUsernamePassword $usernamePassword
   */
  public function setUsernamePassword(ConnectorConfigurationUsernamePassword $usernamePassword)
  {
    $this->usernamePassword = $usernamePassword;
  }
  /**
   * @return ConnectorConfigurationUsernamePassword
   */
  public function getUsernamePassword()
  {
    return $this->usernamePassword;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ConnectorConfigurationAuthentication::class, 'Google_Service_BigQueryConnectionService_ConnectorConfigurationAuthentication');
