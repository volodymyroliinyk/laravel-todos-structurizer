# Laravel Structurized Todos

### About

Command helps use and structurize a lot of todos with next syntax:
`TODO:[category|priority:<high|medium|low>]:<free multiline task text>:ENDTODO`

I had one long-term project and did not use any task trackers (such as Jira or Asana), as a result in the code were a
lot of unstructured todos. And I found a way to do todos structured, this format of use has justified itself.

### How to install

1) Modify `composer.json`: Add package and version to `require`:

`"require": {
...,
"volodymyroliinyk/laravel-todos-structurizer": "v0.1.32"
},`

2) Modify `composer.json`: Add package type and url to `repositories`:

`"repositories": [
...,
{
"type": "vcs",
"url": "https://github.com/volodymyroliinyk/laravel-todos-structurizer.git"
}
]`

3) Publish config file,
   run: `php artisan vendor:publish --provider="VolodymyrOliinyk\TodosStructurizer\Providers\PackageServiceProvider"`
4) Edit `todos-structurizer.php` for own needs.

### How to use:

- Run: `php artisan todos-structurizer:collect > result_todos_structurized.log`
- Or, run: `php artisan todos-structurizer:collect --category=some-todo-category-name > result_todos_structurized.log`
- Or,
  run: `php artisan todos-structurizer:collect --category=some-todo-category-name --priority=high > result_todos_structurized.log` (
  Available priorities: `high`,`medium`,`low`)
- For JetBrains PhpStorm, for correct highlighting add pattern to TODO filter: `\bTODO\:((.|\n)*)\:ENDTODO\b*`

### Development:

- Run: `vendor/bin/phpstan`
- Run: `vendor/bin/phpcbf`
- Run: `vendor/bin/phpunit tests/Commands/TodosStructurizerCommandTest.php` (need to fix)

### todo

- fix and finish phpunit tests, error during test running.
- code refactoring