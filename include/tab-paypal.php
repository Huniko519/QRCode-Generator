<?php
//
// PAYPAL
//
if ($_CONFIG['paypal'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#paypal") echo "show active"; ?>" id="paypal">
        <h4><?php echo getString('paypal'); ?></h4>
        <div class="row form-group">

            <div class="col-sm-6">
                <label><?php echo getString('type'); ?></label>
                <select class="custom-select" name="pp_type" id="pp_type">
                  <option value="_xclick"><?php echo getString('buy_now'); ?></option>
                  <option value="_cart"><?php echo getString('add_to_cart'); ?></option>
                  <option value="_donations"><?php echo getString('donations'); ?></option>
                </select>
            </div>

            <div class="col-sm-6">
                <label><?php echo getString('email'); ?></label>
                <input type="email" name="pp_email" class="form-control" required="required">
                <small><?php echo getString('pp_email'); ?></small>
            </div>
        </div>

        <div class="row form-group">
            <div class="col-8">
                <label><?php echo getString('item_name'); ?></label>
                <input type="text" name="pp_name" class="form-control" required="required">
            </div>

            <div class="col-4">
                <label><?php echo getString('item_id'); ?></label>
                <input type="text" name="pp_id" class="form-control">
            </div>
        </div>

        <div class="row form-group">
           <div class="col-6 col-sm-3 yesdonation">
                <label><?php echo getString('price'); ?></label>
                <input type="text" name="pp_price" class="form-control">
            </div>

            <div class="col-6 col-sm-3 yesdonation">
                <label><?php echo getString('currency'); ?></label>
                <select class="custom-select" name="pp_currency" id="setcurrency">
                  <option value="USD">USD</option>
                  <option value="EUR">EUR</option>
                  <option value="AUD">AUD</option>
                  <option value="CAD">CAD</option>
                  <option value="CZK">CZK</option>
                  <option value="DKK">DKK</option>
                  <option value="HKD">HKD</option>
                  <option value="HUF">HUF</option>
                  <option value="JPY">JPY</option>
                  <option value="NOK">NOK</option>
                  <option value="NZD">NZD</option>
                  <option value="PLN">PLN</option>
                  <option value="GBP">GBP</option>
                  <option value="SGD">SGD</option>
                  <option value="SEK">SEK</option>
                  <option value="CHF">CHF</option>
                </select>
            </div>

            <div class="col-6 col-sm-3 nodonation">
                <label><?php echo getString('shipping'); ?></label>
                <div class="input-group">
                    <input type="text" name="pp_shipping" class="form-control" placeholder="0.00">
                    <div class="input-group-append">
                        <span class="input-group-text" id="getcurrency">USD</span>
                    </div>
                </div>
            </div>
            <div class="col-6 col-sm-3 nodonation">
                <label><?php echo getString('tax_rate'); ?></label>
                <div class="input-group">
                    <input type="text" name="pp_tax" class="form-control" placeholder="0.00">
                    <div class="input-group-append">
                        <span class="input-group-text">%</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
<?php
}
