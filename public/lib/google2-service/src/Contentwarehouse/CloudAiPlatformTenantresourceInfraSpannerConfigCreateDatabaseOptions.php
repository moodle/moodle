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

namespace Google\Service\Contentwarehouse;

class CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions extends \Google\Model
{
  /**
   * The cloud resource name for the CMEK encryption. For example,
   * projects//locations/
   *
   * @var string
   */
  public $cmekCloudResourceName;
  /**
   * The cloud resource type for the CMEK encryption. For example,
   * contentwarehouse.googleapis.com/Location
   *
   * @var string
   */
  public $cmekCloudResourceType;
  /**
   * The service name for the CMEK encryption. For example,
   * contentwarehouse.googleapis.com
   *
   * @var string
   */
  public $cmekServiceName;

  /**
   * The cloud resource name for the CMEK encryption. For example,
   * projects//locations/
   *
   * @param string $cmekCloudResourceName
   */
  public function setCmekCloudResourceName($cmekCloudResourceName)
  {
    $this->cmekCloudResourceName = $cmekCloudResourceName;
  }
  /**
   * @return string
   */
  public function getCmekCloudResourceName()
  {
    return $this->cmekCloudResourceName;
  }
  /**
   * The cloud resource type for the CMEK encryption. For example,
   * contentwarehouse.googleapis.com/Location
   *
   * @param string $cmekCloudResourceType
   */
  public function setCmekCloudResourceType($cmekCloudResourceType)
  {
    $this->cmekCloudResourceType = $cmekCloudResourceType;
  }
  /**
   * @return string
   */
  public function getCmekCloudResourceType()
  {
    return $this->cmekCloudResourceType;
  }
  /**
   * The service name for the CMEK encryption. For example,
   * contentwarehouse.googleapis.com
   *
   * @param string $cmekServiceName
   */
  public function setCmekServiceName($cmekServiceName)
  {
    $this->cmekServiceName = $cmekServiceName;
  }
  /**
   * @return string
   */
  public function getCmekServiceName()
  {
    return $this->cmekServiceName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions::class, 'Google_Service_Contentwarehouse_CloudAiPlatformTenantresourceInfraSpannerConfigCreateDatabaseOptions');
