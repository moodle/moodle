# Assignment JSON

## Create a new folder in Moodledata folder and place the JSON
- for example moodle data folder is $CFG->dirroot = "/var/www/moodledata"
- we have to create new folder in /var/www/moodledata/qbassign
- Then place the your assignment file in JSON format. Just refer the assignment.json in this folder

## manifest.json
```json

[
	{
		"book": {
			"name": "Programming and Artificial Intelligence",
			"code": "DPL10",
			"category": "DigiPro",
			"categorycode": "DP",
			"level": "10",
			"chapters": [
				{
					"route": "book/DPL10/DPL10-TP1-000.mdx",
					"title": "Chapter 1",
					"type": "topic",
					"uid": "DPL10-TP1-000",
					"children": [
						{
							"title": "Loops in Python Challenge 1",
							"uid": "DPL10-TP1-CHNG1",
							"route": "challenge/DPL10-TP1-CHNG1",
							"fname": "DPL10-TP1-CHNG1.json",
							"type": "challenge"
						}
					]
				}
			]
		}
	}
]
```

### place the DPL10-TP1-CHNG1.json file into /var/www/moodledata/qbassign/DPL10-TP1-CHNG1.json

After that import the manifest file. We have to implement assignment read and update logic.

## Assignemnt JSON
```json
{
	"title" : "Challenge 1",
	"instructions": "Write a sample python program",
	"additional_files": "",
	"submission_type": "codeblock",
	"__comment__submission_type": "text/file/codeblock",
	"online_text_limit":"",
	"filesubmission_size":"",
	"filesubmission_count":"",
	"codeblock_mode": "automate",
	"__comment__codeblock_mode": "manual/automate",
	"grade_type":"scale",
	"__comment__grade_type":"scale/point",
	"grading_method": "direct",
	"__comment__grading_method": "direct/rubric/marking_guide",
	"grade_to_pass": "",
	"language" : "python",
	"type" : "codingblock",
	"testcases" : [
	  {
		"value" : "Case = Test integer sum\ninput = 1 2 3 5\noutput = 11"
	  },
	  {
		"value" : "Case = Test integer sum\ninput = 1\n -2\n 3\n 5\noutput = 7"
	  }
	]
}
```
