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

namespace Google\Service\PeopleService;

class Name extends \Google\Model
{
  /**
   * Output only. The display name formatted according to the locale specified
   * by the viewer's account or the `Accept-Language` HTTP header.
   *
   * @var string
   */
  public $displayName;
  /**
   * Output only. The display name with the last name first formatted according
   * to the locale specified by the viewer's account or the `Accept-Language`
   * HTTP header.
   *
   * @var string
   */
  public $displayNameLastFirst;
  /**
   * The family name.
   *
   * @var string
   */
  public $familyName;
  /**
   * The given name.
   *
   * @var string
   */
  public $givenName;
  /**
   * The honorific prefixes, such as `Mrs.` or `Dr.`
   *
   * @var string
   */
  public $honorificPrefix;
  /**
   * The honorific suffixes, such as `Jr.`
   *
   * @var string
   */
  public $honorificSuffix;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The middle name(s).
   *
   * @var string
   */
  public $middleName;
  /**
   * The family name spelled as it sounds.
   *
   * @var string
   */
  public $phoneticFamilyName;
  /**
   * The full name spelled as it sounds.
   *
   * @var string
   */
  public $phoneticFullName;
  /**
   * The given name spelled as it sounds.
   *
   * @var string
   */
  public $phoneticGivenName;
  /**
   * The honorific prefixes spelled as they sound.
   *
   * @var string
   */
  public $phoneticHonorificPrefix;
  /**
   * The honorific suffixes spelled as they sound.
   *
   * @var string
   */
  public $phoneticHonorificSuffix;
  /**
   * The middle name(s) spelled as they sound.
   *
   * @var string
   */
  public $phoneticMiddleName;
  /**
   * The free form name value.
   *
   * @var string
   */
  public $unstructuredName;

  /**
   * Output only. The display name formatted according to the locale specified
   * by the viewer's account or the `Accept-Language` HTTP header.
   *
   * @param string $displayName
   */
  public function setDisplayName($displayName)
  {
    $this->displayName = $displayName;
  }
  /**
   * @return string
   */
  public function getDisplayName()
  {
    return $this->displayName;
  }
  /**
   * Output only. The display name with the last name first formatted according
   * to the locale specified by the viewer's account or the `Accept-Language`
   * HTTP header.
   *
   * @param string $displayNameLastFirst
   */
  public function setDisplayNameLastFirst($displayNameLastFirst)
  {
    $this->displayNameLastFirst = $displayNameLastFirst;
  }
  /**
   * @return string
   */
  public function getDisplayNameLastFirst()
  {
    return $this->displayNameLastFirst;
  }
  /**
   * The family name.
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
   * The given name.
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
   * The honorific prefixes, such as `Mrs.` or `Dr.`
   *
   * @param string $honorificPrefix
   */
  public function setHonorificPrefix($honorificPrefix)
  {
    $this->honorificPrefix = $honorificPrefix;
  }
  /**
   * @return string
   */
  public function getHonorificPrefix()
  {
    return $this->honorificPrefix;
  }
  /**
   * The honorific suffixes, such as `Jr.`
   *
   * @param string $honorificSuffix
   */
  public function setHonorificSuffix($honorificSuffix)
  {
    $this->honorificSuffix = $honorificSuffix;
  }
  /**
   * @return string
   */
  public function getHonorificSuffix()
  {
    return $this->honorificSuffix;
  }
  /**
   * Metadata about the name.
   *
   * @param FieldMetadata $metadata
   */
  public function setMetadata(FieldMetadata $metadata)
  {
    $this->metadata = $metadata;
  }
  /**
   * @return FieldMetadata
   */
  public function getMetadata()
  {
    return $this->metadata;
  }
  /**
   * The middle name(s).
   *
   * @param string $middleName
   */
  public function setMiddleName($middleName)
  {
    $this->middleName = $middleName;
  }
  /**
   * @return string
   */
  public function getMiddleName()
  {
    return $this->middleName;
  }
  /**
   * The family name spelled as it sounds.
   *
   * @param string $phoneticFamilyName
   */
  public function setPhoneticFamilyName($phoneticFamilyName)
  {
    $this->phoneticFamilyName = $phoneticFamilyName;
  }
  /**
   * @return string
   */
  public function getPhoneticFamilyName()
  {
    return $this->phoneticFamilyName;
  }
  /**
   * The full name spelled as it sounds.
   *
   * @param string $phoneticFullName
   */
  public function setPhoneticFullName($phoneticFullName)
  {
    $this->phoneticFullName = $phoneticFullName;
  }
  /**
   * @return string
   */
  public function getPhoneticFullName()
  {
    return $this->phoneticFullName;
  }
  /**
   * The given name spelled as it sounds.
   *
   * @param string $phoneticGivenName
   */
  public function setPhoneticGivenName($phoneticGivenName)
  {
    $this->phoneticGivenName = $phoneticGivenName;
  }
  /**
   * @return string
   */
  public function getPhoneticGivenName()
  {
    return $this->phoneticGivenName;
  }
  /**
   * The honorific prefixes spelled as they sound.
   *
   * @param string $phoneticHonorificPrefix
   */
  public function setPhoneticHonorificPrefix($phoneticHonorificPrefix)
  {
    $this->phoneticHonorificPrefix = $phoneticHonorificPrefix;
  }
  /**
   * @return string
   */
  public function getPhoneticHonorificPrefix()
  {
    return $this->phoneticHonorificPrefix;
  }
  /**
   * The honorific suffixes spelled as they sound.
   *
   * @param string $phoneticHonorificSuffix
   */
  public function setPhoneticHonorificSuffix($phoneticHonorificSuffix)
  {
    $this->phoneticHonorificSuffix = $phoneticHonorificSuffix;
  }
  /**
   * @return string
   */
  public function getPhoneticHonorificSuffix()
  {
    return $this->phoneticHonorificSuffix;
  }
  /**
   * The middle name(s) spelled as they sound.
   *
   * @param string $phoneticMiddleName
   */
  public function setPhoneticMiddleName($phoneticMiddleName)
  {
    $this->phoneticMiddleName = $phoneticMiddleName;
  }
  /**
   * @return string
   */
  public function getPhoneticMiddleName()
  {
    return $this->phoneticMiddleName;
  }
  /**
   * The free form name value.
   *
   * @param string $unstructuredName
   */
  public function setUnstructuredName($unstructuredName)
  {
    $this->unstructuredName = $unstructuredName;
  }
  /**
   * @return string
   */
  public function getUnstructuredName()
  {
    return $this->unstructuredName;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Name::class, 'Google_Service_PeopleService_Name');
