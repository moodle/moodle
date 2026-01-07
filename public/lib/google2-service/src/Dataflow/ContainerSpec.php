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

namespace Google\Service\Dataflow;

class ContainerSpec extends \Google\Model
{
  protected $defaultEnvironmentType = FlexTemplateRuntimeEnvironment::class;
  protected $defaultEnvironmentDataType = '';
  /**
   * Name of the docker container image. E.g., gcr.io/project/some-image
   *
   * @var string
   */
  public $image;
  /**
   * Cloud Storage path to self-signed certificate of private registry.
   *
   * @var string
   */
  public $imageRepositoryCertPath;
  /**
   * Secret Manager secret id for password to authenticate to private registry.
   *
   * @var string
   */
  public $imageRepositoryPasswordSecretId;
  /**
   * Secret Manager secret id for username to authenticate to private registry.
   *
   * @var string
   */
  public $imageRepositoryUsernameSecretId;
  protected $metadataType = TemplateMetadata::class;
  protected $metadataDataType = '';
  protected $sdkInfoType = SDKInfo::class;
  protected $sdkInfoDataType = '';

  /**
   * Default runtime environment for the job.
   *
   * @param FlexTemplateRuntimeEnvironment $defaultEnvironment
   */
  public function setDefaultEnvironment(FlexTemplateRuntimeEnvironment $defaultEnvironment)
  {
    $this->defaultEnvironment = $defaultEnvironment;
  }
  /**
   * @return FlexTemplateRuntimeEnvironment
   */
  public function getDefaultEnvironment()
  {
    return $this->defaultEnvironment;
  }
  /**
   * Name of the docker container image. E.g., gcr.io/project/some-image
   *
   * @param string $image
   */
  public function setImage($image)
  {
    $this->image = $image;
  }
  /**
   * @return string
   */
  public function getImage()
  {
    return $this->image;
  }
  /**
   * Cloud Storage path to self-signed certificate of private registry.
   *
   * @param string $imageRepositoryCertPath
   */
  public function setImageRepositoryCertPath($imageRepositoryCertPath)
  {
    $this->imageRepositoryCertPath = $imageRepositoryCertPath;
  }
  /**
   * @return string
   */
  public function getImageRepositoryCertPath()
  {
    return $this->imageRepositoryCertPath;
  }
  /**
   * Secret Manager secret id for password to authenticate to private registry.
   *
   * @param string $imageRepositoryPasswordSecretId
   */
  public function setImageRepositoryPasswordSecretId($imageRepositoryPasswordSecretId)
  {
    $this->imageRepositoryPasswordSecretId = $imageRepositoryPasswordSecretId;
  }
  /**
   * @return string
   */
  public function getImageRepositoryPasswordSecretId()
  {
    return $this->imageRepositoryPasswordSecretId;
  }
  /**
   * Secret Manager secret id for username to authenticate to private registry.
   *
   * @param string $imageRepositoryUsernameSecretId
   */
  public function setImageRepositoryUsernameSecretId($imageRepositoryUsernameSecretId)
  {
    $this->imageRepositoryUsernameSecretId = $imageRepositoryUsernameSecretId;
  }
  /**
   * @return string
   */
  public function getImageRepositoryUsernameSecretId()
  {
    return $this->imageRepositoryUsernameSecretId;
  }
  /**
   * Metadata describing a template including description and validation rules.
   *
   * @param TemplateMetadata $metadata
   */
  public function setMetadata(TemplateMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return TemplateMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * Required. SDK info of the Flex Template.
   *
   * @param SDKInfo $sdkInfo
   */
  public function setSdkInfo(SDKInfo $sdkInfo)
  {
    $this->sdkInfo = $sdkInfo;
  }
  /**
   * @return SDKInfo
   */
  public function getSdkInfo()
  {
    return $this->sdkInfo;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ContainerSpec::class, 'Google_Service_Dataflow_ContainerSpec');
