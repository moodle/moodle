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

namespace Google\Service\DLP;

class GooglePrivacyDlpV2PathElement extends \Google\Model
{
  /**
   * The auto-allocated ID of the entity. Never equal to zero. Values less than
   * zero are discouraged and may not be supported in the future.
   *
   * @var string
   */
  public $id;
  /**
   * The kind of the entity. A kind matching regex `__.*__` is reserved/read-
   * only. A kind must not contain more than 1500 bytes when UTF-8 encoded.
   * Cannot be `""`.
   *
   * @var string
   */
  public $kind;
  /**
   * The name of the entity. A name matching regex `__.*__` is reserved/read-
   * only. A name must not be more than 1500 bytes when UTF-8 encoded. Cannot be
   * `""`.
   *
   * @var string
   */
  public $name;

  /**
   * The auto-allocated ID of the entity. Never equal to zero. Values less than
   * zero are discouraged and may not be supported in the future.
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
   * The kind of the entity. A kind matching regex `__.*__` is reserved/read-
   * only. A kind must not contain more than 1500 bytes when UTF-8 encoded.
   * Cannot be `""`.
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
   * The name of the entity. A name matching regex `__.*__` is reserved/read-
   * only. A name must not be more than 1500 bytes when UTF-8 encoded. Cannot be
   * `""`.
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
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GooglePrivacyDlpV2PathElement::class, 'Google_Service_DLP_GooglePrivacyDlpV2PathElement');
