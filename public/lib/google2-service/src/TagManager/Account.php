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

namespace Google\Service\TagManager;

class Account extends \Google\Model
{
  /**
   * The Account ID uniquely identifies the GTM Account.
   *
   * @var string
   */
  public $accountId;
  protected $featuresType = AccountFeatures::class;
  protected $featuresDataType = '';
  /**
   * The fingerprint of the GTM Account as computed at storage time. This value
   * is recomputed whenever the account is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Account display name.
   *
   * @var string
   */
  public $name;
  /**
   * GTM Account's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Whether the account shares data anonymously with Google and others. This
   * flag enables benchmarking by sharing your data in an anonymous form. Google
   * will remove all identifiable information about your website, combine the
   * data with hundreds of other anonymous sites and report aggregate trends in
   * the benchmarking service.
   *
   * @var bool
   */
  public $shareData;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;

  /**
   * The Account ID uniquely identifies the GTM Account.
   *
   * @param string $accountId
   */
  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }
  /**
   * @return string
   */
  public function getAccountId()
  {
    return $this->accountId;
  }
  /**
   * Read-only Account feature set
   *
   * @param AccountFeatures $features
   */
  public function setFeatures(AccountFeatures $features)
  {
    $this->features = $features;
  }
  /**
   * @return AccountFeatures
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * The fingerprint of the GTM Account as computed at storage time. This value
   * is recomputed whenever the account is modified.
   *
   * @param string $fingerprint
   */
  public function setFingerprint($fingerprint)
  {
    $this->fingerprint = $fingerprint;
  }
  /**
   * @return string
   */
  public function getFingerprint()
  {
    return $this->fingerprint;
  }
  /**
   * Account display name.
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
   * GTM Account's API relative path.
   *
   * @param string $path
   */
  public function setPath($path)
  {
    $this->path = $path;
  }
  /**
   * @return string
   */
  public function getPath()
  {
    return $this->path;
  }
  /**
   * Whether the account shares data anonymously with Google and others. This
   * flag enables benchmarking by sharing your data in an anonymous form. Google
   * will remove all identifiable information about your website, combine the
   * data with hundreds of other anonymous sites and report aggregate trends in
   * the benchmarking service.
   *
   * @param bool $shareData
   */
  public function setShareData($shareData)
  {
    $this->shareData = $shareData;
  }
  /**
   * @return bool
   */
  public function getShareData()
  {
    return $this->shareData;
  }
  /**
   * Auto generated link to the tag manager UI
   *
   * @param string $tagManagerUrl
   */
  public function setTagManagerUrl($tagManagerUrl)
  {
    $this->tagManagerUrl = $tagManagerUrl;
  }
  /**
   * @return string
   */
  public function getTagManagerUrl()
  {
    return $this->tagManagerUrl;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Account::class, 'Google_Service_TagManager_Account');
