<?php
//
// V CARD
//
if ($_CONFIG['vcard'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#vcard") echo "show active"; ?>" id="vcard">
        <h4><?php echo getString('vcard'); ?></h4>
        <div class="row form-group">
            <div class="col-12">
                <label><?php echo getString('version'); ?></label>
                <select class="custom-select" name="vversion">
                  <option value="2.1">2.1</option>
                  <option value="3.0">3.0</option>
                  <option value="4.0">4.0</option>
                </select>
            </div>
            <div class="col-6">
                <label><?php echo getString('first_name'); ?></label>
                <input type="text" name="vname" class="form-control" required="required">
            </div>
            <div class="col-6">
                 <label><?php echo getString('last_name'); ?></label>
                <input type="text" name="vlast" class="form-control">
            </div>

            <div class="col-6">
                <label><?php echo getString('phone_number'); ?></label>
                <input type="text" name="vphone" class="form-control" required="required">
            </div>
            <div class="col-6">
                 <label><?php echo getString('mobile'); ?></label>
                <input type="text" name="vmobile" class="form-control">
            </div>

            <div class="col-6">
                <label><?php echo getString('email'); ?></label>
                <input type="email" name="vemail" class="form-control">
            </div>

            <div class="col-6">
                <label><?php echo getString('website'); ?></label>
                <input type="text" name="vurl" class="form-control" placeholder="http://">
            </div>

            <div class="col-12">
                <label><?php echo getString('company'); ?></label>
                <input type="text" name="vcompany" class="form-control">
            </div>

            <div class="col-6">
                <label><?php echo getString('jobtitle'); ?></label>
                <input type="text" name="vtitle" class="form-control">
            </div>


            <div class="col-6">
                <label><?php echo getString('fax'); ?></label>
                <input type="text" name="vfax" class="form-control">
            </div>


            <div class="col-12">
                 <label><?php echo getString('address'); ?></label>
                <textarea name="vaddress" class="form-control"></textarea>
            </div>
            <div class="col-sm-4">
                <label><?php echo getString('city'); ?></label>
                <input type="text" name="vcity" class="form-control">
            </div>
            <div class="col-sm-4 col-6">
                <label><?php echo getString('post_code'); ?></label>
                <input type="text" name="vcap" class="form-control">
            </div>
            <div class="col-sm-4 col-6">
                <label><?php echo getString('state'); ?></label>
                <input type="text" name="vcountry" class="form-control">
            </div>
        </div>
    </div>
<?php
}
