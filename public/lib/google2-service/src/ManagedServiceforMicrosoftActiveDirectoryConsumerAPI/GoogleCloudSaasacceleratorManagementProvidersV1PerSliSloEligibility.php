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

namespace Google\Service\ManagedServiceforMicrosoftActiveDirectoryConsumerAPI;

class GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility extends \Google\Model
{
  protected $eligibilitiesType = GoogleCloudSaasacceleratorManagementProvidersV1SloEligibility::class;
  protected $eligibilitiesDataType = 'map';

  /**
   * An entry in the eligibilities map specifies an eligibility for a particular
   * SLI for the given instance. The SLI key in the name must be a valid SLI
   * name specified in the Eligibility Exporter binary flags otherwise an error
   * will be emitted by Eligibility Exporter and the oncaller will be alerted.
   * If an SLI has been defined in the binary flags but the eligibilities map
   * does not contain it, the corresponding SLI time series will not be emitted
   * by the Eligibility Exporter. This ensures a smooth rollout and
   * compatibility between the data produced by different versions of the
   * Eligibility Exporters. If eligibilities map contains a key for an SLI which
   * has not been declared in the binary flags, there will be an error message
   * emitted in the Eligibility Exporter log and the metric for the SLI in
   * question will not be emitted.
   *
   * @param GoogleCloudSaasacceleratorManagementProvidersV1SloEligibility[] $eligibilities
   */
  public function setEligibilities($eligibilities)
  {
    $this->eligibilities = $eligibilities;
  }
  /**
   * @return GoogleCloudSaasacceleratorManagementProvidersV1SloEligibility[]
   */
  public function getEligibilities()
  {
    return $this->eligibilities;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility::class, 'Google_Service_ManagedServiceforMicrosoftActiveDirectoryConsumerAPI_GoogleCloudSaasacceleratorManagementProvidersV1PerSliSloEligibility');
