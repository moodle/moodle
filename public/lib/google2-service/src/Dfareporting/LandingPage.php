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

namespace Google\Service\Dfareporting;

class LandingPage extends \Google\Collection
{
  protected $collection_key = 'deepLinks';
  /**
   * Advertiser ID of this landing page. This is a required field.
   *
   * @var string
   */
  public $advertiserId;
  /**
   * Whether this landing page has been archived.
   *
   * @var bool
   */
  public $archived;
  protected $deepLinksType = DeepLink::class;
  protected $deepLinksDataType = 'array';
  /**
   * ID of this landing page. This is a read-only, auto-generated field.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#landingPage".
   *
   * @var string
   */
  public $kind;
  /**
   * Name of this landing page. This is a required field. It must be less than
   * 256 characters long.
   *
   * @var string
   */
  public $name;
  /**
   * URL of this landing page. This is a required field.
   *
   * @var string
   */
  public $url;

  /**
   * Advertiser ID of this landing page. This is a required field.
   *
   * @param string $advertiserId
   */
  public function setAdvertiserId($advertiserId)
  {
    $this->advertiserId = $advertiserId;
  }
  /**
   * @return string
   */
  public function getAdvertiserId()
  {
    return $this->advertiserId;
  }
  /**
   * Whether this landing page has been archived.
   *
   * @param bool $archived
   */
  public function setArchived($archived)
  {
    $this->archived = $archived;
  }
  /**
   * @return bool
   */
  public function getArchived()
  {
    return $this->archived;
  }
  /**
   * Links that will direct the user to a mobile app, if installed.
   *
   * @param DeepLink[] $deepLinks
   */
  public function setDeepLinks($deepLinks)
  {
    $this->deepLinks = $deepLinks;
  }
  /**
   * @return DeepLink[]
   */
  public function getDeepLinks()
  {
    return $this->deepLinks;
  }
  /**
   * ID of this landing page. This is a read-only, auto-generated field.
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
   * Identifies what kind of resource this is. Value: the fixed string
   * "dfareporting#landingPage".
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
   * Name of this landing page. This is a required field. It must be less than
   * 256 characters long.
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
   * URL of this landing page. This is a required field.
   *
   * @param string $url
   */
  public function setUrl($url)
  {
    $this->url = $url;
  }
  /**
   * @return string
   */
  public function getUrl()
  {
    return $this->url;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LandingPage::class, 'Google_Service_Dfareporting_LandingPage');
