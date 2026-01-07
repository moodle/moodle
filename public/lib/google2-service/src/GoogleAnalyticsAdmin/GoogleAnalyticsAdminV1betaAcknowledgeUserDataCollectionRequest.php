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

namespace Google\Service\GoogleAnalyticsAdmin;

class GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest extends \Google\Model
{
  /**
   * Required. An acknowledgement that the caller of this method understands the
   * terms of user data collection. This field must contain the exact value: "I
   * acknowledge that I have the necessary privacy disclosures and rights from
   * my end users for the collection and processing of their data, including the
   * association of such data with the visitation information Google Analytics
   * collects from my site and/or app property."
   *
   * @var string
   */
  public $acknowledgement;

  /**
   * Required. An acknowledgement that the caller of this method understands the
   * terms of user data collection. This field must contain the exact value: "I
   * acknowledge that I have the necessary privacy disclosures and rights from
   * my end users for the collection and processing of their data, including the
   * association of such data with the visitation information Google Analytics
   * collects from my site and/or app property."
   *
   * @param string $acknowledgement
   */
  public function setAcknowledgement($acknowledgement)
  {
    $this->acknowledgement = $acknowledgement;
  }
  /**
   * @return string
   */
  public function getAcknowledgement()
  {
    return $this->acknowledgement;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest::class, 'Google_Service_GoogleAnalyticsAdmin_GoogleAnalyticsAdminV1betaAcknowledgeUserDataCollectionRequest');
