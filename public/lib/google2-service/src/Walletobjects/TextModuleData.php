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

class TextModuleData extends \Google\Model
{
  /**
   * The body of the Text Module, which is defined as an uninterrupted string.
   * Recommended maximum length is 500 characters to ensure full string is
   * displayed on smaller screens.
   *
   * @var string
   */
  public $body;
  /**
   * The header of the Text Module. Recommended maximum length is 35 characters
   * to ensure full string is displayed on smaller screens.
   *
   * @var string
   */
  public $header;
  /**
   * The ID associated with a text module. This field is here to enable ease of
   * management of text modules and referencing them in template overrides. The
   * ID should only include alphanumeric characters, '_', or '-'. It can not
   * include dots, as dots are used to separate fields within
   * FieldReference.fieldPaths in template overrides.
   *
   * @var string
   */
  public $id;
  protected $localizedBodyType = LocalizedString::class;
  protected $localizedBodyDataType = '';
  protected $localizedHeaderType = LocalizedString::class;
  protected $localizedHeaderDataType = '';

  /**
   * The body of the Text Module, which is defined as an uninterrupted string.
   * Recommended maximum length is 500 characters to ensure full string is
   * displayed on smaller screens.
   *
   * @param string $body
   */
  public function setBody($body)
  {
    $this->body = $body;
  }
  /**
   * @return string
   */
  public function getBody()
  {
    return $this->body;
  }
  /**
   * The header of the Text Module. Recommended maximum length is 35 characters
   * to ensure full string is displayed on smaller screens.
   *
   * @param string $header
   */
  public function setHeader($header)
  {
    $this->header = $header;
  }
  /**
   * @return string
   */
  public function getHeader()
  {
    return $this->header;
  }
  /**
   * The ID associated with a text module. This field is here to enable ease of
   * management of text modules and referencing them in template overrides. The
   * ID should only include alphanumeric characters, '_', or '-'. It can not
   * include dots, as dots are used to separate fields within
   * FieldReference.fieldPaths in template overrides.
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
   * Translated strings for the body. Recommended maximum length is 500
   * characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedBody
   */
  public function setLocalizedBody(LocalizedString $localizedBody)
  {
    $this->localizedBody = $localizedBody;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedBody()
  {
    return $this->localizedBody;
  }
  /**
   * Translated strings for the header. Recommended maximum length is 35
   * characters to ensure full string is displayed on smaller screens.
   *
   * @param LocalizedString $localizedHeader
   */
  public function setLocalizedHeader(LocalizedString $localizedHeader)
  {
    $this->localizedHeader = $localizedHeader;
  }
  /**
   * @return LocalizedString
   */
  public function getLocalizedHeader()
  {
    return $this->localizedHeader;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(TextModuleData::class, 'Google_Service_Walletobjects_TextModuleData');
