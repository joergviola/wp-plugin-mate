<select class="admin-input" name="<?= $name ?>" id="<?= $name ?>">
	<?php foreach ($options as $value=>$label) { ?>
		<option value="<?= $value ?>" <?= $value==$selected?'selected':''?>><?= $label ?></option>
	<?php } ?>
</select>
