<?php
//
// TEL
//
if ($_CONFIG['tel'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#tel") echo "show active"; ?>" id="tel">
        <h4><?php echo getString('tel'); ?></h4>
        <div class="row">
            <div class="col-sm-4">
               <div class="form-group">
                    <label><?php echo getString('country_code'); ?></label>
                    <?php
                    $output = '<select class="custom-select" name="countrycodetel">';
                    foreach ($countries as $i=>$row) {
                        $output .= '<option value="'.$row['code'].'" label="'.$row['name'].'">'.$row['name'].'</option>\n';
                    }
                    $output .= '</select>';
                    echo $output;
                    ?> 
                </div>
            </div>


            <div class="col-sm-8">
                <div class="form-group">
                    <label><?php echo getString('phone_number'); ?></label>
                    <input type="number" name="tel" placeholder="" class="form-control" required="required">
                </div>
            </div>
        </div>
    </div>
<?php
}
