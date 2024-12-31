# Moodle tool_aiconnect

This plugin is a fork of the local_ai_connector plugin https://github.com/enovation/moodle-local_ai_connector created by Irish Moodle partner Ennovation. For custom development and consultancy for this plugin contact Moodle Partner Catalyst EU (https://www.catalyst-eu.net/).

The main difference is that it can be configured for providers of AI Large language models other than OpenAI's ChatGPT, and was developed to target the Ollama self hosting tool. Support for image related LLM's (DALL-E/Stable Diffusion) has been removed

It was created to support the Moodle aitext question type which can be found here https://github.com/marcusgreen/moodle-qtype_aitext

## AI Class
The ai class is part of the tool_aiconnect namespace and provides functionality for interacting with AI models and making requests to AI APIs.

# Class Properties
**$apikey**: Stores the API key required for authentication with AI services

**$model:** Represents the AI model used for generating responses.

**$temperature:** Controls the randomness of generated responses.

**$max_length**: Specifies the maximum length of the generated response.

**$top_p:** Determines the cumulative probability cutoff for the generated response.

**$frequency_penalty**: Adjusts the penalty for frequently used tokens in the generated response.

**$presence_penalty:** Adjusts the penalty for tokens already present in the prompt in the generated response.

# API Keys:
For ChatGPT you can retrieve your API key from: https://platform.openai.com/account/api-keys. There is currently no built in API system for Ollama but this repo may help with that https://github.com/ParisNeo/ollama_proxy_server


## Methods:
prompt($prompttext): Generates a response using the AI model and the given prompt text. Returns the generated response.
 text and optional image. Returns the URL of the generated image.


## Usage
To use the ai class, follow these steps:

**Create an instance of the ai class.**
**$ai = new \tool_aiconnect\ai\ai();**

**Call the desired method on the instance.**
$response = $ai->prompt('Please generate a response for this prompt.');

## Configuration Settings
To configure the AI class and customize its behavior, you can use the following settings at /admin/settings.php?section=tool_aiconnect

**OpenAI API Key**: Provide the API key for authentication with OpenAI services (ignored by Ollama)

**Source of Truth:** Specify the source of truth for the AI model.

**Model**: Select the AI model to be used for generating responses. The default value is 'gpt-4'.

**Temperature**: Set the temperature value to control the randomness of generated responses.

**Max Length**: Set the maximum length of the generated response.

**Top P**: Set the cumulative probability cutoff for the generated response.

**Frequency Penalty**: Adjust the penalty for frequently used tokens in the generated response.

**Presence Penalty**: Adjust the penalty for tokens already present in the prompt in the generated response.

Please note that the availability and functionality of these settings may depend on the specific AI models and APIs used.

### Error Handling
The ai class throws moodle_exception exceptions in case of errors. You should handle these exceptions appropriately to provide meaningful feedback to the user.

### PHPUnit tests

PHPTests with the key are being developed and will require the following in config.php

```
define("AICONNECT_API_KEY", "putyourkeyhere");
```
