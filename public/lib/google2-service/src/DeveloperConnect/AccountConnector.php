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

namespace Google\Service\DeveloperConnect;

class AccountConnector extends \Google\Model
{
  /**
   * Optional. Allows users to store small amounts of arbitrary data.
   *
   * @var string[]
   */
  public $annotations;
  /**
   * Output only. The timestamp when the accountConnector was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @var string
   */
  public $etag;
  /**
   * Optional. Labels as key value pairs
   *
   * @var string[]
   */
  public $labels;
  /**
   * Identifier. The resource name of the accountConnector, in the format `proje
   * cts/{project}/locations/{location}/accountConnectors/{account_connector_id}
   * `.
   *
   * @var string
   */
  public $name;
  /**
   * Output only. Start OAuth flow by clicking on this URL.
   *
   * @var string
   */
  public $oauthStartUri;
  protected $providerOauthConfigType = ProviderOAuthConfig::class;
  protected $providerOauthConfigDataType = '';
  /**
   * Output only. The timestamp when the accountConnector was updated.
   *
   * @var string
   */
  public $updateTime;

  /**
   * Optional. Allows users to store small amounts of arbitrary data.
   *
   * @param string[] $annotations
   */
  public function setAnnotations($annotations)
  {
    $this->annotations = $annotations;
  }
  /**
   * @return string[]
   */
  public function getAnnotations()
  {
    return $this->annotations;
  }
  /**
   * Output only. The timestamp when the accountConnector was created.
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
   * Optional. This checksum is computed by the server based on the value of
   * other fields, and may be sent on update and delete requests to ensure the
   * client has an up-to-date value before proceeding.
   *
   * @param string $etag
   */
  public function setEtag($etag)
  {
    $this->etag = $etag;
  }
  /**
   * @return string
   */
  public function getEtag()
  {
    return $this->etag;
  }
  /**
   * Optional. Labels as key value pairs
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
   * Identifier. The resource name of the accountConnector, in the format `proje
   * cts/{project}/locations/{location}/accountConnectors/{account_connector_id}
   * `.
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
   * Output only. Start OAuth flow by clicking on this URL.
   *
   * @param string $oauthStartUri
   */
  public function setOauthStartUri($oauthStartUri)
  {
    $this->oauthStartUri = $oauthStartUri;
  }
  /**
   * @return string
   */
  public function getOauthStartUri()
  {
    return $this->oauthStartUri;
  }
  /**
   * Provider OAuth config.
   *
   * @param ProviderOAuthConfig $providerOauthConfig
   */
  public function setProviderOauthConfig(ProviderOAuthConfig $providerOauthConfig)
  {
    $this->providerOauthConfig = $providerOauthConfig;
  }
  /**
   * @return ProviderOAuthConfig
   */
  public function getProviderOauthConfig()
  {
    return $this->providerOauthConfig;
  }
  /**
   * Output only. The timestamp when the accountConnector was updated.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(AccountConnector::class, 'Google_Service_DeveloperConnect_AccountConnector');
