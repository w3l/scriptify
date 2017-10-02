# scriptify

## Usage

```php
<?php
echo (new w3l\scriptify)->encode('
var owner = "us";
alert("All your base are belong to " + owner);
');
```

Results: 
```html
<script src="scriptify.php?js=dmFyIG93bmVyPSJ1cyI7YWxlcnQoIkFsbCB5b3VyIGJhc2UgYXJlIGJlbG9uZyB0byAiK293bmVyKQ%3D%3D" integrity="sha384-e7SU+5i8b6bk4ck7S4uufgvLeOPVujVWDF4LdCN/H/5IPQVC021203V7dcB+MLFI"></script>
```

It's also possible to send attributes:
```php
echo (new w3l\scriptify)->encode('
<script async crossorigin="anonymous">
var owner = "us";
alert("All your base are belong to " + owner);
</script>
');
```

Results: 
```html
<script src="scriptify.php?js=dmFyIG93bmVyPSJ1cyI7YWxlcnQoIkFsbCB5b3VyIGJhc2UgYXJlIGJlbG9uZyB0byAiK293bmVyKQ%3D%3D" integrity="sha384-e7SU+5i8b6bk4ck7S4uufgvLeOPVujVWDF4LdCN/H/5IPQVC021203V7dcB+MLFI" async crossorigin="anonymous"></script>
```


Call `w3l\scriptify::decode` to generate the code externally: 

```javascript
var owner="us";alert("All your base are belong to "+owner)
```

[Example scriptify.php](examples/scriptify.php)
```php
<?php
header("Content-Type: application/javascript");
echo (new w3l\scriptify)->decode($_GET["js"] ?? "");
```