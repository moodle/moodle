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

namespace Google\Service\CloudTalentSolution;

class ApplicationInfo extends \Google\Collection
{
  protected $collection_key = 'uris';
  /**
   * Use this field to specify email address(es) to which resumes or
   * applications can be sent. The maximum number of allowed characters for each
   * entry is 255.
   *
   * @var string[]
   */
  public $emails;
  /**
   * Use this field to provide instructions, such as "Mail your application to
   * ...", that a candidate can follow to apply for the job. This field accepts
   * and sanitizes HTML input, and also accepts bold, italic, ordered list, and
   * unordered list markup tags. The maximum number of allowed characters is
   * 3,000.
   *
   * @var string
   */
  public $instruction;
  /**
   * Use this URI field to direct an applicant to a website, for example to link
   * to an online application form. The maximum number of allowed characters for
   * each entry is 2,000.
   *
   * @var string[]
   */
  public $uris;

  /**
   * Use this field to specify email address(es) to which resumes or
   * applications can be sent. The maximum number of allowed characters for each
   * entry is 255.
   *
   * @param string[] $emails
   */
  public function setEmails($emails)
  {
    $this->emails = $emails;
  }
  /**
   * @return string[]
   */
  public function getEmails()
  {
    return $this->emails;
  }
  /**
   * Use this field to provide instructions, such as "Mail your application to
   * ...", that a candidate can follow to apply for the job. This field accepts
   * and sanitizes HTML input, and also accepts bold, italic, ordered list, and
   * unordered list markup tags. The maximum number of allowed characters is
   * 3,000.
   *
   * @param string $instruction
   */
  public function setInstruction($instruction)
  {
    $this->instruction = $instruction;
  }
  /**
   * @return string
   */
  public function getInstruction()
  {
    return $this->instruction;
  }
  /**
   * Use this URI field to direct an applicant to a website, for example to link
   * to an online application form. The maximum number of allowed characters for
   * each entry is 2,000.
   *
   * @param string[] $uris
   */
  public function setUris($uris)
  {
    $this->uris = $uris;
  }
  /**
   * @return string[]
   */
  public function getUris()
  {
    return $this->uris;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(ApplicationInfo::class, 'Google_Service_CloudTalentSolution_ApplicationInfo');
