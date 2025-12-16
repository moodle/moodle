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

namespace Google\Service\Appengine;

class DomainMapping extends \Google\Collection
{
  protected $collection_key = 'resourceRecords';
  /**
   * Relative name of the domain serving the application. Example: example.com.
   *
   * @var string
   */
  public $id;
  /**
   * Output only. Full path to the DomainMapping resource in the API. Example:
   * apps/myapp/domainMapping/example.com.@OutputOnly
   *
   * @var string
   */
  public $name;
  protected $resourceRecordsType = ResourceRecord::class;
  protected $resourceRecordsDataType = 'array';
  protected $sslSettingsType = SslSettings::class;
  protected $sslSettingsDataType = '';

  /**
   * Relative name of the domain serving the application. Example: example.com.
   *
   * @param string $id
   */
  public function setId($id)
  {
    $this->id = $id;
  }
  /**
   * @return string
   */
  public function getId()
  {
    return $this->id;
  }
  /**
   * Output only. Full path to the DomainMapping resource in the API. Example:
   * apps/myapp/domainMapping/example.com.@OutputOnly
   *
   * @param string $name
   */
  public function setName($name)
  {
    $this->name = $name;
  }
  /**
   * @return string
   */
  public function getName()
  {
    return $this->name;
  }
  /**
   * Output only. The resource records required to configure this domain
   * mapping. These records must be added to the domain's DNS configuration in
   * order to serve the application via this domain mapping.@OutputOnly
   *
   * @param ResourceRecord[] $resourceRecords
   */
  public function setResourceRecords($resourceRecords)
  {
    $this->resourceRecords = $resourceRecords;
  }
  /**
   * @return ResourceRecord[]
   */
  public function getResourceRecords()
  {
    return $this->resourceRecords;
  }
  /**
   * SSL configuration for this domain. If unconfigured, this domain will not
   * serve with SSL.
   *
   * @param SslSettings $sslSettings
   */
  public function setSslSettings(SslSettings $sslSettings)
  {
    $this->sslSettings = $sslSettings;
  }
  /**
   * @return SslSettings
   */
  public function getSslSettings()
  {
    return $this->sslSettings;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(DomainMapping::class, 'Google_Service_Appengine_DomainMapping');
