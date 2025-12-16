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

class ImClient extends \Google\Model
{
  /**
   * Output only. The protocol of the IM client formatted in the viewer's
   * account locale or the `Accept-Language` HTTP header locale.
   *
   * @var string
   */
  public $formattedProtocol;
  /**
   * Output only. The type of the IM client translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @var string
   */
  public $formattedType;
  protected $metadataType = FieldMetadata::class;
  protected $metadataDataType = '';
  /**
   * The protocol of the IM client. The protocol can be custom or one of these
   * predefined values: * `aim` * `msn` * `yahoo` * `skype` * `qq` *
   * `googleTalk` * `icq` * `jabber` * `netMeeting`
   *
   * @var string
   */
  public $protocol;
  /**
   * The type of the IM client. The type can be custom or one of these
   * predefined values: * `home` * `work` * `other`
   *
   * @var string
   */
  public $type;
  /**
   * The user name used in the IM client.
   *
   * @var string
   */
  public $username;

  /**
   * Output only. The protocol of the IM client formatted in the viewer's
   * account locale or the `Accept-Language` HTTP header locale.
   *
   * @param string $formattedProtocol
   */
  public function setFormattedProtocol($formattedProtocol)
  {
    $this->formattedProtocol = $formattedProtocol;
  }
  /**
   * @return string
   */
  public function getFormattedProtocol()
  {
    return $this->formattedProtocol;
  }
  /**
   * Output only. The type of the IM client translated and formatted in the
   * viewer's account locale or the `Accept-Language` HTTP header locale.
   *
   * @param string $formattedType
   */
  public function setFormattedType($formattedType)
  {
    $this->formattedType = $formattedType;
  }
  /**
   * @return string
   */
  public function getFormattedType()
  {
    return $this->formattedType;
  }
  /**
   * Metadata about the IM client.
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
   * The protocol of the IM client. The protocol can be custom or one of these
   * predefined values: * `aim` * `msn` * `yahoo` * `skype` * `qq` *
   * `googleTalk` * `icq` * `jabber` * `netMeeting`
   *
   * @param string $protocol
   */
  public function setProtocol($protocol)
  {
    $this->protocol = $protocol;
  }
  /**
   * @return string
   */
  public function getProtocol()
  {
    return $this->protocol;
  }
  /**
   * The type of the IM client. The type can be custom or one of these
   * predefined values: * `home` * `work` * `other`
   *
   * @param string $type
   */
  public function setType($type)
  {
    $this->type = $type;
  }
  /**
   * @return string
   */
  public function getType()
  {
    return $this->type;
  }
  /**
   * The user name used in the IM client.
   *
   * @param string $username
   */
  public function setUsername($username)
  {
    $this->username = $username;
  }
  /**
   * @return string
   */
  public function getUsername()
  {
    return $this->username;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ImClient::class, 'Google_Service_PeopleService_ImClient');
