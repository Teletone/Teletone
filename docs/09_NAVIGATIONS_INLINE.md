[To main documentation](00_MAIN.md)

# Navigations through inline menu

For example, we have two menus. The second menu has a back button. It would seem that we need a new callback and a bunch of code, but with Teletone we can do this:

```php
$r->message('menu1', 'menu1');
$r->callbackQuery('menu1', 'menu1');
function menu1($u)
{
    $u->answerOrEdit('menu 1', [
        'reply_markup' => new InlineKeyboardMarkup([
            [ [ 'text' => 'menu 2', 'callback_data' => 'menu2' ] ]
        ])
    ]);
}

$r->callbackQuery('menu2', 'menu2');
function menu2($u)
{
    $u->edit('menu 2', [
        'reply_markup' => new InlineKeyboardMarkup([
            [ [ 'text' => 'back', 'callback_data' => 'menu1' ] ]
        ])
    ]);
}
```

The answerOrEdit function checks whether the request is a callback_query and edits the message, otherwise sends a new message to the user

Also use the **edit** function to quickly edit a message, only works with callback_query

[Next chapter: Groups](10_GROUPS.md)