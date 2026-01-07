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

namespace Google\Service\DeploymentManager;

class Manifest extends \Google\Collection
{
  protected $collection_key = 'imports';
  protected $configType = ConfigFile::class;
  protected $configDataType = '';
  /**
   * Output only. The fully-expanded configuration file, including any templates
   * and references.
   *
   * @var string
   */
  public $expandedConfig;
  /**
   * @var string
   */
  public $id;
  protected $importsType = ImportFile::class;
  protected $importsDataType = 'array';
  /**
   * Output only. Creation timestamp in RFC3339 text format.
   *
   * @var string
   */
  public $insertTime;
  /**
   * Output only. The YAML layout for this manifest.
   *
   * @var string
   */
  public $layout;
  /**
   * Output only. The computed size of the fully expanded manifest.
   *
   * @var string
   */
  public $manifestSizeBytes;
  /**
   * Output only. The size limit for expanded manifests in the project.
   *
   * @var string
   */
  public $manifestSizeLimitBytes;
  /**
   * Output only. The name of the manifest.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Self link for the manifest.
   *
   * @var string
   */
  public $selfLink;

  /**
   * Output only. The YAML configuration for this manifest.
   *
   * @param ConfigFile $config
   */
  public function setConfig(ConfigFile $config)
  {
    $this->config = $config;
  }
  /**
   * @return ConfigFile
   */
  public function getConfig()
  {
    return $this->config;
  }
  /**
   * Output only. The fully-expanded configuration file, including any templates
   * and references.
   *
   * @param string $expandedConfig
   */
  public function setExpandedConfig($expandedConfig)
  {
    $this->expandedConfig = $expandedConfig;
  }
  /**
   * @return string
   */
  public function getExpandedConfig()
  {
    return $this->expandedConfig;
  }
  /**
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
   * Output only. The imported files for this manifest.
   *
   * @param ImportFile[] $imports
   */
  public function setImports($imports)
  {
    $this->imports = $imports;
  }
  /**
   * @return ImportFile[]
   */
  public function getImports()
  {
    return $this->imports;
  }
  /**
   * Output only. Creation timestamp in RFC3339 text format.
   *
   * @param string $insertTime
   */
  public function setInsertTime($insertTime)
  {
    $this->insertTime = $insertTime;
  }
  /**
   * @return string
   */
  public function getInsertTime()
  {
    return $this->insertTime;
  }
  /**
   * Output only. The YAML layout for this manifest.
   *
   * @param string $layout
   */
  public function setLayout($layout)
  {
    $this->layout = $layout;
  }
  /**
   * @return string
   */
  public function getLayout()
  {
    return $this->layout;
  }
  /**
   * Output only. The computed size of the fully expanded manifest.
   *
   * @param string $manifestSizeBytes
   */
  public function setManifestSizeBytes($manifestSizeBytes)
  {
    $this->manifestSizeBytes = $manifestSizeBytes;
  }
  /**
   * @return string
   */
  public function getManifestSizeBytes()
  {
    return $this->manifestSizeBytes;
  }
  /**
   * Output only. The size limit for expanded manifests in the project.
   *
   * @param string $manifestSizeLimitBytes
   */
  public function setManifestSizeLimitBytes($manifestSizeLimitBytes)
  {
    $this->manifestSizeLimitBytes = $manifestSizeLimitBytes;
  }
  /**
   * @return string
   */
  public function getManifestSizeLimitBytes()
  {
    return $this->manifestSizeLimitBytes;
  }
  /**
   * Output only. The name of the manifest.
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
   * Output only. Self link for the manifest.
   *
   * @param string $selfLink
   */
  public function setSelfLink($selfLink)
  {
    $this->selfLink = $selfLink;
  }
  /**
   * @return string
   */
  public function getSelfLink()
  {
    return $this->selfLink;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Manifest::class, 'Google_Service_DeploymentManager_Manifest');
