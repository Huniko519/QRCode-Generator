<?php
//
// WHATSAPP
//
if ($_CONFIG['whatsapp'] == true) { ?>
    <div class="tab-pane fade<?php if ($getsection === "#whatsapp") echo " show active"; ?>" id="whatsapp">
        <h4>WhatsApp</h4>
        <div class="row">
            <div class="col-sm-4">
               <div class="form-group">
                    <label><?php echo getString('country_code'); ?></label>
                    <?php
                    $output = '<select class="custom-select" name="wapp_countrycode" required="required">';
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
                    <input type="number" name="wapp_number" placeholder="" class="form-control" required="required">
                </div>
            </div>

            <div class="col-sm-12">
                <div class="form-group">
                    <label><?php echo getString('message'); ?></label>
                    <textarea rows="3"  name="wapp_message" class="form-control"></textarea>
                </div>
            </div>
        </div>
    </div>
<?php
}
