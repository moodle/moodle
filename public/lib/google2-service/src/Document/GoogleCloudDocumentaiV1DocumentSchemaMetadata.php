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

namespace Google\Service\Document;

class GoogleCloudDocumentaiV1DocumentSchemaMetadata extends \Google\Model
{
  /**
   * If true, on a given page, there can be multiple `document` annotations
   * covering it.
   *
   * @var bool
   */
  public $documentAllowMultipleLabels;
  /**
   * If true, a `document` entity type can be applied to subdocument
   * (splitting). Otherwise, it can only be applied to the entire document
   * (classification).
   *
   * @var bool
   */
  public $documentSplitter;
  /**
   * If set, all the nested entities must be prefixed with the parents.
   *
   * @var bool
   */
  public $prefixedNamingOnProperties;
  /**
   * If set, we will skip the naming format validation in the schema. So the
   * string values in `DocumentSchema.EntityType.name` and
   * `DocumentSchema.EntityType.Property.name` will not be checked.
   *
   * @var bool
   */
  public $skipNamingValidation;

  /**
   * If true, on a given page, there can be multiple `document` annotations
   * covering it.
   *
   * @param bool $documentAllowMultipleLabels
   */
  public function setDocumentAllowMultipleLabels($documentAllowMultipleLabels)
  {
    $this->documentAllowMultipleLabels = $documentAllowMultipleLabels;
  }
  /**
   * @return bool
   */
  public function getDocumentAllowMultipleLabels()
  {
    return $this->documentAllowMultipleLabels;
  }
  /**
   * If true, a `document` entity type can be applied to subdocument
   * (splitting). Otherwise, it can only be applied to the entire document
   * (classification).
   *
   * @param bool $documentSplitter
   */
  public function setDocumentSplitter($documentSplitter)
  {
    $this->documentSplitter = $documentSplitter;
  }
  /**
   * @return bool
   */
  public function getDocumentSplitter()
  {
    return $this->documentSplitter;
  }
  /**
   * If set, all the nested entities must be prefixed with the parents.
   *
   * @param bool $prefixedNamingOnProperties
   */
  public function setPrefixedNamingOnProperties($prefixedNamingOnProperties)
  {
    $this->prefixedNamingOnProperties = $prefixedNamingOnProperties;
  }
  /**
   * @return bool
   */
  public function getPrefixedNamingOnProperties()
  {
    return $this->prefixedNamingOnProperties;
  }
  /**
   * If set, we will skip the naming format validation in the schema. So the
   * string values in `DocumentSchema.EntityType.name` and
   * `DocumentSchema.EntityType.Property.name` will not be checked.
   *
   * @param bool $skipNamingValidation
   */
  public function setSkipNamingValidation($skipNamingValidation)
  {
    $this->skipNamingValidation = $skipNamingValidation;
  }
  /**
   * @return bool
   */
  public function getSkipNamingValidation()
  {
    return $this->skipNamingValidation;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiV1DocumentSchemaMetadata::class, 'Google_Service_Document_GoogleCloudDocumentaiV1DocumentSchemaMetadata');
