# ARepository 

## Install
If you want to use other project, You can:

- Copy folder `modules/arepository` into your root project

- Open file `composer.json` and edit

```$json
{
    ...
    "extra": {
        "laravel": {
          ...
          "providers": [
            "Herode\\ARepo\\ARepoServiceProvider"
          ],
          "aliases": {
            "ARepo": "Herode\\ARepo\\ARepo"
          }
        }
    },
    "autoload": {
        "psr-4": {
          ...
          "Herode\\ARepo\\": "modules/arepository/src/"
        },
        ...
    },
    ...
}
```

After run: `composer dump-autoload`

## How to use

### Run make new arepository

`php artisan make:arepository [Name]`

Example, you want to create new UserRepository

`php artisan make:arepository User`