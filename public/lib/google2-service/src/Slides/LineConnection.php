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

namespace Google\Service\Slides;

class LineConnection extends \Google\Model
{
  /**
   * The object ID of the connected page element. Some page elements, such as
   * groups, tables, and lines do not have connection sites and therefore cannot
   * be connected to a connector line.
   *
   * @var string
   */
  public $connectedObjectId;
  /**
   * The index of the connection site on the connected page element. In most
   * cases, it corresponds to the predefined connection site index from the
   * ECMA-376 standard. More information on those connection sites can be found
   * in both the description of the "cxn" attribute in section 20.1.9.9 and
   * "Annex H. Example Predefined DrawingML Shape and Text Geometries" of
   * "Office Open XML File Formats - Fundamentals and Markup Language
   * Reference", part 1 of [ECMA-376 5th edition](https://ecma-
   * international.org/publications-and-standards/standards/ecma-376/). The
   * position of each connection site can also be viewed from Slides editor.
   *
   * @var int
   */
  public $connectionSiteIndex;

  /**
   * The object ID of the connected page element. Some page elements, such as
   * groups, tables, and lines do not have connection sites and therefore cannot
   * be connected to a connector line.
   *
   * @param string $connectedObjectId
   */
  public function setConnectedObjectId($connectedObjectId)
  {
    $this->connectedObjectId = $connectedObjectId;
  }
  /**
   * @return string
   */
  public function getConnectedObjectId()
  {
    return $this->connectedObjectId;
  }
  /**
   * The index of the connection site on the connected page element. In most
   * cases, it corresponds to the predefined connection site index from the
   * ECMA-376 standard. More information on those connection sites can be found
   * in both the description of the "cxn" attribute in section 20.1.9.9 and
   * "Annex H. Example Predefined DrawingML Shape and Text Geometries" of
   * "Office Open XML File Formats - Fundamentals and Markup Language
   * Reference", part 1 of [ECMA-376 5th edition](https://ecma-
   * international.org/publications-and-standards/standards/ecma-376/). The
   * position of each connection site can also be viewed from Slides editor.
   *
   * @param int $connectionSiteIndex
   */
  public function setConnectionSiteIndex($connectionSiteIndex)
  {
    $this->connectionSiteIndex = $connectionSiteIndex;
  }
  /**
   * @return int
   */
  public function getConnectionSiteIndex()
  {
    return $this->connectionSiteIndex;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(LineConnection::class, 'Google_Service_Slides_LineConnection');
