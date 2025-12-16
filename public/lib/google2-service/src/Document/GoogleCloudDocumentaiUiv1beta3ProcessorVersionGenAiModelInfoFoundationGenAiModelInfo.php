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

class GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo extends \Google\Model
{
  /**
   * Whether finetuning is allowed for this base processor version.
   *
   * @var bool
   */
  public $finetuningAllowed;
  /**
   * The minimum number of labeled documents in the training dataset required
   * for finetuning.
   *
   * @var int
   */
  public $minTrainLabeledDocuments;

  /**
   * Whether finetuning is allowed for this base processor version.
   *
   * @param bool $finetuningAllowed
   */
  public function setFinetuningAllowed($finetuningAllowed)
  {
    $this->finetuningAllowed = $finetuningAllowed;
  }
  /**
   * @return bool
   */
  public function getFinetuningAllowed()
  {
    return $this->finetuningAllowed;
  }
  /**
   * The minimum number of labeled documents in the training dataset required
   * for finetuning.
   *
   * @param int $minTrainLabeledDocuments
   */
  public function setMinTrainLabeledDocuments($minTrainLabeledDocuments)
  {
    $this->minTrainLabeledDocuments = $minTrainLabeledDocuments;
  }
  /**
   * @return int
   */
  public function getMinTrainLabeledDocuments()
  {
    return $this->minTrainLabeledDocuments;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo::class, 'Google_Service_Document_GoogleCloudDocumentaiUiv1beta3ProcessorVersionGenAiModelInfoFoundationGenAiModelInfo');
