<div class="box clearfix" style="width:55%;float:left;">
  <div class="content clearfix flex">
    <div class="box-brown" style="font-size:11px;width:100%;">

      <h3>Loot Calculators</h3>



          <div class="input-group mb-1" >
            <div class="d-inline p-2">
              <input type="text" style="width:260px;" class="form-control s2_players" placeholder="Knight">
            </div>
            <div class="d-inline p-2">
              <input type="text" style="width:100px;" class="form-control s2_balance" placeholder="Balance">
            </div>
          </div>

          <div class="input-group mb-1" >
            <div class="d-inline p-2">
              <input type="text" style="width:260px;" class="form-control s2_players" placeholder="Druid">
            </div>
            <div class="d-inline p-2">
              <input type="text" style="width:100px;" class="form-control s2_balance" placeholder="Balance">
            </div>
          </div>

          <div class="input-group mb-1" >
            <div class="d-inline p-2">
              <input type="text" style="width:260px;" class="form-control s2_players" placeholder="Sorcerer">
            </div>
            <div class="d-inline p-2">
              <input type="text" style="width:100px;" class="form-control s2_balance" placeholder="Balance">
            </div>
          </div>

          <div class="input-group mb-1" >
            <div class="d-inline p-2">
              <input type="text" style="width:260px;" class="form-control s2_players" placeholder="Paladin">
            </div>
            <div class="d-inline p-2">
              <input type="text" style="width:100px;" class="form-control s2_balance" placeholder="Balance">
            </div>
          </div>

          <hr />

        <div class="input-group mb-1" >
          <div class="d-inline p-2">
            <input type="text" style="width:260px;" class="form-control" placeholder="Add Player">
          </div>
          <div class="d-inline p-2">
            <button class="btn button-big button-green" type="button">Add Player</button>
          </div>
        </div>



    </div>
  </div>
</div>

<div class="box clearfix" style="width:45%;float:right;">
  <div class="content clearfix flex">
    <div class="half box-brown" style="font-size:11px;width:100%;">

      <div class="input-group mb-3" style="margin-bottom:0px !important;">
        <ul class="list-group mb-3" style="margin-bottom:0px !important;">
          <li class="list-group-item d-flex justify-content-between lh-condensed bg-light">
            <div>
              <h5 class="my-0">Total Balance</h5>
              <small class="text-muted">Total balance of all players</small>
            </div>
            <span class="text-success"><h6 class="my-0 totalBalance" style="font-weight:bold;">+12.235.744</h6></span>
          </li>
          <li class="list-group-item d-flex justify-content-between bg-light">
            <div style="font-weight:normal;font-size:12px;">
              <h5 class="my-0" style="margin-bottom:15px !important;">Share Information</h5>
              <span class="my-0">Knight give Paladin 343.654 gp</span><br />
              <span class="my-0">Knight give Sorcerer 120.453 gp</span><br />
              <span class="my-0">Paladin give Druid 340.876 gp</span>
            </div>
          </li>
          <li class="list-group-item d-flex justify-content-between" style="border: 0px !important;">
            <div style="font-weight:normal;font-size:12px;">
              <span class="my-0" style="width:100%;text-align:right;">
                <button class="btn button-big button-green" type="button">Copy share info</button>
                <button class="btn button-big button-blue" type="button">Reset calculator</button>
              </span>
            </div>
          </li>
        </ul>


      </div>
    </div>
  </div>
</div>

<script>

  $(document).ready(function(){

    $('.s2_balance').keyup(function(){
      calculate();
    });
    $('.s2_players').keyup(function(){
      calculate();
    });

    function calculate() {
      var players = [];
      var totalBalance = 0;

      // Get all balances
      $('.s2_balance').each(function( index ){
        var playerBalance = { "balance" : parseInt(this.value) };
        players.push(playerBalance);
        // Update total balance
        if(parseInt(this.value)) { totalBalance = totalBalance + parseInt(this.value) }
      });

      // Get all names
      $('.s2_players').each(function( index ){
        if(this.value === "") {
          players[index].name = $(this).attr('placeholder');
        }
        else {
          players[index].name = this.value;
        }
      });

      if(totalBalance > 0) {
        $('.totalBalance').text("+"+totalBalance.toLocaleString("de-DE")).removeClass("minusBalance");
      }
      else {
        $('.totalBalance').text(totalBalance.toLocaleString("de-DE")).addClass("minusBalance");
      }

      console.log("Total Balance: "+ totalBalance.toLocaleString("de-DE"));
      console.log(players);
    }

  });

</script>