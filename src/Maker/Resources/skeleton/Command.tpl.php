<?= "<?php\n" ?>

namespace <?= $namespace; ?>;

final class <?= $class_name."\n" ?>
{
    public function __construct(
     <?php foreach ($attributes as $type => $attribute): ?>
         public readonly <?= $type ?> $<?= $attribute ?>,
     <?php endforeach; ?>
    ) {
    }
}
