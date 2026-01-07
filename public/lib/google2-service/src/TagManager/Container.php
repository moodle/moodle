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

class Container extends \Google\Collection
{
  protected $collection_key = 'usageContext';
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * The Container ID uniquely identifies the GTM Container.
   *
   * @var string
   */
  public $containerId;
  /**
   * List of domain names associated with the Container.
   *
   * @var string[]
   */
  public $domainName;
  protected $featuresType = ContainerFeatures::class;
  protected $featuresDataType = '';
  /**
   * The fingerprint of the GTM Container as computed at storage time. This
   * value is recomputed whenever the account is modified.
   *
   * @var string
   */
  public $fingerprint;
  /**
   * Container display name.
   *
   * @var string
   */
  public $name;
  /**
   * Container Notes.
   *
   * @var string
   */
  public $notes;
  /**
   * GTM Container's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Container Public ID.
   *
   * @var string
   */
  public $publicId;
  /**
   * All Tag IDs that refer to this Container.
   *
   * @var string[]
   */
  public $tagIds;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  /**
   * List of server-side container URLs for the Container. If multiple URLs are
   * provided, all URL paths must match.
   *
   * @var string[]
   */
  public $taggingServerUrls;
  /**
   * List of Usage Contexts for the Container. Valid values include: web,
   * android, or ios.
   *
   * @var string[]
   */
  public $usageContext;

  /**
   * GTM Account ID.
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
   * The Container ID uniquely identifies the GTM Container.
   *
   * @param string $containerId
   */
  public function setContainerId($containerId)
  {
    $this->containerId = $containerId;
  }
  /**
   * @return string
   */
  public function getContainerId()
  {
    return $this->containerId;
  }
  /**
   * List of domain names associated with the Container.
   *
   * @param string[] $domainName
   */
  public function setDomainName($domainName)
  {
    $this->domainName = $domainName;
  }
  /**
   * @return string[]
   */
  public function getDomainName()
  {
    return $this->domainName;
  }
  /**
   * Read-only Container feature set.
   *
   * @param ContainerFeatures $features
   */
  public function setFeatures(ContainerFeatures $features)
  {
    $this->features = $features;
  }
  /**
   * @return ContainerFeatures
   */
  public function getFeatures()
  {
    return $this->features;
  }
  /**
   * The fingerprint of the GTM Container as computed at storage time. This
   * value is recomputed whenever the account is modified.
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
   * Container display name.
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
   * Container Notes.
   *
   * @param string $notes
   */
  public function setNotes($notes)
  {
    $this->notes = $notes;
  }
  /**
   * @return string
   */
  public function getNotes()
  {
    return $this->notes;
  }
  /**
   * GTM Container's API relative path.
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
   * Container Public ID.
   *
   * @param string $publicId
   */
  public function setPublicId($publicId)
  {
    $this->publicId = $publicId;
  }
  /**
   * @return string
   */
  public function getPublicId()
  {
    return $this->publicId;
  }
  /**
   * All Tag IDs that refer to this Container.
   *
   * @param string[] $tagIds
   */
  public function setTagIds($tagIds)
  {
    $this->tagIds = $tagIds;
  }
  /**
   * @return string[]
   */
  public function getTagIds()
  {
    return $this->tagIds;
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
  /**
   * List of server-side container URLs for the Container. If multiple URLs are
   * provided, all URL paths must match.
   *
   * @param string[] $taggingServerUrls
   */
  public function setTaggingServerUrls($taggingServerUrls)
  {
    $this->taggingServerUrls = $taggingServerUrls;
  }
  /**
   * @return string[]
   */
  public function getTaggingServerUrls()
  {
    return $this->taggingServerUrls;
  }
  /**
   * List of Usage Contexts for the Container. Valid values include: web,
   * android, or ios.
   *
   * @param string[] $usageContext
   */
  public function setUsageContext($usageContext)
  {
    $this->usageContext = $usageContext;
  }
  /**
   * @return string[]
   */
  public function getUsageContext()
  {
    return $this->usageContext;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Container::class, 'Google_Service_TagManager_Container');
