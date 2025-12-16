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

namespace Google\Service\Apigee;

class GoogleCloudApigeeV1SharedFlowRevision extends \Google\Collection
{
  protected $collection_key = 'sharedFlows';
  protected $configurationVersionType = GoogleCloudApigeeV1ConfigVersion::class;
  protected $configurationVersionDataType = '';
  /**
   * A textual description of the shared flow revision.
   *
   * @var string
   */
  public $contextInfo;
  /**
   * Time at which this shared flow revision was created, in milliseconds since
   * epoch.
   *
   * @var string
   */
  public $createdAt;
  /**
   * Description of the shared flow revision.
   *
   * @var string
   */
  public $description;
  /**
   * The human readable name of this shared flow.
   *
   * @var string
   */
  public $displayName;
  /**
   * A Key-Value map of metadata about this shared flow revision.
   *
   * @var string[]
   */
  public $entityMetaDataAsProperties;
  /**
   * Time at which this shared flow revision was most recently modified, in
   * milliseconds since epoch.
   *
   * @var string
   */
  public $lastModifiedAt;
  /**
   * The resource ID of the parent shared flow.
   *
   * @var string
   */
  public $name;
  /**
   * A list of policy names included in this shared flow revision.
   *
   * @var string[]
   */
  public $policies;
  protected $resourceFilesType = GoogleCloudApigeeV1ResourceFiles::class;
  protected $resourceFilesDataType = '';
  /**
   * A list of the resources included in this shared flow revision formatted as
   * "{type}://{name}".
   *
   * @var string[]
   */
  public $resources;
  /**
   * The resource ID of this revision.
   *
   * @var string
   */
  public $revision;
  /**
   * A list of the shared flow names included in this shared flow revision.
   *
   * @var string[]
   */
  public $sharedFlows;
  /**
   * The string "Application"
   *
   * @var string
   */
  public $type;

  /**
   * The version of the configuration schema to which this shared flow conforms.
   * The only supported value currently is majorVersion 4 and minorVersion 0.
   * This setting may be used in the future to enable evolution of the shared
   * flow format.
   *
   * @param GoogleCloudApigeeV1ConfigVersion $configurationVersion
   */
  public function setConfigurationVersion(GoogleCloudApigeeV1ConfigVersion $configurationVersion)
  {
    $this->configurationVersion = $configurationVersion;
  }
  /**
   * @return GoogleCloudApigeeV1ConfigVersion
   */
  public function getConfigurationVersion()
  {
    return $this->configurationVersion;
  }
  /**
   * A textual description of the shared flow revision.
   *
   * @param string $contextInfo
   */
  public function setContextInfo($contextInfo)
  {
    $this->contextInfo = $contextInfo;
  }
  /**
   * @return string
   */
  public function getContextInfo()
  {
    return $this->contextInfo;
  }
  /**
   * Time at which this shared flow revision was created, in milliseconds since
   * epoch.
   *
   * @param string $createdAt
   */
  public function setCreatedAt($createdAt)
  {
    $this->createdAt = $createdAt;
  }
  /**
   * @return string
   */
  public function getCreatedAt()
  {
    return $this->createdAt;
  }
  /**
   * Description of the shared flow revision.
   *
   * @param string $description
   */
  public function setDescription($description)
  {
    $this->description = $description;
  }
  /**
   * @return string
   */
  public function getDescription()
  {
    return $this->description;
  }
  /**
   * The human readable name of this shared flow.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * A Key-Value map of metadata about this shared flow revision.
   *
   * @param string[] $entityMetaDataAsProperties
   */
  public function setEntityMetaDataAsProperties($entityMetaDataAsProperties)
  {
    $this->entityMetaDataAsProperties = $entityMetaDataAsProperties;
  }
  /**
   * @return string[]
   */
  public function getEntityMetaDataAsProperties()
  {
    return $this->entityMetaDataAsProperties;
  }
  /**
   * Time at which this shared flow revision was most recently modified, in
   * milliseconds since epoch.
   *
   * @param string $lastModifiedAt
   */
  public function setLastModifiedAt($lastModifiedAt)
  {
    $this->lastModifiedAt = $lastModifiedAt;
  }
  /**
   * @return string
   */
  public function getLastModifiedAt()
  {
    return $this->lastModifiedAt;
  }
  /**
   * The resource ID of the parent shared flow.
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
   * A list of policy names included in this shared flow revision.
   *
   * @param string[] $policies
   */
  public function setPolicies($policies)
  {
    $this->policies = $policies;
  }
  /**
   * @return string[]
   */
  public function getPolicies()
  {
    return $this->policies;
  }
  /**
   * The resource files included in this shared flow revision.
   *
   * @param GoogleCloudApigeeV1ResourceFiles $resourceFiles
   */
  public function setResourceFiles(GoogleCloudApigeeV1ResourceFiles $resourceFiles)
  {
    $this->resourceFiles = $resourceFiles;
  }
  /**
   * @return GoogleCloudApigeeV1ResourceFiles
   */
  public function getResourceFiles()
  {
    return $this->resourceFiles;
  }
  /**
   * A list of the resources included in this shared flow revision formatted as
   * "{type}://{name}".
   *
   * @param string[] $resources
   */
  public function setResources($resources)
  {
    $this->resources = $resources;
  }
  /**
   * @return string[]
   */
  public function getResources()
  {
    return $this->resources;
  }
  /**
   * The resource ID of this revision.
   *
   * @param string $revision
   */
  public function setRevision($revision)
  {
    $this->revision = $revision;
  }
  /**
   * @return string
   */
  public function getRevision()
  {
    return $this->revision;
  }
  /**
   * A list of the shared flow names included in this shared flow revision.
   *
   * @param string[] $sharedFlows
   */
  public function setSharedFlows($sharedFlows)
  {
    $this->sharedFlows = $sharedFlows;
  }
  /**
   * @return string[]
   */
  public function getSharedFlows()
  {
    return $this->sharedFlows;
  }
  /**
   * The string "Application"
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudApigeeV1SharedFlowRevision::class, 'Google_Service_Apigee_GoogleCloudApigeeV1SharedFlowRevision');
