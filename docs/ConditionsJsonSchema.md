```json
{
  "class_collection": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\CollectionCondition",
  "elements": [
    {
      "priority": 0,
      "type": "ANY",
      "result": true,
      "attribute_condition": null,
      "sub_conditions": {
        "class_collection": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\CollectionCondition",
        "elements": [
          {
            "priority": 0,
            "type": "ATTRIBUTE",
            "result": null,
            "attribute_condition": {
              "class_condition": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\AttributeCondition",
              "properties": {
                "class_resource": "App\\Entity\\ModelClass",
                "field_name": "name",
                "operator": "EQ",
                "type": "string",
                "value": "Hello World"
              }
            },
            "sub_conditions": null
          },
          {
            "priority": 1,
            "type": "ATTRIBUTE",
            "result": null,
            "attribute_condition": {
              "class_condition": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\AttributeCondition",
              "properties": {
                "class_resource": "App\\Entity\\ModelClass",
                "field_name": "name",
                "operator": "EQ",
                "type": "number",
                "value": 23
              }
            },
            "sub_conditions": null
          },
          {
            "sub_conditions": {
              "priority": 2,
              "type": "ALL",
              "result": false,
              "attribute_condition": null,
              "sub_conditions": {
                "class_collection": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\CollectionCondition",
                "elements": [
                  {
                    "priority": 0,
                    "type": "ATTRIBUTE",
                    "result": null,
                    "attribute_condition": {
                      "class_condition": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\AttributeCondition",
                      "properties": {
                        "class_resource": "App\\Entity\\ModelClass",
                        "field_name": "name",
                        "operator": "EQ",
                        "type": "string",
                        "value": "Hello World"
                      }
                    },
                    "sub_conditions": null
                  },
                  {
                    "priority": 0,
                    "type": "ATTRIBUTE",
                    "result": null,
                    "attribute_condition": {
                      "class_condition": "DrinksIt\\RuleEngineBundle\\Rule\\Condition\\AttributeCondition",
                      "properties": {
                        "class_resource": "App\\Entity\\ModelClass",
                        "field_name": "name",
                        "operator": "EQ",
                        "type": "number",
                        "value": 23
                      }
                    },
                    "sub_conditions": null
                  }
                ]
              }
            }
          }
        ]
      }
    }
  ]
}
```
