<html>
<head>
  <!-- Forthusk CSS -->
  <link rel="stylesheet" type="text/css" href="/public/css/mission_alerts.css" />
  <!-- JQUERY -->
  <link rel="stylesheet" href="/public/css/jquery-ui-1.12.1.min.css">
  <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-3.3.1.min.js"></script>
  <script src="/public/js/libs/jquery/jquery-3.3.1/jquery-ui-1.12.1.min.js"></script>

  <script src="/public/js/libs/jquery.transform.js-master/jquery.transform2d.js"></script>
  <script src="/public/js/libs/jquery-mousewheel-master/jquery.mousewheel.min.js"></script>

</head>
<body>

  <div id="sidemenu" style="text-align:center;padding-top:10px;">
    <strong>Fortnite PvE Map Builder</strong>

    <hr />
    <strong>Add a Zone</strong><br />
    <img zone="industrial" class="add_zone" src="/public/images/mission_alerts/industrial.png" style="width:100px;" />
    <img zone="forest" class="add_zone" src="/public/images/mission_alerts/forest.png" style="width:100px;" />
    <br />
    <img zone="city" class="add_zone" src="/public/images/mission_alerts/city.png" style="width:100px;" />
    <img zone="suburbs" class="add_zone" src="/public/images/mission_alerts/suburbs.png" style="width:100px;" />
    <br />
    <img zone="grasslands" class="add_zone" src="/public/images/mission_alerts/grasslands.png" style="width:100px;" />

    <hr />
    <strong>Stormshields</strong><br />
    <button class="add_zone" zone="stormshield_stonewood">Add Stonewood</button>
    <br />
    <button class="add_zone" zone="stormshield_plankerton">Add Plankerton</button>
    <br />
    <button class="add_zone" zone="stormshield_canny_valley">Add Canny Valley</button>
    <br />
    <button class="add_zone" zone="stormshield_twine_peaks">Add Twine Peaks</button>
    <br />

    <hr />
    <button id="set_scale_100">Zoom 100%</button>

    <hr />
    <button id="get_code">Get Code</button> <button id="set_code">Set Code</button>
    <br />
    <textarea id="the_code" style="width:100%;" rows="10"></textarea>
  </div>


  <div id="range">
    <div id="container">
      <div id="map">

      </div>
    </div>
  </div>


  <div style="display:none;">
    <img id="zone_stormshield_stonewood" class="drag" src="/public/images/mission_alerts/stormshield_stonewood.png" />
    <img id="zone_stormshield_plankerton" class="drag" src="/public/images/mission_alerts/stormshield_plankerton.png" />
    <img id="zone_stormshield_canny_valley" class="drag" src="/public/images/mission_alerts/stormshield_canny_valley.png" />
    <img id="zone_stormshield_twine_peaks" class="drag" src="/public/images/mission_alerts/stormshield_twine_peaks.png" />
    <img id="zone_industrial" class="drag" src="/public/images/mission_alerts/industrial.png" />
    <img id="zone_forest" class="drag" src="/public/images/mission_alerts/forest.png" />
    <img id="zone_city" class="drag" src="/public/images/mission_alerts/city.png" />
    <img id="zone_suburbs" class="drag" src="/public/images/mission_alerts/suburbs.png" />
    <img id="zone_grasslands" class="drag" src="/public/images/mission_alerts/grasslands.png" />
  </div>


  <script src="/public/js/libs/mission_alerts.js"></script>
</body>
</html>