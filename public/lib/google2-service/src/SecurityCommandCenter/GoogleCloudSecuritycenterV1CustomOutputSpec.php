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

namespace Google\Service\SecurityCommandCenter;

class GoogleCloudSecuritycenterV1CustomOutputSpec extends \Google\Collection
{
  protected $collection_key = 'properties';
  protected $propertiesType = GoogleCloudSecuritycenterV1Property::class;
  protected $propertiesDataType = 'array';

  /**
   * A list of custom output properties to add to the finding.
   *
   * @param GoogleCloudSecuritycenterV1Property[] $properties
   */
  public function setProperties($properties)
  {
    $this->properties = $properties;
  }
  /**
   * @return GoogleCloudSecuritycenterV1Property[]
   */
  public function getProperties()
  {
    return $this->properties;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudSecuritycenterV1CustomOutputSpec::class, 'Google_Service_SecurityCommandCenter_GoogleCloudSecuritycenterV1CustomOutputSpec');
