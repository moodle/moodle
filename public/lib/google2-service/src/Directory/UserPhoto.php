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

namespace Google\Service\Directory;

class UserPhoto extends \Google\Model
{
  /**
   * ETag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * Height of the photo in pixels.
   *
   * @var int
   */
  public $height;
  /**
   * The ID the API uses to uniquely identify the user.
   *
   * @var string
   */
  public $id;
  /**
   * The type of the API resource. For Photo resources, this is
   * `admin#directory#user#photo`.
   *
   * @var string
   */
  public $kind;
  /**
   * The MIME type of the photo. Allowed values are `JPEG`, `PNG`, `GIF`, `BMP`,
   * `TIFF`, and web-safe base64 encoding.
   *
   * @var string
   */
  public $mimeType;
  /**
   * The user photo's upload data in [web-safe
   * Base64](https://en.wikipedia.org/wiki/Base64#URL_applications) format in
   * bytes. This means: * The slash (/) character is replaced with the
   * underscore (_) character. * The plus sign (+) character is replaced with
   * the hyphen (-) character. * The equals sign (=) character is replaced with
   * the asterisk (*). * For padding, the period (.) character is used instead
   * of the RFC-4648 baseURL definition which uses the equals sign (=) for
   * padding. This is done to simplify URL-parsing. * Whatever the size of the
   * photo being uploaded, the API downsizes it to 96x96 pixels.
   *
   * @var string
   */
  public $photoData;
  /**
   * The user's primary email address.
   *
   * @var string
   */
  public $primaryEmail;
  /**
   * Width of the photo in pixels.
   *
   * @var int
   */
  public $width;

  /**
   * ETag of the resource.
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
   * Height of the photo in pixels.
   *
   * @param int $height
   */
  public function setHeight($height)
  {
    $this->height = $height;
  }
  /**
   * @return int
   */
  public function getHeight()
  {
    return $this->height;
  }
  /**
   * The ID the API uses to uniquely identify the user.
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
   * The type of the API resource. For Photo resources, this is
   * `admin#directory#user#photo`.
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
   * The MIME type of the photo. Allowed values are `JPEG`, `PNG`, `GIF`, `BMP`,
   * `TIFF`, and web-safe base64 encoding.
   *
   * @param string $mimeType
   */
  public function setMimeType($mimeType)
  {
    $this->mimeType = $mimeType;
  }
  /**
   * @return string
   */
  public function getMimeType()
  {
    return $this->mimeType;
  }
  /**
   * The user photo's upload data in [web-safe
   * Base64](https://en.wikipedia.org/wiki/Base64#URL_applications) format in
   * bytes. This means: * The slash (/) character is replaced with the
   * underscore (_) character. * The plus sign (+) character is replaced with
   * the hyphen (-) character. * The equals sign (=) character is replaced with
   * the asterisk (*). * For padding, the period (.) character is used instead
   * of the RFC-4648 baseURL definition which uses the equals sign (=) for
   * padding. This is done to simplify URL-parsing. * Whatever the size of the
   * photo being uploaded, the API downsizes it to 96x96 pixels.
   *
   * @param string $photoData
   */
  public function setPhotoData($photoData)
  {
    $this->photoData = $photoData;
  }
  /**
   * @return string
   */
  public function getPhotoData()
  {
    return $this->photoData;
  }
  /**
   * The user's primary email address.
   *
   * @param string $primaryEmail
   */
  public function setPrimaryEmail($primaryEmail)
  {
    $this->primaryEmail = $primaryEmail;
  }
  /**
   * @return string
   */
  public function getPrimaryEmail()
  {
    return $this->primaryEmail;
  }
  /**
   * Width of the photo in pixels.
   *
   * @param int $width
   */
  public function setWidth($width)
  {
    $this->width = $width;
  }
  /**
   * @return int
   */
  public function getWidth()
  {
    return $this->width;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(UserPhoto::class, 'Google_Service_Directory_UserPhoto');
