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

class GoogleCloudAiplatformV1SharePointSourcesSharePointSource extends \Google\Model
{
  /**
   * The Application ID for the app registered in Microsoft Azure Portal. The
   * application must also be configured with MS Graph permissions
   * "Files.ReadAll", "Sites.ReadAll" and BrowserSiteLists.Read.All.
   *
   * @var string
   */
  public $clientId;
  protected $clientSecretType = GoogleCloudAiplatformV1ApiAuthApiKeyConfig::class;
  protected $clientSecretDataType = '';
  /**
   * The ID of the drive to download from.
   *
   * @var string
   */
  public $driveId;
  /**
   * The name of the drive to download from.
   *
   * @var string
   */
  public $driveName;
  /**
   * Output only. The SharePoint file id. Output only.
   *
   * @var string
   */
  public $fileId;
  /**
   * The ID of the SharePoint folder to download from.
   *
   * @var string
   */
  public $sharepointFolderId;
  /**
   * The path of the SharePoint folder to download from.
   *
   * @var string
   */
  public $sharepointFolderPath;
  /**
   * The name of the SharePoint site to download from. This can be the site name
   * or the site id.
   *
   * @var string
   */
  public $sharepointSiteName;
  /**
   * Unique identifier of the Azure Active Directory Instance.
   *
   * @var string
   */
  public $tenantId;

  /**
   * The Application ID for the app registered in Microsoft Azure Portal. The
   * application must also be configured with MS Graph permissions
   * "Files.ReadAll", "Sites.ReadAll" and BrowserSiteLists.Read.All.
   *
   * @param string $clientId
   */
  public function setClientId($clientId)
  {
    $this->clientId = $clientId;
  }
  /**
   * @return string
   */
  public function getClientId()
  {
    return $this->clientId;
  }
  /**
   * The application secret for the app registered in Azure.
   *
   * @param GoogleCloudAiplatformV1ApiAuthApiKeyConfig $clientSecret
   */
  public function setClientSecret(GoogleCloudAiplatformV1ApiAuthApiKeyConfig $clientSecret)
  {
    $this->clientSecret = $clientSecret;
  }
  /**
   * @return GoogleCloudAiplatformV1ApiAuthApiKeyConfig
   */
  public function getClientSecret()
  {
    return $this->clientSecret;
  }
  /**
   * The ID of the drive to download from.
   *
   * @param string $driveId
   */
  public function setDriveId($driveId)
  {
    $this->driveId = $driveId;
  }
  /**
   * @return string
   */
  public function getDriveId()
  {
    return $this->driveId;
  }
  /**
   * The name of the drive to download from.
   *
   * @param string $driveName
   */
  public function setDriveName($driveName)
  {
    $this->driveName = $driveName;
  }
  /**
   * @return string
   */
  public function getDriveName()
  {
    return $this->driveName;
  }
  /**
   * Output only. The SharePoint file id. Output only.
   *
   * @param string $fileId
   */
  public function setFileId($fileId)
  {
    $this->fileId = $fileId;
  }
  /**
   * @return string
   */
  public function getFileId()
  {
    return $this->fileId;
  }
  /**
   * The ID of the SharePoint folder to download from.
   *
   * @param string $sharepointFolderId
   */
  public function setSharepointFolderId($sharepointFolderId)
  {
    $this->sharepointFolderId = $sharepointFolderId;
  }
  /**
   * @return string
   */
  public function getSharepointFolderId()
  {
    return $this->sharepointFolderId;
  }
  /**
   * The path of the SharePoint folder to download from.
   *
   * @param string $sharepointFolderPath
   */
  public function setSharepointFolderPath($sharepointFolderPath)
  {
    $this->sharepointFolderPath = $sharepointFolderPath;
  }
  /**
   * @return string
   */
  public function getSharepointFolderPath()
  {
    return $this->sharepointFolderPath;
  }
  /**
   * The name of the SharePoint site to download from. This can be the site name
   * or the site id.
   *
   * @param string $sharepointSiteName
   */
  public function setSharepointSiteName($sharepointSiteName)
  {
    $this->sharepointSiteName = $sharepointSiteName;
  }
  /**
   * @return string
   */
  public function getSharepointSiteName()
  {
    return $this->sharepointSiteName;
  }
  /**
   * Unique identifier of the Azure Active Directory Instance.
   *
   * @param string $tenantId
   */
  public function setTenantId($tenantId)
  {
    $this->tenantId = $tenantId;
  }
  /**
   * @return string
   */
  public function getTenantId()
  {
    return $this->tenantId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1SharePointSourcesSharePointSource::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1SharePointSourcesSharePointSource');
