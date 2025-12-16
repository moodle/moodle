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

namespace Google\Service\Walletobjects;

class Uri extends \Google\Model
{
  /**
   * The URI's title appearing in the app as text. Recommended maximum is 20
   * characters to ensure full string is displayed on smaller screens. Note that
   * in some contexts this text is not used, such as when `description` is part
   * of an image.
   *
   * @var string
   */
  public $description;
  /**
   * The ID associated with a uri. This field is here to enable ease of
   * management of uris.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies what kind of resource this is. Value: the fixed string
   * `"walletobjects#uri"`.
   *
   * @deprecated
   * @var string
   */
  public $kind;
  protected $localizedDescriptionType = LocalizedString::class;
  protected $localizedDescriptionDataType = '';
  /**
   * The location of a web page, image, or other resource. URIs in the
   * `LinksModuleData` module can have different prefixes indicating the type of
   * URI (a link to a web page, a link to a map, a telephone number, or an email
   * address). URIs must have a scheme.
   *
   * @var string
   */
  public $uri;

  /**
   * The URI's title appearing in the app as text. Recommended maximum is 20
   * characters to ensure full string is displayed on smaller screens. Note that
   * in some contexts this text is not used, such as when `description` is part
   * of an image.
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
   * The ID associated with a uri. This field is here to enable ease of
   * management of uris.
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
   * `"walletobjects#uri"`.
   *
   * @deprecated
   * @param string $kind
   */
  public function setKind($kind)
  {
    $this->kind = $kind;
  }
  /**
   * @deprecated
   * @return string
   */
  public function getKind()
  {
    return $this->kind;
  }
  /**
   * Translated strings for the description. Recommended maximum is 20
   * characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedDescription
   */
  public function setLocalizedDescription(LocalizedString $localizedDescription)
  {
    $this->localizedDescription = $localizedDescription;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedDescription()
  {
    return $this->localizedDescription;
  }
  /**
   * The location of a web page, image, or other resource. URIs in the
   * `LinksModuleData` module can have different prefixes indicating the type of
   * URI (a link to a web page, a link to a map, a telephone number, or an email
   * address). URIs must have a scheme.
   *
   * @param string $uri
   */
  public function setUri($uri)
  {
    $this->uri = $uri;
  }
  /**
   * @return string
   */
  public function getUri()
  {
    return $this->uri;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Uri::class, 'Google_Service_Walletobjects_Uri');
