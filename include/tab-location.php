<?php
//
// LOCATION
//
if ($_CONFIG['location'] == true) { ?>
    <div class="tab-pane fade <?php if ($getsection === "#location") echo "show active"; ?>" id="location">
        <h4><?php echo getString('location'); ?></h4>
    <?php
    if ($_CONFIG['google_api_key'] == 'YOUR-API-KEY' || strlen($_CONFIG['google_api_key']) < 10) { ?>
    <p class="lead">Please set a <strong>google_api_key</strong> inside <strong>config.php</strong><br>
        <a target="_blank" href="https://developers.google.com/maps/documentation/javascript/get-api-key#get-an-api-key">
            &gt; How to get an api key for Gmaps
        </a>
    </p>
<?php 
    } else { ?>
<script src="https://maps.googleapis.com/maps/api/js?key=<?php echo $_CONFIG['google_api_key']; ?>&libraries=places"></script>
        <div style="min-height:350px">
            <div id="latlong">
                <input id="pac-input" class="controls" type="text" placeholder="<?php echo getString('search'); ?>">
                <input type="text" id="latbox" placeholder="<?php echo getString('latitude'); ?>" class="controls" name="lat" readonly>
                <input type="text" id="lngbox" placeholder="<?php echo getString('longitude'); ?>" class="controls" name="lng" readonly>
            </div>
            <div id="map-canvas"></div>
        </div>
<?php 
    } ?>
    </div>
<?php
}
