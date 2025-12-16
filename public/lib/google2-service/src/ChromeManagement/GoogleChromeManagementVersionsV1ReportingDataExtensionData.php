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

namespace Google\Service\ChromeManagement;

class GoogleChromeManagementVersionsV1ReportingDataExtensionData extends \Google\Collection
{
  /**
   * Represents an unspecified extension type.
   */
  public const EXTENSION_TYPE_EXTENSION_TYPE_UNSPECIFIED = 'EXTENSION_TYPE_UNSPECIFIED';
  /**
   * Represents an extension.
   */
  public const EXTENSION_TYPE_EXTENSION = 'EXTENSION';
  /**
   * Represents an app.
   */
  public const EXTENSION_TYPE_APP = 'APP';
  /**
   * Represents a theme.
   */
  public const EXTENSION_TYPE_THEME = 'THEME';
  /**
   * Represents a hosted app.
   */
  public const EXTENSION_TYPE_HOSTED_APP = 'HOSTED_APP';
  /**
   * Represents an unspecified installation type.
   */
  public const INSTALLATION_TYPE_INSTALLATION_TYPE_UNSPECIFIED = 'INSTALLATION_TYPE_UNSPECIFIED';
  /**
   * Represents instances of the extension having mixed installation types.
   */
  public const INSTALLATION_TYPE_MULTIPLE = 'MULTIPLE';
  /**
   * Represents a normal installation type.
   */
  public const INSTALLATION_TYPE_NORMAL = 'NORMAL';
  /**
   * Represents an installation by admin.
   */
  public const INSTALLATION_TYPE_ADMIN = 'ADMIN';
  /**
   * Represents a development installation type.
   */
  public const INSTALLATION_TYPE_DEVELOPMENT = 'DEVELOPMENT';
  /**
   * Represents a sideload installation type.
   */
  public const INSTALLATION_TYPE_SIDELOAD = 'SIDELOAD';
  /**
   * Represents an installation type that is not covered in the other options.
   */
  public const INSTALLATION_TYPE_OTHER = 'OTHER';
  protected $collection_key = 'permissions';
  /**
   * Output only. Description of the extension.
   *
   * @var string
   */
  public $description;
  /**
   * Output only. ID of the extension.
   *
   * @var string
   */
  public $extensionId;
  /**
   * Output only. Type of the extension.
   *
   * @var string
   */
  public $extensionType;
  /**
   * Output only. The URL of the homepage of the extension.
   *
   * @var string
   */
  public $homepageUri;
  /**
   * Output only. Installation type of the extension.
   *
   * @var string
   */
  public $installationType;
  /**
   * Output only. Represents whether the user disabled the extension.
   *
   * @var bool
   */
  public $isDisabled;
  /**
   * Output only. Represents whether the extension is from the webstore.
   *
   * @var bool
   */
  public $isWebstoreExtension;
  /**
   * Output only. Manifest version of the extension.
   *
   * @var int
   */
  public $manifestVersion;
  /**
   * Output only. Name of the extension.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Permissions requested by the extension.
   *
   * @var string[]
   */
  public $permissions;
  /**
   * Output only. Version of the extension.
   *
   * @var string
   */
  public $version;

  /**
   * Output only. Description of the extension.
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
   * Output only. ID of the extension.
   *
   * @param string $extensionId
   */
  public function setExtensionId($extensionId)
  {
    $this->extensionId = $extensionId;
  }
  /**
   * @return string
   */
  public function getExtensionId()
  {
    return $this->extensionId;
  }
  /**
   * Output only. Type of the extension.
   *
   * Accepted values: EXTENSION_TYPE_UNSPECIFIED, EXTENSION, APP, THEME,
   * HOSTED_APP
   *
   * @param self::EXTENSION_TYPE_* $extensionType
   */
  public function setExtensionType($extensionType)
  {
    $this->extensionType = $extensionType;
  }
  /**
   * @return self::EXTENSION_TYPE_*
   */
  public function getExtensionType()
  {
    return $this->extensionType;
  }
  /**
   * Output only. The URL of the homepage of the extension.
   *
   * @param string $homepageUri
   */
  public function setHomepageUri($homepageUri)
  {
    $this->homepageUri = $homepageUri;
  }
  /**
   * @return string
   */
  public function getHomepageUri()
  {
    return $this->homepageUri;
  }
  /**
   * Output only. Installation type of the extension.
   *
   * Accepted values: INSTALLATION_TYPE_UNSPECIFIED, MULTIPLE, NORMAL, ADMIN,
   * DEVELOPMENT, SIDELOAD, OTHER
   *
   * @param self::INSTALLATION_TYPE_* $installationType
   */
  public function setInstallationType($installationType)
  {
    $this->installationType = $installationType;
  }
  /**
   * @return self::INSTALLATION_TYPE_*
   */
  public function getInstallationType()
  {
    return $this->installationType;
  }
  /**
   * Output only. Represents whether the user disabled the extension.
   *
   * @param bool $isDisabled
   */
  public function setIsDisabled($isDisabled)
  {
    $this->isDisabled = $isDisabled;
  }
  /**
   * @return bool
   */
  public function getIsDisabled()
  {
    return $this->isDisabled;
  }
  /**
   * Output only. Represents whether the extension is from the webstore.
   *
   * @param bool $isWebstoreExtension
   */
  public function setIsWebstoreExtension($isWebstoreExtension)
  {
    $this->isWebstoreExtension = $isWebstoreExtension;
  }
  /**
   * @return bool
   */
  public function getIsWebstoreExtension()
  {
    return $this->isWebstoreExtension;
  }
  /**
   * Output only. Manifest version of the extension.
   *
   * @param int $manifestVersion
   */
  public function setManifestVersion($manifestVersion)
  {
    $this->manifestVersion = $manifestVersion;
  }
  /**
   * @return int
   */
  public function getManifestVersion()
  {
    return $this->manifestVersion;
  }
  /**
   * Output only. Name of the extension.
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
   * Output only. Permissions requested by the extension.
   *
   * @param string[] $permissions
   */
  public function setPermissions($permissions)
  {
    $this->permissions = $permissions;
  }
  /**
   * @return string[]
   */
  public function getPermissions()
  {
    return $this->permissions;
  }
  /**
   * Output only. Version of the extension.
   *
   * @param string $version
   */
  public function setVersion($version)
  {
    $this->version = $version;
  }
  /**
   * @return string
   */
  public function getVersion()
  {
    return $this->version;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleChromeManagementVersionsV1ReportingDataExtensionData::class, 'Google_Service_ChromeManagement_GoogleChromeManagementVersionsV1ReportingDataExtensionData');
