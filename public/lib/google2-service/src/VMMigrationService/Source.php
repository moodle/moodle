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

namespace Google\Service\VMMigrationService;

class Source extends \Google\Model
{
  protected $awsType = AwsSourceDetails::class;
  protected $awsDataType = '';
  protected $azureType = AzureSourceDetails::class;
  protected $azureDataType = '';
  /**
   * Output only. The create time timestamp.
   *
   * @var string
   */
  public $createTime;
  /**
   * User-provided description of the source.
   *
   * @var string
   */
  public $description;
  protected $encryptionType = Encryption::class;
  protected $encryptionDataType = '';
  /**
   * The labels of the source.
   *
   * @var string[]
   */
  public $labels;
  /**
   * Output only. The Source name.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. The update time timestamp.
   *
   * @var string
   */
  public $updateTime;
  protected $vmwareType = VmwareSourceDetails::class;
  protected $vmwareDataType = '';

  /**
   * AWS type source details.
   *
   * @param AwsSourceDetails $aws
   */
  public function setAws(AwsSourceDetails $aws)
  {
    $this->aws = $aws;
  }
  /**
   * @return AwsSourceDetails
   */
  public function getAws()
  {
    return $this->aws;
  }
  /**
   * Azure type source details.
   *
   * @param AzureSourceDetails $azure
   */
  public function setAzure(AzureSourceDetails $azure)
  {
    $this->azure = $azure;
  }
  /**
   * @return AzureSourceDetails
   */
  public function getAzure()
  {
    return $this->azure;
  }
  /**
   * Output only. The create time timestamp.
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
   * User-provided description of the source.
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
   * Optional. Immutable. The encryption details of the source data stored by
   * the service.
   *
   * @param Encryption $encryption
   */
  public function setEncryption(Encryption $encryption)
  {
    $this->encryption = $encryption;
  }
  /**
   * @return Encryption
   */
  public function getEncryption()
  {
    return $this->encryption;
  }
  /**
   * The labels of the source.
   *
   * @param string[] $labels
   */
  public function setLabels($labels)
  {
    $this->labels = $labels;
  }
  /**
   * @return string[]
   */
  public function getLabels()
  {
    return $this->labels;
  }
  /**
   * Output only. The Source name.
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
   * Output only. The update time timestamp.
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
   * Vmware type source details.
   *
   * @param VmwareSourceDetails $vmware
   */
  public function setVmware(VmwareSourceDetails $vmware)
  {
    $this->vmware = $vmware;
  }
  /**
   * @return VmwareSourceDetails
   */
  public function getVmware()
  {
    return $this->vmware;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Source::class, 'Google_Service_VMMigrationService_Source');
