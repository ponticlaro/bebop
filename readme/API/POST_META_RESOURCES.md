Bebop API - Post Meta Resources
---

## Default Resources
### GET /_bebop/api/**{ post_type }**/:post_id/meta/:meta_key
Returns all meta valus for the target `meta_key`.  

**Available parameters**  
*No parameters allowed currently.*  

**Expected success response**  
On success this resource will return a list with all the meta entries.   

```json
[
    {
        "__type": "postmeta",
        "__post_id": 6,
        "__id": 57,
        "__key": "gallery",
        "value": {
            "id": 999
        }
    },
    {
        "__type": "postmeta",
        "__post_id": 6,
        "__id": 59,
        "__key": "gallery",
        "value": {
            "id": 12
        }
    }
]
```

### GET /_bebop/api/**{ post_type }**/:post_id/meta/:meta_key/:meta_id
Returns the value of `meta_id`.  

**Available parameters**  
*No parameters allowed currently.*  

**Expected success response**  
On success this resource will return the target meta entry.  

```json
{
    "__type": "postmeta",
    "__post_id": 6,
    "__id": 57,
    "__key": "gallery",
    "value": {
        "id": 999
    }
}
```

### POST /_bebop/api/**{ post_type }**/:post_id/meta/:meta_key/
Creates a new single meta entry for tha target `meta_key`.  

**Available parameters**  
- `storage_method`: This determines if an array or object is either serialized or encoded as JSON when stored in the database. Possible values are `serialize` or `json`. Default: **json**  

**Expected success response**  
On success this resource will return the new meta entry.  

```json
{
        "__type": "postmeta",
        "__post_id": 6,
        "__id": 89,
        "__key": "gallery",
        "value": {
            "id": 23
        }
    },
```

### PUT /_bebop/api/**{ post_type }**/:post_id/meta/:meta_key/:meta_id
Updates the value for the target `meta_id`.  

**Available parameters**  
- `storage_method`: This determines if an array or object is either serialized or encoded as JSON when stored in the database. Possible values are `serialize` or `json`. Default: **json**  

**Expected success response**  
On success this resource will return the updated meta entry.  

```json
{
    "__type": "postmeta",
    "__post_id": 6,
    "__id": 57,
    "__key": "gallery",
    "value": {
        "id": 999999
    }
}
```

### DELETE /_bebop/api/**{ post_type }**/:post_id/meta/:meta_key/:meta_id
Deletes the target `meta_id`.

**Available parameters**  
*No parameters allowed currently.*  

**Expected success response**  
On sucess this resource will return the remaining items for the target `meta_key`.  

```json
[
    {
        "__type": "postmeta",
        "__post_id": 6,
        "__id": 57,
        "__key": "gallery",
        "value": {
            "id": 999
        }
    }
]
```