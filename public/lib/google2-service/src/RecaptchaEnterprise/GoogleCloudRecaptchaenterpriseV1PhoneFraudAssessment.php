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

class GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment extends \Google\Model
{
  protected $smsTollFraudVerdictType = GoogleCloudRecaptchaenterpriseV1SmsTollFraudVerdict::class;
  protected $smsTollFraudVerdictDataType = '';

  /**
   * Output only. Assessment of this phone event for risk of SMS toll fraud.
   *
   * @param GoogleCloudRecaptchaenterpriseV1SmsTollFraudVerdict $smsTollFraudVerdict
   */
  public function setSmsTollFraudVerdict(GoogleCloudRecaptchaenterpriseV1SmsTollFraudVerdict $smsTollFraudVerdict)
  {
    $this->smsTollFraudVerdict = $smsTollFraudVerdict;
  }
  /**
   * @return GoogleCloudRecaptchaenterpriseV1SmsTollFraudVerdict
   */
  public function getSmsTollFraudVerdict()
  {
    return $this->smsTollFraudVerdict;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment::class, 'Google_Service_RecaptchaEnterprise_GoogleCloudRecaptchaenterpriseV1PhoneFraudAssessment');
