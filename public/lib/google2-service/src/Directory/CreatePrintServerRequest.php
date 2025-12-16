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

class CreatePrintServerRequest extends \Google\Model
{
  /**
   * Required. The [unique ID](https://developers.google.com/workspace/admin/dir
   * ectory/reference/rest/v1/customers) of the customer's Google Workspace
   * account. Format: `customers/{id}`
   *
   * @var string
   */
  public $parent;
  protected $printServerType = PrintServer::class;
  protected $printServerDataType = '';

  /**
   * Required. The [unique ID](https://developers.google.com/workspace/admin/dir
   * ectory/reference/rest/v1/customers) of the customer's Google Workspace
   * account. Format: `customers/{id}`
   *
   * @param string $parent
   */
  public function setParent($parent)
  {
    $this->parent = $parent;
  }
  /**
   * @return string
   */
  public function getParent()
  {
    return $this->parent;
  }
  /**
   * Required. A print server to create. If you want to place the print server
   * under a specific organizational unit (OU), then populate the `org_unit_id`.
   * Otherwise the print server is created under the root OU. The `org_unit_id`
   * can be retrieved using the [Directory API](https://developers.google.com/wo
   * rkspace/admin/directory/v1/guides/manage-org-units).
   *
   * @param PrintServer $printServer
   */
  public function setPrintServer(PrintServer $printServer)
  {
    $this->printServer = $printServer;
  }
  /**
   * @return PrintServer
   */
  public function getPrintServer()
  {
    return $this->printServer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreatePrintServerRequest::class, 'Google_Service_Directory_CreatePrintServerRequest');
