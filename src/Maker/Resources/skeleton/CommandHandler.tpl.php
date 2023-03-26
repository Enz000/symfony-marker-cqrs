<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

use <?= $commandPath; ?>;

final class <?= $class_name."\n" ?>
{
    public function __construct() {
    }

    public function __invoke(<?= $commandClassName ?> $command): void
    {

        //Dispatch your event here.
    }
}
