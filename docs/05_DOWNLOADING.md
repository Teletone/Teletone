[To main documentation](00_MAIN.md)

# Downloading files

You can download files (images, videos, etc.) with just one command

To do this, use the $update function:

## download

**path** - Path to the file to save

Returns true if saving is successful, false otherwise

Example:

```php
$update->download('photo.jpg');
// or
$update->download('/home/user/photo.jpg');
```

[Next chapter: Webhook](06_WEBHOOK.md)