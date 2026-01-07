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

namespace Google\Service\DataTransfer;

class Application extends \Google\Collection
{
  protected $collection_key = 'transferParams';
  /**
   * Etag of the resource.
   *
   * @var string
   */
  public $etag;
  /**
   * The application's ID. Retrievable by using the
   * [`applications.list()`](https://developers.google.com/workspace/admin/data-
   * transfer/reference/rest/v1/applications/list) method.
   *
   * @var string
   */
  public $id;
  /**
   * Identifies the resource as a DataTransfer Application Resource.
   *
   * @var string
   */
  public $kind;
  /**
   * The application's name.
   *
   * @var string
   */
  public $name;
  protected $transferParamsType = ApplicationTransferParam::class;
  protected $transferParamsDataType = 'array';

  /**
   * Etag of the resource.
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
   * The application's ID. Retrievable by using the
   * [`applications.list()`](https://developers.google.com/workspace/admin/data-
   * transfer/reference/rest/v1/applications/list) method.
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
   * Identifies the resource as a DataTransfer Application Resource.
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
   * The application's name.
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
   * The list of all possible transfer parameters for this application. These
   * parameters select which categories of the user's data to transfer.
   *
   * @param ApplicationTransferParam[] $transferParams
   */
  public function setTransferParams($transferParams)
  {
    $this->transferParams = $transferParams;
  }
  /**
   * @return ApplicationTransferParam[]
   */
  public function getTransferParams()
  {
    return $this->transferParams;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Application::class, 'Google_Service_DataTransfer_Application');
