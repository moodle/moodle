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

class GoogleCloudAiplatformV1RagFile extends \Google\Model
{
  /**
   * Output only. Timestamp when this RagFile was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. The description of the RagFile.
   *
   * @var string
   */
  public $description;
  protected $directUploadSourceType = GoogleCloudAiplatformV1DirectUploadSource::class;
  protected $directUploadSourceDataType = '';
  /**
   * Required. The display name of the RagFile. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
   *
   * @var string
   */
  public $displayName;
  protected $fileStatusType = GoogleCloudAiplatformV1FileStatus::class;
  protected $fileStatusDataType = '';
  protected $gcsSourceType = GoogleCloudAiplatformV1GcsSource::class;
  protected $gcsSourceDataType = '';
  protected $googleDriveSourceType = GoogleCloudAiplatformV1GoogleDriveSource::class;
  protected $googleDriveSourceDataType = '';
  protected $jiraSourceType = GoogleCloudAiplatformV1JiraSource::class;
  protected $jiraSourceDataType = '';
  /**
   * Output only. The resource name of the RagFile.
   *
   * @var string
   */
  public $name;
  protected $sharePointSourcesType = GoogleCloudAiplatformV1SharePointSources::class;
  protected $sharePointSourcesDataType = '';
  protected $slackSourceType = GoogleCloudAiplatformV1SlackSource::class;
  protected $slackSourceDataType = '';
  /**
   * Output only. Timestamp when this RagFile was last updated.
   *
   * @var string
   */
  public $updateTime;
  /**
   * Output only. The metadata for metadata search. The user_metadata Needs to
   * be in JSON format.
   *
   * @var string
   */
  public $userMetadata;

  /**
   * Output only. Timestamp when this RagFile was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Optional. The description of the RagFile.
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
   * Output only. The RagFile is encapsulated and uploaded in the UploadRagFile
   * request.
   *
   * @param GoogleCloudAiplatformV1DirectUploadSource $directUploadSource
   */
  public function setDirectUploadSource(GoogleCloudAiplatformV1DirectUploadSource $directUploadSource)
  {
    $this->directUploadSource = $directUploadSource;
  }
  /**
   * @return GoogleCloudAiplatformV1DirectUploadSource
   */
  public function getDirectUploadSource()
  {
    return $this->directUploadSource;
  }
  /**
   * Required. The display name of the RagFile. The name can be up to 128
   * characters long and can consist of any UTF-8 characters.
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
   * Output only. State of the RagFile.
   *
   * @param GoogleCloudAiplatformV1FileStatus $fileStatus
   */
  public function setFileStatus(GoogleCloudAiplatformV1FileStatus $fileStatus)
  {
    $this->fileStatus = $fileStatus;
  }
  /**
   * @return GoogleCloudAiplatformV1FileStatus
   */
  public function getFileStatus()
  {
    return $this->fileStatus;
  }
  /**
   * Output only. Google Cloud Storage location of the RagFile. It does not
   * support wildcards in the Cloud Storage uri for now.
   *
   * @param GoogleCloudAiplatformV1GcsSource $gcsSource
   */
  public function setGcsSource(GoogleCloudAiplatformV1GcsSource $gcsSource)
  {
    $this->gcsSource = $gcsSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GcsSource
   */
  public function getGcsSource()
  {
    return $this->gcsSource;
  }
  /**
   * Output only. Google Drive location. Supports importing individual files as
   * well as Google Drive folders.
   *
   * @param GoogleCloudAiplatformV1GoogleDriveSource $googleDriveSource
   */
  public function setGoogleDriveSource(GoogleCloudAiplatformV1GoogleDriveSource $googleDriveSource)
  {
    $this->googleDriveSource = $googleDriveSource;
  }
  /**
   * @return GoogleCloudAiplatformV1GoogleDriveSource
   */
  public function getGoogleDriveSource()
  {
    return $this->googleDriveSource;
  }
  /**
   * The RagFile is imported from a Jira query.
   *
   * @param GoogleCloudAiplatformV1JiraSource $jiraSource
   */
  public function setJiraSource(GoogleCloudAiplatformV1JiraSource $jiraSource)
  {
    $this->jiraSource = $jiraSource;
  }
  /**
   * @return GoogleCloudAiplatformV1JiraSource
   */
  public function getJiraSource()
  {
    return $this->jiraSource;
  }
  /**
   * Output only. The resource name of the RagFile.
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
   * The RagFile is imported from a SharePoint source.
   *
   * @param GoogleCloudAiplatformV1SharePointSources $sharePointSources
   */
  public function setSharePointSources(GoogleCloudAiplatformV1SharePointSources $sharePointSources)
  {
    $this->sharePointSources = $sharePointSources;
  }
  /**
   * @return GoogleCloudAiplatformV1SharePointSources
   */
  public function getSharePointSources()
  {
    return $this->sharePointSources;
  }
  /**
   * The RagFile is imported from a Slack channel.
   *
   * @param GoogleCloudAiplatformV1SlackSource $slackSource
   */
  public function setSlackSource(GoogleCloudAiplatformV1SlackSource $slackSource)
  {
    $this->slackSource = $slackSource;
  }
  /**
   * @return GoogleCloudAiplatformV1SlackSource
   */
  public function getSlackSource()
  {
    return $this->slackSource;
  }
  /**
   * Output only. Timestamp when this RagFile was last updated.
   *
   * @param string $updateTime
   */
  public function setUpdateTime($updateTime)
  {
    $this->updateTime = $updateTime;
  }
  /**
   * @return string
   */
  public function getUpdateTime()
  {
    return $this->updateTime;
  }
  /**
   * Output only. The metadata for metadata search. The user_metadata Needs to
   * be in JSON format.
   *
   * @param string $userMetadata
   */
  public function setUserMetadata($userMetadata)
  {
    $this->userMetadata = $userMetadata;
  }
  /**
   * @return string
   */
  public function getUserMetadata()
  {
    return $this->userMetadata;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagFile::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagFile');
