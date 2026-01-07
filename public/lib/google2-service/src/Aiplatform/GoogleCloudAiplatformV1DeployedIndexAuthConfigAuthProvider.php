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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1DeployedIndexAuthConfigAuthProvider extends \Google\Collection
{
  protected $collection_key = 'audiences';
  /**
   * A list of allowed JWT issuers. Each entry must be a valid Google service
   * account, in the following format: `service-account-name@project-
   * id.iam.gserviceaccount.com`
   *
   * @var string[]
   */
  public $allowedIssuers;
  /**
   * The list of JWT [audiences](https://tools.ietf.org/html/draft-ietf-oauth-
   * json-web-token-32#section-4.1.3). that are allowed to access. A JWT
   * containing any of these audiences will be accepted.
   *
   * @var string[]
   */
  public $audiences;

  /**
   * A list of allowed JWT issuers. Each entry must be a valid Google service
   * account, in the following format: `service-account-name@project-
   * id.iam.gserviceaccount.com`
   *
   * @param string[] $allowedIssuers
   */
  public function setAllowedIssuers($allowedIssuers)
  {
    $this->allowedIssuers = $allowedIssuers;
  }
  /**
   * @return string[]
   */
  public function getAllowedIssuers()
  {
    return $this->allowedIssuers;
  }
  /**
   * The list of JWT [audiences](https://tools.ietf.org/html/draft-ietf-oauth-
   * json-web-token-32#section-4.1.3). that are allowed to access. A JWT
   * containing any of these audiences will be accepted.
   *
   * @param string[] $audiences
   */
  public function setAudiences($audiences)
  {
    $this->audiences = $audiences;
  }
  /**
   * @return string[]
   */
  public function getAudiences()
  {
    return $this->audiences;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DeployedIndexAuthConfigAuthProvider::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DeployedIndexAuthConfigAuthProvider');
