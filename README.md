# Laravel Structurized Todos

## Attention. Not stable. In development.

### About

Command helps use and structurize a lot of todos with next syntax:
`TODO:[category|priority:<high|medium|low>]:<free multiline task text>:ENDTODO`

I had one long-term project and did not use any task trackers (such as Jira or Asana), as a result in the code were a
lot of unstructured todos. And I found a way to do todos structured, this format of use has justified itself.

### How to install

- Run: `composer require volodymyroliinyk/laravel-todos-structurizer`
- Or, add to the end of `require` section line: `"volodymyroliinyk/laravel-todos-structurizer": "^0.1.0"`

### How to use

- Run: `php artisan todos-structurizer:collect > result_todos_structurized.log`
- Or, run: `php artisan todos-structurizer:collect --category=some-todo-category-name > result_todos_structurized.log`

### Test running

- `vendor/bin/phpunit tests/Commands/TodosStructurizerCommandTest.php`

### todo

- live test
- fix and finish unit tests
- maybe code refactoring
- readable readme
- add priority property to the command for filtering by priority