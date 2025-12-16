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

class GoogleCloudAiplatformV1DnsPeeringConfig extends \Google\Model
{
  /**
   * Required. The DNS name suffix of the zone being peered to, e.g., "my-
   * internal-domain.corp.". Must end with a dot.
   *
   * @var string
   */
  public $domain;
  /**
   * Required. The VPC network name in the target_project where the DNS zone
   * specified by 'domain' is visible.
   *
   * @var string
   */
  public $targetNetwork;
  /**
   * Required. The project ID hosting the Cloud DNS managed zone that contains
   * the 'domain'. The Vertex AI Service Agent requires the dns.peer role on
   * this project.
   *
   * @var string
   */
  public $targetProject;

  /**
   * Required. The DNS name suffix of the zone being peered to, e.g., "my-
   * internal-domain.corp.". Must end with a dot.
   *
   * @param string $domain
   */
  public function setDomain($domain)
  {
    $this->domain = $domain;
  }
  /**
   * @return string
   */
  public function getDomain()
  {
    return $this->domain;
  }
  /**
   * Required. The VPC network name in the target_project where the DNS zone
   * specified by 'domain' is visible.
   *
   * @param string $targetNetwork
   */
  public function setTargetNetwork($targetNetwork)
  {
    $this->targetNetwork = $targetNetwork;
  }
  /**
   * @return string
   */
  public function getTargetNetwork()
  {
    return $this->targetNetwork;
  }
  /**
   * Required. The project ID hosting the Cloud DNS managed zone that contains
   * the 'domain'. The Vertex AI Service Agent requires the dns.peer role on
   * this project.
   *
   * @param string $targetProject
   */
  public function setTargetProject($targetProject)
  {
    $this->targetProject = $targetProject;
  }
  /**
   * @return string
   */
  public function getTargetProject()
  {
    return $this->targetProject;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1DnsPeeringConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1DnsPeeringConfig');
