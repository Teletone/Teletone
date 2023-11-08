[To main documentation](00_MAIN.md)

# Bot on classes

You can build a bot on classes, regular and static functions

To use a method that will create a class, use the format: **CLASS_NAME->METHOD_NAME**

Example:

```php
class MyClass
{
    function __construct()
    {
        echo "call __construct\n";
    }

    public function func($u)
    {
        $u->answer('ok');
    }
}

$r->message('test', 'MyClass->func');
```

The second method calls a static function and does not instantiate the class. Call format: **CLASS_NAME::METHOD_NAME**

Example:

```php
class MyClass
{
    static function func($u)
    {
        $u->answer('ok');
    }
}

$r->message('test', 'MyClass::func');
```

All this also works with namespaces and class extends

[Next chapter: Navigations through inline menu](09_NAVIGATIONS_INLINE.md)