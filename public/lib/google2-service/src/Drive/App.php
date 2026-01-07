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

namespace Google\Service\Drive;

class App extends \Google\Collection
{
  protected $collection_key = 'secondaryMimeTypes';
  /**
   * Whether the app is authorized to access data on the user's Drive.
   *
   * @var bool
   */
  public $authorized;
  /**
   * The template URL to create a file with this app in a given folder. The
   * template contains the {folderId} to be replaced by the folder ID house the
   * new file.
   *
   * @var string
   */
  public $createInFolderTemplate;
  /**
   * The URL to create a file with this app.
   *
   * @var string
   */
  public $createUrl;
  /**
   * Whether the app has Drive-wide scope. An app with Drive-wide scope can
   * access all files in the user's Drive.
   *
   * @var bool
   */
  public $hasDriveWideScope;
  protected $iconsType = AppIcons::class;
  protected $iconsDataType = 'array';
  /**
   * The ID of the app.
   *
   * @var string
   */
  public $id;
  /**
   * Whether the app is installed.
   *
   * @var bool
   */
  public $installed;
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string "drive#app".
   *
   * @var string
   */
  public $kind;
  /**
   * A long description of the app.
   *
   * @var string
   */
  public $longDescription;
  /**
   * The name of the app.
   *
   * @var string
   */
  public $name;
  /**
   * The type of object this app creates such as a Chart. If empty, the app name
   * should be used instead.
   *
   * @var string
   */
  public $objectType;
  /**
   * The template URL for opening files with this app. The template contains
   * {ids} or {exportIds} to be replaced by the actual file IDs. For more
   * information, see Open Files for the full documentation.
   *
   * @var string
   */
  public $openUrlTemplate;
  /**
   * The list of primary file extensions.
   *
   * @var string[]
   */
  public $primaryFileExtensions;
  /**
   * The list of primary MIME types.
   *
   * @var string[]
   */
  public $primaryMimeTypes;
  /**
   * The ID of the product listing for this app.
   *
   * @var string
   */
  public $productId;
  /**
   * A link to the product listing for this app.
   *
   * @var string
   */
  public $productUrl;
  /**
   * The list of secondary file extensions.
   *
   * @var string[]
   */
  public $secondaryFileExtensions;
  /**
   * The list of secondary MIME types.
   *
   * @var string[]
   */
  public $secondaryMimeTypes;
  /**
   * A short description of the app.
   *
   * @var string
   */
  public $shortDescription;
  /**
   * Whether this app supports creating objects.
   *
   * @var bool
   */
  public $supportsCreate;
  /**
   * Whether this app supports importing from Google Docs.
   *
   * @var bool
   */
  public $supportsImport;
  /**
   * Whether this app supports opening more than one file.
   *
   * @var bool
   */
  public $supportsMultiOpen;
  /**
   * Whether this app supports creating files when offline.
   *
   * @var bool
   */
  public $supportsOfflineCreate;
  /**
   * Whether the app is selected as the default handler for the types it
   * supports.
   *
   * @var bool
   */
  public $useByDefault;

  /**
   * Whether the app is authorized to access data on the user's Drive.
   *
   * @param bool $authorized
   */
  public function setAuthorized($authorized)
  {
    $this->authorized = $authorized;
  }
  /**
   * @return bool
   */
  public function getAuthorized()
  {
    return $this->authorized;
  }
  /**
   * The template URL to create a file with this app in a given folder. The
   * template contains the {folderId} to be replaced by the folder ID house the
   * new file.
   *
   * @param string $createInFolderTemplate
   */
  public function setCreateInFolderTemplate($createInFolderTemplate)
  {
    $this->createInFolderTemplate = $createInFolderTemplate;
  }
  /**
   * @return string
   */
  public function getCreateInFolderTemplate()
  {
    return $this->createInFolderTemplate;
  }
  /**
   * The URL to create a file with this app.
   *
   * @param string $createUrl
   */
  public function setCreateUrl($createUrl)
  {
    $this->createUrl = $createUrl;
  }
  /**
   * @return string
   */
  public function getCreateUrl()
  {
    return $this->createUrl;
  }
  /**
   * Whether the app has Drive-wide scope. An app with Drive-wide scope can
   * access all files in the user's Drive.
   *
   * @param bool $hasDriveWideScope
   */
  public function setHasDriveWideScope($hasDriveWideScope)
  {
    $this->hasDriveWideScope = $hasDriveWideScope;
  }
  /**
   * @return bool
   */
  public function getHasDriveWideScope()
  {
    return $this->hasDriveWideScope;
  }
  /**
   * The various icons for the app.
   *
   * @param AppIcons[] $icons
   */
  public function setIcons($icons)
  {
    $this->icons = $icons;
  }
  /**
   * @return AppIcons[]
   */
  public function getIcons()
  {
    return $this->icons;
  }
  /**
   * The ID of the app.
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
   * Whether the app is installed.
   *
   * @param bool $installed
   */
  public function setInstalled($installed)
  {
    $this->installed = $installed;
  }
  /**
   * @return bool
   */
  public function getInstalled()
  {
    return $this->installed;
  }
  /**
   * Output only. Identifies what kind of resource this is. Value: the fixed
   * string "drive#app".
   *
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * A long description of the app.
   *
   * @param string $longDescription
   */
  public function setLongDescription($longDescription)
  {
    $this->longDescription = $longDescription;
  }
  /**
   * @return string
   */
  public function getLongDescription()
  {
    return $this->longDescription;
  }
  /**
   * The name of the app.
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
   * The type of object this app creates such as a Chart. If empty, the app name
   * should be used instead.
   *
   * @param string $objectType
   */
  public function setObjectType($objectType)
  {
    $this->objectType = $objectType;
  }
  /**
   * @return string
   */
  public function getObjectType()
  {
    return $this->objectType;
  }
  /**
   * The template URL for opening files with this app. The template contains
   * {ids} or {exportIds} to be replaced by the actual file IDs. For more
   * information, see Open Files for the full documentation.
   *
   * @param string $openUrlTemplate
   */
  public function setOpenUrlTemplate($openUrlTemplate)
  {
    $this->openUrlTemplate = $openUrlTemplate;
  }
  /**
   * @return string
   */
  public function getOpenUrlTemplate()
  {
    return $this->openUrlTemplate;
  }
  /**
   * The list of primary file extensions.
   *
   * @param string[] $primaryFileExtensions
   */
  public function setPrimaryFileExtensions($primaryFileExtensions)
  {
    $this->primaryFileExtensions = $primaryFileExtensions;
  }
  /**
   * @return string[]
   */
  public function getPrimaryFileExtensions()
  {
    return $this->primaryFileExtensions;
  }
  /**
   * The list of primary MIME types.
   *
   * @param string[] $primaryMimeTypes
   */
  public function setPrimaryMimeTypes($primaryMimeTypes)
  {
    $this->primaryMimeTypes = $primaryMimeTypes;
  }
  /**
   * @return string[]
   */
  public function getPrimaryMimeTypes()
  {
    return $this->primaryMimeTypes;
  }
  /**
   * The ID of the product listing for this app.
   *
   * @param string $productId
   */
  public function setProductId($productId)
  {
    $this->productId = $productId;
  }
  /**
   * @return string
   */
  public function getProductId()
  {
    return $this->productId;
  }
  /**
   * A link to the product listing for this app.
   *
   * @param string $productUrl
   */
  public function setProductUrl($productUrl)
  {
    $this->productUrl = $productUrl;
  }
  /**
   * @return string
   */
  public function getProductUrl()
  {
    return $this->productUrl;
  }
  /**
   * The list of secondary file extensions.
   *
   * @param string[] $secondaryFileExtensions
   */
  public function setSecondaryFileExtensions($secondaryFileExtensions)
  {
    $this->secondaryFileExtensions = $secondaryFileExtensions;
  }
  /**
   * @return string[]
   */
  public function getSecondaryFileExtensions()
  {
    return $this->secondaryFileExtensions;
  }
  /**
   * The list of secondary MIME types.
   *
   * @param string[] $secondaryMimeTypes
   */
  public function setSecondaryMimeTypes($secondaryMimeTypes)
  {
    $this->secondaryMimeTypes = $secondaryMimeTypes;
  }
  /**
   * @return string[]
   */
  public function getSecondaryMimeTypes()
  {
    return $this->secondaryMimeTypes;
  }
  /**
   * A short description of the app.
   *
   * @param string $shortDescription
   */
  public function setShortDescription($shortDescription)
  {
    $this->shortDescription = $shortDescription;
  }
  /**
   * @return string
   */
  public function getShortDescription()
  {
    return $this->shortDescription;
  }
  /**
   * Whether this app supports creating objects.
   *
   * @param bool $supportsCreate
   */
  public function setSupportsCreate($supportsCreate)
  {
    $this->supportsCreate = $supportsCreate;
  }
  /**
   * @return bool
   */
  public function getSupportsCreate()
  {
    return $this->supportsCreate;
  }
  /**
   * Whether this app supports importing from Google Docs.
   *
   * @param bool $supportsImport
   */
  public function setSupportsImport($supportsImport)
  {
    $this->supportsImport = $supportsImport;
  }
  /**
   * @return bool
   */
  public function getSupportsImport()
  {
    return $this->supportsImport;
  }
  /**
   * Whether this app supports opening more than one file.
   *
   * @param bool $supportsMultiOpen
   */
  public function setSupportsMultiOpen($supportsMultiOpen)
  {
    $this->supportsMultiOpen = $supportsMultiOpen;
  }
  /**
   * @return bool
   */
  public function getSupportsMultiOpen()
  {
    return $this->supportsMultiOpen;
  }
  /**
   * Whether this app supports creating files when offline.
   *
   * @param bool $supportsOfflineCreate
   */
  public function setSupportsOfflineCreate($supportsOfflineCreate)
  {
    $this->supportsOfflineCreate = $supportsOfflineCreate;
  }
  /**
   * @return bool
   */
  public function getSupportsOfflineCreate()
  {
    return $this->supportsOfflineCreate;
  }
  /**
   * Whether the app is selected as the default handler for the types it
   * supports.
   *
   * @param bool $useByDefault
   */
  public function setUseByDefault($useByDefault)
  {
    $this->useByDefault = $useByDefault;
  }
  /**
   * @return bool
   */
  public function getUseByDefault()
  {
    return $this->useByDefault;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(App::class, 'Google_Service_Drive_App');
