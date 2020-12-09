<?php
//
// LINK
//
if ($_CONFIG['link'] == true) { ?>
<div class="tab-pane fade <?php if ($getsection === "#link") echo "show active"; ?>" id="link">
	<h4><?php echo getString('link'); ?></h4>
    <div class="form-group">
        <label for="malink"><?php echo getString('link'); ?></label>
        <input type="url" name="link" id="malink" class="form-control" value="<?php if ($getsection === "#link" && $output_data) echo $output_data; ?>" placeholder="http://" required="required" />
    </div>
</div>
<?php
}
