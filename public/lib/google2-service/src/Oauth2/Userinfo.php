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

namespace Google\Service\Oauth2;

class Userinfo extends \Google\Model
{
  protected $internal_gapi_mappings = [
        "familyName" => "family_name",
        "givenName" => "given_name",
        "verifiedEmail" => "verified_email",
  ];
  /**
   * The user's email address.
   *
   * @var string
   */
  public $email;
  /**
   * The user's last name.
   *
   * @var string
   */
  public $familyName;
  /**
   * The user's gender.
   *
   * @var string
   */
  public $gender;
  /**
   * The user's first name.
   *
   * @var string
   */
  public $givenName;
  /**
   * The hosted domain e.g. example.com if the user is Google apps user.
   *
   * @var string
   */
  public $hd;
  /**
   * The obfuscated ID of the user.
   *
   * @var string
   */
  public $id;
  /**
   * URL of the profile page.
   *
   * @var string
   */
  public $link;
  /**
   * The user's preferred locale.
   *
   * @var string
   */
  public $locale;
  /**
   * The user's full name.
   *
   * @var string
   */
  public $name;
  /**
   * URL of the user's picture image.
   *
   * @var string
   */
  public $picture;
  /**
   * Boolean flag which is true if the email address is verified. Always
   * verified because we only return the user's primary email address.
   *
   * @var bool
   */
  public $verifiedEmail;

  /**
   * The user's email address.
   *
   * @param string $email
   */
  public function setEmail($email)
  {
    $this->email = $email;
  }
  /**
   * @return string
   */
  public function getEmail()
  {
    return $this->email;
  }
  /**
   * The user's last name.
   *
   * @param string $familyName
   */
  public function setFamilyName($familyName)
  {
    $this->familyName = $familyName;
  }
  /**
   * @return string
   */
  public function getFamilyName()
  {
    return $this->familyName;
  }
  /**
   * The user's gender.
   *
   * @param string $gender
   */
  public function setGender($gender)
  {
    $this->gender = $gender;
  }
  /**
   * @return string
   */
  public function getGender()
  {
    return $this->gender;
  }
  /**
   * The user's first name.
   *
   * @param string $givenName
   */
  public function setGivenName($givenName)
  {
    $this->givenName = $givenName;
  }
  /**
   * @return string
   */
  public function getGivenName()
  {
    return $this->givenName;
  }
  /**
   * The hosted domain e.g. example.com if the user is Google apps user.
   *
   * @param string $hd
   */
  public function setHd($hd)
  {
    $this->hd = $hd;
  }
  /**
   * @return string
   */
  public function getHd()
  {
    return $this->hd;
  }
  /**
   * The obfuscated ID of the user.
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
   * URL of the profile page.
   *
   * @param string $link
   */
  public function setLink($link)
  {
    $this->link = $link;
  }
  /**
   * @return string
   */
  public function getLink()
  {
    return $this->link;
  }
  /**
   * The user's preferred locale.
   *
   * @param string $locale
   */
  public function setLocale($locale)
  {
    $this->locale = $locale;
  }
  /**
   * @return string
   */
  public function getLocale()
  {
    return $this->locale;
  }
  /**
   * The user's full name.
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
   * URL of the user's picture image.
   *
   * @param string $picture
   */
  public function setPicture($picture)
  {
    $this->picture = $picture;
  }
  /**
   * @return string
   */
  public function getPicture()
  {
    return $this->picture;
  }
  /**
   * Boolean flag which is true if the email address is verified. Always
   * verified because we only return the user's primary email address.
   *
   * @param bool $verifiedEmail
   */
  public function setVerifiedEmail($verifiedEmail)
  {
    $this->verifiedEmail = $verifiedEmail;
  }
  /**
   * @return bool
   */
  public function getVerifiedEmail()
  {
    return $this->verifiedEmail;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Userinfo::class, 'Google_Service_Oauth2_Userinfo');
