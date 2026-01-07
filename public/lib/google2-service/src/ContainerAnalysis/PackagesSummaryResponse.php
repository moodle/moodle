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

namespace Google\Service\ContainerAnalysis;

class PackagesSummaryResponse extends \Google\Collection
{
  protected $collection_key = 'licensesSummary';
  protected $licensesSummaryType = LicensesSummary::class;
  protected $licensesSummaryDataType = 'array';
  /**
   * @var string
   */
  public $resourceUrl;

  /**
   * @param LicensesSummary[]
   */
  public function setLicensesSummary($licensesSummary)
  {
    $this->licensesSummary = $licensesSummary;
  }
  /**
   * @return LicensesSummary[]
   */
  public function getLicensesSummary()
  {
    return $this->licensesSummary;
  }
  /**
   * @param string
   */
  public function setResourceUrl($resourceUrl)
  {
    $this->resourceUrl = $resourceUrl;
  }
  /**
   * @return string
   */
  public function getResourceUrl()
  {
    return $this->resourceUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(PackagesSummaryResponse::class, 'Google_Service_ContainerAnalysis_PackagesSummaryResponse');
