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

namespace Google\Service\Cloudchannel;

class GoogleCloudChannelV1UnregisterSubscriberRequest extends \Google\Model
{
  /**
   * Optional. Resource name of the account. Required if integrator is not
   * provided. Otherwise, leave this field empty/unset.
   *
   * @var string
   */
  public $account;
  /**
   * Optional. Resource name of the integrator. Required if account is not
   * provided. Otherwise, leave this field empty/unset.
   *
   * @var string
   */
  public $integrator;
  /**
   * Required. Service account to unregister from subscriber access to the
   * topic.
   *
   * @var string
   */
  public $serviceAccount;

  /**
   * Optional. Resource name of the account. Required if integrator is not
   * provided. Otherwise, leave this field empty/unset.
   *
   * @param string $account
   */
  public function setAccount($account)
  {
    $this->account = $account;
  }
  /**
   * @return string
   */
  public function getAccount()
  {
    return $this->account;
  }
  /**
   * Optional. Resource name of the integrator. Required if account is not
   * provided. Otherwise, leave this field empty/unset.
   *
   * @param string $integrator
   */
  public function setIntegrator($integrator)
  {
    $this->integrator = $integrator;
  }
  /**
   * @return string
   */
  public function getIntegrator()
  {
    return $this->integrator;
  }
  /**
   * Required. Service account to unregister from subscriber access to the
   * topic.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudChannelV1UnregisterSubscriberRequest::class, 'Google_Service_Cloudchannel_GoogleCloudChannelV1UnregisterSubscriberRequest');
