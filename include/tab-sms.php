<?php
//
// SMS
//
if ($_CONFIG['sms'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#sms") echo "show active"; ?>" id="sms">
        <h4><?php echo getString('sms'); ?></h4>
        <div class="row">
            <div class="col-sm-4">
               <div class="form-group">
                    <label><?php echo getString('country_code'); ?></label>
                    <?php
                    $output = '<select class="custom-select" name="countrycodesms">';
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
                    <input type="number" name="sms" placeholder="" class="form-control" required="required">
                </div>
            </div>

            <div class="col-sm-12">

                <div class="form-group">
                    <label><?php echo getString('message'); ?></label>
                    <textarea rows="3"  name="bodysms" class="form-control" required="required"></textarea>
                </div>

            </div>
        </div>
    </div>
<?php
}
