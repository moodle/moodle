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

namespace Google\Service\DataCatalog;

class GoogleCloudDatacatalogV1EntryOverview extends \Google\Model
{
  /**
   * Entry overview with support for rich text. The overview must only contain
   * Unicode characters, and should be formatted using HTML. The maximum length
   * is 10 MiB as this value holds HTML descriptions including encoded images.
   * The maximum length of the text without images is 100 KiB.
   *
   * @var string
   */
  public $overview;

  /**
   * Entry overview with support for rich text. The overview must only contain
   * Unicode characters, and should be formatted using HTML. The maximum length
   * is 10 MiB as this value holds HTML descriptions including encoded images.
   * The maximum length of the text without images is 100 KiB.
   *
   * @param string $overview
   */
  public function setOverview($overview)
  {
    $this->overview = $overview;
  }
  /**
   * @return string
   */
  public function getOverview()
  {
    return $this->overview;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDatacatalogV1EntryOverview::class, 'Google_Service_DataCatalog_GoogleCloudDatacatalogV1EntryOverview');
