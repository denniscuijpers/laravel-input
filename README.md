# laravel-input

### Installation
```bash
composer require denniscuijpers/laravel-input
```

```php
<?php
use DennisCuijpers\Input\Input;

$input = Input::make($request->all());

$validated = $input->validate([
    'userId' => ['required', 'exists:users,id'],
]);

$model->update($validated->assemble([
    'user_id' => ['userId', 'integer'],
]);
```
