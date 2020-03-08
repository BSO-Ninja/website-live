<?php

  $text = file("../temp/Server Log.txt");

  foreach($text AS $row) {

    if(strstr($row, "You lose")) {
      $next = str_replace(" You lose ","|", $row);
      $next = str_replace(" hitpoints due to an attack by ","|", $next);
      $next = str_replace(strstr($next, "."),"", $next);
      $tmp = explode("|",$next);

      $dmg[$tmp[2]][] = $tmp[1];
    }

  }

  // EACH MONSTER
  foreach($dmg AS $monster => $hits) {
    asort($hits);
    $count_hits = count($hits);

    $a = array_filter($hits);
    $average = array_sum($hits)/count($hits);

    $total = array_sum($hits);

    echo "<h2>". $monster ."</h2> Total Hits: ". $count_hits ." / Avarage Dmg: ". intval($average) ." / Total Damage: ". $total ."<br />";
    print_r($hits);
  }