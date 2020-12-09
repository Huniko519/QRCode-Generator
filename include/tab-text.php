<?php
//
// TEXT
//
if ($_CONFIG['text'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#text") echo "show active"; ?>" id="text">
        <h4><?php echo getString('text'); ?></h4>
        <div class="form-group">
            <label><?php echo getString('message'); ?></label>
            <textarea rows="3" name="data" class="form-control" required="required"><?php if ($getsection === "#text" && $output_data) echo $output_data; ?></textarea>
        </div>
    </div>
<?php
}
