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

class Printer extends \Google\Collection
{
  protected $collection_key = 'auxiliaryMessages';
  protected $auxiliaryMessagesType = AuxiliaryMessage::class;
  protected $auxiliaryMessagesDataType = 'array';
  /**
   * Output only. Time when printer was created.
   *
   * @var string
   */
  public $createTime;
  /**
   * Editable. Description of printer.
   *
   * @var string
   */
  public $description;
  /**
   * Editable. Name of printer.
   *
   * @var string
   */
  public $displayName;
  /**
   * Id of the printer. (During printer creation leave empty)
   *
   * @var string
   */
  public $id;
  /**
   * Editable. Make and model of printer. e.g. Lexmark MS610de Value must be in
   * format as seen in ListPrinterModels response.
   *
   * @var string
   */
  public $makeAndModel;
  /**
   * Identifier. The resource name of the Printer object, in the format
   * customers/{customer-id}/printers/{printer-id} (During printer creation
   * leave empty)
   *
   * @var string
   */
  public $name;
  /**
   * Organization Unit that owns this printer (Only can be set during Printer
   * creation)
   *
   * @var string
   */
  public $orgUnitId;
  /**
   * Editable. Printer URI.
   *
   * @var string
   */
  public $uri;
  /**
   * Editable. flag to use driverless configuration or not. If it's set to be
   * true, make_and_model can be ignored
   *
   * @var bool
   */
  public $useDriverlessConfig;

  /**
   * Output only. Auxiliary messages about issues with the printer configuration
   * if any.
   *
   * @param AuxiliaryMessage[] $auxiliaryMessages
   */
  public function setAuxiliaryMessages($auxiliaryMessages)
  {
    $this->auxiliaryMessages = $auxiliaryMessages;
  }
  /**
   * @return AuxiliaryMessage[]
   */
  public function getAuxiliaryMessages()
  {
    return $this->auxiliaryMessages;
  }
  /**
   * Output only. Time when printer was created.
   *
   * @param string $createTime
   */
  public function setCreateTime($createTime)
  {
    $this->createTime = $createTime;
  }
  /**
   * @return string
   */
  public function getCreateTime()
  {
    return $this->createTime;
  }
  /**
   * Editable. Description of printer.
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
   * Editable. Name of printer.
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
   * Id of the printer. (During printer creation leave empty)
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
   * Editable. Make and model of printer. e.g. Lexmark MS610de Value must be in
   * format as seen in ListPrinterModels response.
   *
   * @param string $makeAndModel
   */
  public function setMakeAndModel($makeAndModel)
  {
    $this->makeAndModel = $makeAndModel;
  }
  /**
   * @return string
   */
  public function getMakeAndModel()
  {
    return $this->makeAndModel;
  }
  /**
   * Identifier. The resource name of the Printer object, in the format
   * customers/{customer-id}/printers/{printer-id} (During printer creation
   * leave empty)
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
   * Organization Unit that owns this printer (Only can be set during Printer
   * creation)
   *
   * @param string $orgUnitId
   */
  public function setOrgUnitId($orgUnitId)
  {
    $this->orgUnitId = $orgUnitId;
  }
  /**
   * @return string
   */
  public function getOrgUnitId()
  {
    return $this->orgUnitId;
  }
  /**
   * Editable. Printer URI.
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
  /**
   * Editable. flag to use driverless configuration or not. If it's set to be
   * true, make_and_model can be ignored
   *
   * @param bool $useDriverlessConfig
   */
  public function setUseDriverlessConfig($useDriverlessConfig)
  {
    $this->useDriverlessConfig = $useDriverlessConfig;
  }
  /**
   * @return bool
   */
  public function getUseDriverlessConfig()
  {
    return $this->useDriverlessConfig;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(Printer::class, 'Google_Service_Directory_Printer');
