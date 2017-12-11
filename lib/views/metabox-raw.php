<?php
    $col = 0;
    foreach ($fields as $field) { ?>
        <?= $field->render($post) ?>
<?php } ?>
