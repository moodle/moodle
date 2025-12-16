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

namespace Google\Service\RecaptchaEnterprise;

class GoogleCloudRecaptchaenterpriseV1FraudSignals extends \Google\Model
{
  protected $cardSignalsType = GoogleCloudRecaptchaenterpriseV1FraudSignalsCardSignals::class;
  protected $cardSignalsDataType = '';
  protected $userSignalsType = GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals::class;
  protected $userSignalsDataType = '';

  /**
   * Output only. Signals describing the payment card or cards used in this
   * transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FraudSignalsCardSignals $cardSignals
   */
  public function setCardSignals(GoogleCloudRecaptchaenterpriseV1FraudSignalsCardSignals $cardSignals)
  {
    $this->cardSignals = $cardSignals;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FraudSignalsCardSignals
   */
  public function getCardSignals()
  {
    return $this->cardSignals;
  }
  /**
   * Output only. Signals describing the end user in this transaction.
   *
   * @param GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals $userSignals
   */
  public function setUserSignals(GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals $userSignals)
  {
    $this->userSignals = $userSignals;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1FraudSignalsUserSignals
   */
  public function getUserSignals()
  {
    return $this->userSignals;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1FraudSignals::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1FraudSignals');
