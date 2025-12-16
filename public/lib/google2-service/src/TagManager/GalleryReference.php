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

class GalleryReference extends \Google\Model
{
  /**
   * ID for the gallery template that is generated once during first sync and
   * travels with the template redirects.
   *
   * @var string
   */
  public $galleryTemplateId;
  /**
   * The name of the host for the community gallery template.
   *
   * @var string
   */
  public $host;
  /**
   * If a user has manually edited the community gallery template.
   *
   * @var bool
   */
  public $isModified;
  /**
   * The name of the owner for the community gallery template.
   *
   * @var string
   */
  public $owner;
  /**
   * The name of the repository for the community gallery template.
   *
   * @var string
   */
  public $repository;
  /**
   * The signature of the community gallery template as computed at import time.
   * This value is recomputed whenever the template is updated from the gallery.
   *
   * @var string
   */
  public $signature;
  /**
   * The developer id of the community gallery template. This value is set
   * whenever the template is created from the gallery.
   *
   * @var string
   */
  public $templateDeveloperId;
  /**
   * The version of the community gallery template.
   *
   * @var string
   */
  public $version;

  /**
   * ID for the gallery template that is generated once during first sync and
   * travels with the template redirects.
   *
   * @param string $galleryTemplateId
   */
  public function setGalleryTemplateId($galleryTemplateId)
  {
    $this->galleryTemplateId = $galleryTemplateId;
  }
  /**
   * @return string
   */
  public function getGalleryTemplateId()
  {
    return $this->galleryTemplateId;
  }
  /**
   * The name of the host for the community gallery template.
   *
   * @param string $host
   */
  public function setHost($host)
  {
    $this->host = $host;
  }
  /**
   * @return string
   */
  public function getHost()
  {
    return $this->host;
  }
  /**
   * If a user has manually edited the community gallery template.
   *
   * @param bool $isModified
   */
  public function setIsModified($isModified)
  {
    $this->isModified = $isModified;
  }
  /**
   * @return bool
   */
  public function getIsModified()
  {
    return $this->isModified;
  }
  /**
   * The name of the owner for the community gallery template.
   *
   * @param string $owner
   */
  public function setOwner($owner)
  {
    $this->owner = $owner;
  }
  /**
   * @return string
   */
  public function getOwner()
  {
    return $this->owner;
  }
  /**
   * The name of the repository for the community gallery template.
   *
   * @param string $repository
   */
  public function setRepository($repository)
  {
    $this->repository = $repository;
  }
  /**
   * @return string
   */
  public function getRepository()
  {
    return $this->repository;
  }
  /**
   * The signature of the community gallery template as computed at import time.
   * This value is recomputed whenever the template is updated from the gallery.
   *
   * @param string $signature
   */
  public function setSignature($signature)
  {
    $this->signature = $signature;
  }
  /**
   * @return string
   */
  public function getSignature()
  {
    return $this->signature;
  }
  /**
   * The developer id of the community gallery template. This value is set
   * whenever the template is created from the gallery.
   *
   * @param string $templateDeveloperId
   */
  public function setTemplateDeveloperId($templateDeveloperId)
  {
    $this->templateDeveloperId = $templateDeveloperId;
  }
  /**
   * @return string
   */
  public function getTemplateDeveloperId()
  {
    return $this->templateDeveloperId;
  }
  /**
   * The version of the community gallery template.
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
class_alias(GalleryReference::class, 'Google_Service_TagManager_GalleryReference');
