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

namespace Google\Service\Aiplatform;

class GoogleCloudAiplatformV1RagFileParsingConfig extends \Google\Model
{
  protected $layoutParserType = GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser::class;
  protected $layoutParserDataType = '';
  protected $llmParserType = GoogleCloudAiplatformV1RagFileParsingConfigLlmParser::class;
  protected $llmParserDataType = '';

  /**
   * The Layout Parser to use for RagFiles.
   *
   * @param GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser $layoutParser
   */
  public function setLayoutParser(GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser $layoutParser)
  {
    $this->layoutParser = $layoutParser;
  }
  /**
   * @return GoogleCloudAiplatformV1RagFileParsingConfigLayoutParser
   */
  public function getLayoutParser()
  {
    return $this->layoutParser;
  }
  /**
   * The LLM Parser to use for RagFiles.
   *
   * @param GoogleCloudAiplatformV1RagFileParsingConfigLlmParser $llmParser
   */
  public function setLlmParser(GoogleCloudAiplatformV1RagFileParsingConfigLlmParser $llmParser)
  {
    $this->llmParser = $llmParser;
  }
  /**
   * @return GoogleCloudAiplatformV1RagFileParsingConfigLlmParser
   */
  public function getLlmParser()
  {
    return $this->llmParser;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudAiplatformV1RagFileParsingConfig::class, 'Google_Service_Aiplatform_GoogleCloudAiplatformV1RagFileParsingConfig');
