```json
{
  "class_collection_action": "DrinksIt\\RuleEngineBundle\\Rule\\Action\\CollectionActions",
  "elements": [
    {
      "class_action": "DrinksIt\\RuleEngineBundle\\Rule\\Action\\Attribute\\NumberActionType",
      "properties": {
        "type": "number",
        "object_resource": "App\\Entity\\ModelNumber",
        "field": "fieldNameNumber",
        "action": {
          "math": "1 + 1 %self% * (%field.property% / 22)",
          "macros": [
            "%self%",
            "%field.property%"
          ]
        }
      }
    },
    {
      "class_action": "DrinksIt\\RuleEngineBundle\\Rule\\Action\\Attribute\\NumberActionType",
      "properties": {
        "type": "string",
        "object_resource": "App\\Entity\\Model",
        "field": "fieldName",
        "action": {
          "pattern": "Hello World %self%",
          "macros": [
            "%self%"
          ]
        }
      } 
    }
  ]
}
```
