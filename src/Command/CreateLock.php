<?php

namespace App\Command;

use Doctrine\DBAL\Driver\PDO\Exception;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Lock\Key;
use Symfony\Component\Lock\LockFactory;

#[AsCommand(name: 'app:create-lock')]
final class CreateLock extends Command
{
    public function __construct(
        private readonly LockFactory $lockFactory,
    ) {
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        if (file_exists(__DIR__ . '/lock.txt')) {
            $key = unserialize(file_get_contents(__DIR__ . '/lock.txt'), ['allowed_classes' => [Key::class]]);
        } else {
            $key = new Key('foo');
        }

        $lock = $this->lockFactory->createLockFromKey($key, 30, false);

        try {
            if ($lock->isAcquired()) {
                $output->writeln('Lock is already acquired, refreshing instead.');
                $lock->refresh(300.3777777);
            } else {
                $lock->acquire();
            }
        } catch (\Doctrine\DBAL\Driver\Exception) {
            $lock->acquire();
        }

        file_put_contents(__DIR__ . '/lock.txt', serialize($key));

        return Command::SUCCESS;
    }
}
