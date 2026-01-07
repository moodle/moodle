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

namespace Google\Service\SA360;

class GoogleAdsSearchads360V0ErrorsErrorLocation extends \Google\Collection
{
  protected $collection_key = 'fieldPathElements';
  protected $fieldPathElementsType = GoogleAdsSearchads360V0ErrorsErrorLocationFieldPathElement::class;
  protected $fieldPathElementsDataType = 'array';

  /**
   * A field path that indicates which field was invalid in the request.
   *
   * @param GoogleAdsSearchads360V0ErrorsErrorLocationFieldPathElement[] $fieldPathElements
   */
  public function setFieldPathElements($fieldPathElements)
  {
    $this->fieldPathElements = $fieldPathElements;
  }
  /**
   * @return GoogleAdsSearchads360V0ErrorsErrorLocationFieldPathElement[]
   */
  public function getFieldPathElements()
  {
    return $this->fieldPathElements;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleAdsSearchads360V0ErrorsErrorLocation::class, 'Google_Service_SA360_GoogleAdsSearchads360V0ErrorsErrorLocation');
