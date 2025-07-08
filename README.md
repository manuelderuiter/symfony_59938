# Initial setup
- PostgreSQL
- PHP 8.x

# How to reproduce
`php bin/console app:create-lock`

You can find the caller at: https://github.com/symfony/scheduler/blob/7.3/Generator/Checkpoint.php

# Related code
## symfony/scheduler
https://github.com/symfony/scheduler/blob/7.3/Generator/Checkpoint.php
```php
$this->lock->refresh((float) $nextTime->format('U.u') - (float) $now->format('U.u') + $remaining);
```

## symfony/lock
```php
$platform instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform => 'CAST(EXTRACT(epoch FROM NOW()) AS INT)',
```

A possible solution could be:
```php
$platform instanceof \Doctrine\DBAL\Platforms\PostgreSQLPlatform => 'CAST(EXTRACT(epoch FROM NOW()) AS DOUBLE PRECISION)',
```
-----
# After updating the cast
Output:
```text
symfony_lock_issue git:(master) ✗ php bin/console app:create-lock -vvv
[debug] Successfully acquired the "foo" lock.
[debug] Expiration defined for "foo" lock for "30" seconds.
➜  symfony_lock_issue git:(master) ✗ php bin/console app:create-lock -vvv
Lock is already acquired, refreshing instead.
[debug] Expiration defined for "foo" lock for "300.3777777" seconds.
[debug] Notified event "console.terminate" to listener "Symfony\Component\Console\EventListener\ErrorListener::onConsoleTerminate".
```

Then in the database you will find:
`key_expiration
1751955113
`
