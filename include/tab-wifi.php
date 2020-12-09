<?php
//
// WI FI
//
if ($_CONFIG['wifi'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#wifi") echo "show active"; ?>" id="wifi">
        <h4><?php echo getString('wifi'); ?></h4>
        <div class="row form-group">
            <div class="col-md-4">
                <label><?php echo getString('network_name'); ?></label>
                <input type="text" name="ssid" placeholder="SSID" class="form-control" required="required">
            </div>
            <div class="col-md-4">
                <label><?php echo getString('network_type'); ?></label>
                <select class="custom-select" name="networktype">
                  <option value="WEP">WEP</option>
                  <option value="WPA">WPA/WPA2</option>
                  <option value=""><?php echo getString('no_encryption'); ?></option>
                </select>
            </div>
            <div class="col-md-4">
                <label><?php echo getString('password'); ?></label>
                <input type="text" name="wifipass" class="form-control">
            </div>
        </div>

        <div class="row form-group">
            <div class="col-12">
                <div class="custom-control custom-checkbox">
                    <input type="checkbox" class="custom-control-input" id="wifihidden" name="wifihidden">
                    <label class="custom-control-label" for="wifihidden"><?php echo getString('hidden'); ?></label>
                </div>
            </div>
        </div>
    </div>
<?php 
}
