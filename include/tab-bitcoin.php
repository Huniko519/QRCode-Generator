<?php
//
// BITCOIN
//
if ($_CONFIG['bitcoin'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#bitcoin") echo "show active"; ?>" id="bitcoin">
        <h4><?php echo getString('bitcoin'); ?></h4>
        <div class="row form-group">
            <div class="col-sm-6">
                <label><?php echo getString('account'); ?></label>
                <input type="text" name="btc_account" class="form-control" required="required">
            </div>
           <div class="col-sm-6">
                <label><?php echo getString('amount'); ?></label>
                <div class="input-group">
                    <input type="text" name="btc_amount" class="form-control">
                    <div class="input-group-append">
                        <span class="input-group-text">BTC</span>
                    </div>
                </div>
                <?php echo getBtcRates(); ?>
            </div>

            <div class="col-sm-6">
                <label><?php echo getString('item_name'); ?></label>
                <input type="text" name="btc_label" class="form-control">
            </div>

            <div class="col-sm-6">
                <label><?php echo getString('message'); ?></label>
                <input type="text" name="btc_message" class="form-control">
            </div>


        </div>
    </div>
<?php
}
