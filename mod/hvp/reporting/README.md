H5P-PHP-Report
==========

A generic library for rendering H5P reports for content types based on their 
xAPI statements.

## Usage

1. To start with we need xAPI data from the content we will render, covered in [Content type requirements](#content-type-requirements).
2. We can then call ```H5PReport->generateReport``` with the content statement and an optional parent ID. Parent ID is provided if the content type is the parent of other content. This can be determined from its children.
3. Returned is HTML for the complete report.

### Generate report

Generate report expect a special format of the xAPI data, an object with the 
several properties. Following is a simple example:

```php
(object) array(
  'interaction_type' => 'choice',
  'description' => 'Do you like cake ?',
  'correct_responses_pattern' => '[0]',
  'response': '2',
  'extras': '{"choices":[{"id":0,"description":{"en-US":"Of course, who doesn't ?"}},{"id":1,"description":{"en-US":"No"}},{"id":2,"description":{"en-US":"What is a cake ?"}}},'
)
```

Note that ```correct_responses_pattern``` and ```extras``` should be json 
encoded.

## Content type requirements

The plugin requires content types to implement the ```getXAPIData``` [contract](https://h5p.org/documentation/developers/contracts#guides-header-6)
 in order to get the proper reports with a ```statement``` object and an optional 
```children``` array:
```javascript
getXAPIData = function () {
  return {
    statement: {},
    children: []
  }
}
```

### Statement

The statement object is a standard xAPI statement, which should adhere to the
 [xAPI specification](https://github.com/adlnet/xAPI-Spec/blob/master/xAPI-Data.md#table-of-contents). You can 
 see an example for how this looks for [True False question](https://h5p.org/true-false) in [Appendix A](#appendix-a).
 

### Children

The children array is a collection of statements for the children of the 
content.
An Interactive Video with "Summary" and "Mark The Words" content inside of it would have its own statement and two children items with their own statements.
Children may also have children, so you can follow the chain all the way down to the lowest level.
A complete example of [Single Choice Set](https://h5p.org/single-choice-set) can be found in [Appendix B](#appendix-b).

## Appendix A

An example of an xAPI statement from [True False question](https://h5p.org/true-false).

```json
{  
  "actor":{  
    "name":"user",
    "mbox":"mailto:user@mail.com",
    "objectType":"Agent"
  },
  "verb":{  
    "id":"http://adlnet.gov/expapi/verbs/answered",
    "display":{  
      "en-US":"answered"
    }
  },
  "object":{  
    "id":"https://h5p.org/true-false",
    "objectType":"Activity",
    "definition":{  
      "extensions":{  
        "http://h5p.org/x-api/h5p-local-content-id":34806
      },
      "name":{  
        "en-US":"True/False Question"
      },
      "interactionType":"true-false",
      "type":"http://adlnet.gov/expapi/activities/cmi.interaction",
      "description":{  
        "en-US":"Oslo is the capital of Norway.\n"
      },
      "correctResponsesPattern":[  
        "True"
      ]
    }
  },
  "context":{  
    "contextActivities":{  
      "category":[  
        {  
          "id":"http://h5p.org/libraries/H5P.TrueFalse-1.0",
          "objectType":"Activity"
        }
      ]
    }
  },
  "result":{  
    "score":{  
      "min":0,
      "max":1,
      "raw":0,
      "scaled":0
    },
    "completion":true,
    "success":false,
    "duration":"PT4.92S",
    "response":""
  }
}
```

## Appendix B

An example of a complete ```getXAPIData``` call to [Single Choice Set](https://h5p.org/single-choice-set).

```json
{  
  "statement":{  
    "actor":{  
      "name":"user",
      "mbox":"mailto:user@mail.com",
      "objectType":"Agent"
    },
    "verb":{  
      "id":"http://adlnet.gov/expapi/verbs/answered",
      "display":{  
        "en-US":"answered"
      }
    },
    "object":{  
      "id":"https://h5p.org/single-choice-set",
      "objectType":"Activity",
      "definition":{  
        "interactionType":"compound",
        "type":"http://adlnet.gov/expapi/activities/cmi.interaction",
        "extensions":{  
          "http://h5p.org/x-api/h5p-local-content-id":1515
        }
      }
    },
    "result":{  
      "duration":"PT0S",
      "score":{  
        "raw":0,
        "min":0,
        "max":3,
        "scaled":0
      }
    }
  },
  "children":[  
    {  
      "statement":{  
        "actor":{  
          "name":"user",
          "mbox":"mailto:user@mail.com",
          "objectType":"Agent"
        },
        "verb":{  
          "id":"http://adlnet.gov/expapi/verbs/answered",
          "display":{  
            "en-US":"answered"
          }
        },
        "context":{  
          "contextActivities":{  
            "parent":[  
              {  
                "id":"https://h5p.org/single-choice-set",
                "objectType":"Activity"
              }
            ]
          }
        },
        "object":{  
          "id":"https://h5p.org/single-choice-set?subContentId=51445ea9-9c98-437a-8981-72d46d6e86c4",
          "objectType":"Activity",
          "definition":{  
            "description":{  
              "en-US":"Goji berries are also known as ..."
            },
            "interactionType":"choice",
            "correctResponsesPattern":[  
              "0"
            ],
            "type":"http://adlnet.gov/expapi/activities/cmi.interaction",
            "choices":[  
              {  
                "id":"0",
                "description":{  
                  "en-US":"Wolfberries"
                }
              },
              {  
                "id":"1",
                "description":{  
                  "en-US":"Catberries"
                }
              },
              {  
                "id":"2",
                "description":{  
                  "en-US":"Bearberries"
                }
              }
            ],
            "extensions":{  
              "http://h5p.org/x-api/h5p-local-content-id":1515,
              "http://h5p.org/x-api/h5p-subContentId":"51445ea9-9c98-437a-8981-72d46d6e86c4"
            }
          }
        },
        "result":{  
          "response":"",
          "duration":"PT0S",
          "score":{  
            "raw":0,
            "min":0,
            "max":1,
            "scaled":0
          }
        }
      }
    },
    {  
      "statement":{  
        "actor":{  
          "name":"user",
          "mbox":"mailto:user@mail.com",
          "objectType":"Agent"
        },
        "verb":{  
          "id":"http://adlnet.gov/expapi/verbs/answered",
          "display":{  
            "en-US":"answered"
          }
        },
        "context":{  
          "contextActivities":{  
            "parent":[  
              {  
                "id":"https://h5p.org/single-choice-set",
                "objectType":"Activity"
              }
            ]
          }
        },
        "object":{  
          "id":"https://h5p.org/single-choice-set?subContentId=95b60491-e203-4bc5-956c-89e966b1005d",
          "objectType":"Activity",
          "definition":{  
            "description":{  
              "en-US":"Goji berries are native to ..."
            },
            "interactionType":"choice",
            "correctResponsesPattern":[  
              "0"
            ],
            "type":"http://adlnet.gov/expapi/activities/cmi.interaction",
            "choices":[  
              {  
                "id":"0",
                "description":{  
                  "en-US":"Asia"
                }
              },
              {  
                "id":"1",
                "description":{  
                  "en-US":"Africa"
                }
              },
              {  
                "id":"2",
                "description":{  
                  "en-US":"Europe"
                }
              }
            ],
            "extensions":{  
              "http://h5p.org/x-api/h5p-local-content-id":1515,
              "http://h5p.org/x-api/h5p-subContentId":"95b60491-e203-4bc5-956c-89e966b1005d"
            }
          }
        },
        "result":{  
          "response":"",
          "duration":"PT0S",
          "score":{  
            "raw":0,
            "min":0,
            "max":1,
            "scaled":0
          }
        }
      }
    },
    {  
      "statement":{  
        "actor":{  
          "name":"user",
          "mbox":"mailto:user@mail.com",
          "objectType":"Agent"
        },
        "verb":{  
          "id":"http://adlnet.gov/expapi/verbs/answered",
          "display":{  
            "en-US":"answered"
          }
        },
        "context":{  
          "contextActivities":{  
            "parent":[  
              {  
                "id":"https://h5p.org/single-choice-set",
                "objectType":"Activity"
              }
            ]
          }
        },
        "object":{  
          "id":"https://h5p.org/single-choice-set?subContentId=aa694288-df4d-4ed3-8f81-f47fa3d27fe4",
          "objectType":"Activity",
          "definition":{  
            "description":{  
              "en-US":"Goji berries are usually sold ..."
            },
            "interactionType":"choice",
            "correctResponsesPattern":[  
              "0"
            ],
            "type":"http://adlnet.gov/expapi/activities/cmi.interaction",
            "choices":[  
              {  
                "id":"0",
                "description":{  
                  "en-US":"Dried"
                }
              },
              {  
                "id":"1",
                "description":{  
                  "en-US":"Pickled"
                }
              },
              {  
                "id":"2",
                "description":{  
                  "en-US":"Frozen"
                }
              }
            ],
            "extensions":{  
              "http://h5p.org/x-api/h5p-local-content-id":1515,
              "http://h5p.org/x-api/h5p-subContentId":"aa694288-df4d-4ed3-8f81-f47fa3d27fe4"
            }
          }
        },
        "result":{  
          "response":"",
          "duration":"PT0S",
          "score":{  
            "raw":0,
            "min":0,
            "max":1,
            "scaled":0
          }
        }
      }
    }
  ]
}
```

## License

(The MIT License)

Copyright (c) 2016 Joubel AS
 
Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
 
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
