<table class="form-table">
	<tbody>
	<?php
        $col = 0;
	    foreach ($fields as $field) {
	        if ($col==0) echo '<tr>'; ?>
                <?php if ($field->label) { ?>
                    <th scope="row">
                        <label for="meta-text" class="prfx-row-title"><?= $field->label ?></label>
                    </th>
                    <td>
                        <?= $field->render($post) ?>
                    </td>
                <?php } else { ?>
                    <td colspan="<?= 2*$columns ?>">
                        <?= $field->render($post) ?>
                    </td>
                </tr>
		        <?php } ?>
		    <?php
            $col++;
            if ($field->linebreak && $col<$columns) {
	            echo '<td colspan="' . 2*($columns-$col) . '">';
	            $col=$columns;
            }
            if ($col==$columns) {
                $col = 0;
                echo '</tr>';
            }
            ?>
        <?php } ?>
	</tbody>
</table>
