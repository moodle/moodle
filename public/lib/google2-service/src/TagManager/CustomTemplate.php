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

class CustomTemplate extends \Google\Model
{
  /**
   * GTM Account ID.
   *
   * @var string
   */
  public $accountId;
  /**
   * GTM Container ID.
   *
   * @var string
   */
  public $containerId;
  /**
   * The fingerprint of the GTM Custom Template as computed at storage time.
   * This value is recomputed whenever the template is modified.
   *
   * @var string
   */
  public $fingerprint;
  protected $galleryReferenceType = GalleryReference::class;
  protected $galleryReferenceDataType = '';
  /**
   * Custom Template display name.
   *
   * @var string
   */
  public $name;
  /**
   * GTM Custom Template's API relative path.
   *
   * @var string
   */
  public $path;
  /**
   * Auto generated link to the tag manager UI
   *
   * @var string
   */
  public $tagManagerUrl;
  /**
   * The custom template in text format.
   *
   * @var string
   */
  public $templateData;
  /**
   * The Custom Template ID uniquely identifies the GTM custom template.
   *
   * @var string
   */
  public $templateId;
  /**
   * GTM Workspace ID.
   *
   * @var string
   */
  public $workspaceId;

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
   * GTM Container ID.
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
   * The fingerprint of the GTM Custom Template as computed at storage time.
   * This value is recomputed whenever the template is modified.
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
   * A reference to the Community Template Gallery entry.
   *
   * @param GalleryReference $galleryReference
   */
  public function setGalleryReference(GalleryReference $galleryReference)
  {
    $this->galleryReference = $galleryReference;
  }
  /**
   * @return GalleryReference
   */
  public function getGalleryReference()
  {
    return $this->galleryReference;
  }
  /**
   * Custom Template display name.
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
   * GTM Custom Template's API relative path.
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
   * The custom template in text format.
   *
   * @param string $templateData
   */
  public function setTemplateData($templateData)
  {
    $this->templateData = $templateData;
  }
  /**
   * @return string
   */
  public function getTemplateData()
  {
    return $this->templateData;
  }
  /**
   * The Custom Template ID uniquely identifies the GTM custom template.
   *
   * @param string $templateId
   */
  public function setTemplateId($templateId)
  {
    $this->templateId = $templateId;
  }
  /**
   * @return string
   */
  public function getTemplateId()
  {
    return $this->templateId;
  }
  /**
   * GTM Workspace ID.
   *
   * @param string $workspaceId
   */
  public function setWorkspaceId($workspaceId)
  {
    $this->workspaceId = $workspaceId;
  }
  /**
   * @return string
   */
  public function getWorkspaceId()
  {
    return $this->workspaceId;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CustomTemplate::class, 'Google_Service_TagManager_CustomTemplate');
