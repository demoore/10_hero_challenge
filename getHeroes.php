<?php
// Grab the user's 64 bit steam id 
$steam_id = $_GET["steam_id"];
//convert their steam id into a 32 bit steam id 
$player_account_id = convert_id($steam_id);
//grab the players last 25 matches and decode it for parsing
$json_player = file_get_contents('https://api.steampowered.com/IDOTA2Match_570/GetMatchHistory/V001/?key=CD44403C3CEDB535EFCEFC7E64F487C6&account_id='.$steam_id);
$json_decoded_player = (json_decode($json_player, true));

/*
*Function used to convert the user's 64 bit ID to a 32 bit ID or vice versa
*
* $id - the user's 32 or 64 bit id to be changed
*/
function convert_id($id){
    if (strlen($id) === 17){
        $converted = substr($id, 3) - 61197960265728;
    }else{
        $converted = '765'.($id + 61197960265728);
    }
    return (string) $converted;
}

/*
*function to get the hero names of their most recent 100 dota2 matches
*
*$player_json - The parsable json for the last 25 matches of the user
*$account_id_32 - the 32 bit account id of the user, used for finding which player the hero is in a game
*/
function get_player_info($player_json, $account_id_32){
    $json_heroes = file_get_contents('https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key=CD44403C3CEDB535EFCEFC7E64F487C6&language=en_us');
    $json_decoded_heroes = (json_decode($json_heroes, true));

    foreach($player_json['result']['matches'] as $matches){
        foreach($matches['players'] as $players){
            if($players['account_id'] == $account_id_32){
                $hero_id = $players['hero_id'];
                $hero_name = get_hero($json_decoded_heroes, $hero_id);
                echo $hero_name."<br>";
            }
        }
    }
}

//function to get hero's name based off of ID
function get_hero($heroes,$hero_id){
    foreach($heroes['result']['heroes'] as $hero){
        if($hero['id'] == $hero_id){
            return $hero['localized_name'];
        }
    }
    return "could not find player name";
}

function get_10_heroes(){
    $json_heroes = file_get_contents('https://api.steampowered.com/IEconDOTA2_570/GetHeroes/v0001/?key=CD44403C3CEDB535EFCEFC7E64F487C6&language=en_us');
    $json_decoded_heroes = (json_decode($json_heroes, true));
    $current_10_heroes = array(10);

    for($i=0;$i<10;$i++){
       $hero_id = rand(1,110);
       $current_10_heroes[$i] = get_hero($json_decoded_heroes, $hero_id);
    }
    foreach($current_10_heroes as $hero){
        $hero_no_space =  str_replace(" ", "_", $hero); ;
        echo '<img src="img/heroes/'.$hero_no_space.'.png" alt="'.$hero.'" >';
        echo "<br />";
    }
}

?>

<html>
    <head>
    </head>
    <body>
        <?php 
        echo "<h1> Your last played heroes! </h1>";
        get_player_info($json_decoded_player, $player_account_id);
        echo "<h1> Your 10 heroes! </h1>";
        get_10_heroes();
        ?>
    </body>
</html>
