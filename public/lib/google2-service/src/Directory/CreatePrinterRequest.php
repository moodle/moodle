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

class CreatePrinterRequest extends \Google\Model
{
  /**
   * Required. The name of the customer. Format: customers/{customer_id}
   *
   * @var string
   */
  public $parent;
  protected $printerType = Printer::class;
  protected $printerDataType = '';

  /**
   * Required. The name of the customer. Format: customers/{customer_id}
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
   * Required. A printer to create. If you want to place the printer under
   * particular OU then populate printer.org_unit_id filed. Otherwise the
   * printer will be placed under root OU.
   *
   * @param Printer $printer
   */
  public function setPrinter(Printer $printer)
  {
    $this->printer = $printer;
  }
  /**
   * @return Printer
   */
  public function getPrinter()
  {
    return $this->printer;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(CreatePrinterRequest::class, 'Google_Service_Directory_CreatePrinterRequest');
