<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

// require __DIR__.'/../config/db.php';


$app = AppFactory::create();
// Add Routing Middleware

$app->get('/organizations/all', function (Request $request, Response $response){
    $sql = "SELECT * FROM orgranization";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $organization = $stmt->fetchAll(PDO::FETCH_OBJ);

        $db = null;
        $response->getBody()->write(json_encode($organization));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(200);



    }catch(PDOException $e){
        $error = array(
            "message"=>$e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->get('/organizations/to-close', function (Request $request, Response $response){
    $sql = "SELECT * FROM orgranization as o LEFT JOIN schedule as s ON o.id = s.organization_id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $organization = $stmt->fetchAll(PDO::FETCH_OBJ);

        $db = null;
        // $response->getBody()->write(json_encode($organization));

        $array_encoded = json_encode($organization);
        $array = json_decode($array_encoded, true);

        $now_day = date('d');
        // var_dump($now_day);
        if($now_day === '01'){
            $now_day = 'Duisenbi';
            // var_dump($now_day);
        }
        if($now_day === '02'){
            $now_day = 'Seisenbi';
            // var_dump($now_day);
        }
        if($now_day === '03'){
            $now_day = 'Sarsenbi';
            // var_dump($now_day);
        }
        if($now_day === '04'){
            $now_day = 'Beisenbi';
            // var_dump($now_day);
        }
        if($now_day === '05'){
            $now_day = 'Juma';
            // var_dump($now_day);
        }
        if($now_day === '06'){
            $now_day = 'Senbi';
            // var_dump($now_day);
        }
        if($now_day === '07'){
            $now_day = 'Jeksenbi';
            // var_dump($now_day);
        }

        $now_hours = date('H');
        $now_minutes = date('i');
        // var_dump($now_minutes);
        $new_array = array_filter($array, function ($var) use ($now_day, $now_hours, $now_minutes) {
            // var_dump($var['day_of_week']);
            if($var['day_of_week'] === $now_day && $now_hours > $var['open'] && $now_hours < $var['close']){
            
                $diff_hours = $var['close'] - $now_hours;
                $diff_minutes = 60 - $now_minutes; // decimal 1 hour counting
                if($diff_minutes != 0){
                    $whole_hours_m = $diff_hours * 60;
                    $counted_whole_diff_minutes = $whole_hours_m - $now_minutes;
                    $counted_diff_hours = $counted_whole_diff_minutes/60;
                    $diff_hours = floor($counted_diff_hours);
                    $diff_minutes = ($counted_diff_hours - $diff_hours)*60; 
                }
                if($diff_hours < 1){

                } 

                echo $var['name'].' closed after: '.$diff_hours." hours : ".$diff_minutes.' minutes';
            // return ;
            }
                        
        });

        $response->getBody()->write(json_encode($new_array));

        // var_dump($response);
        // return $response
        // ->withHeader('content-type', 'application/json')
        // ->withStatus(200);



    }catch(PDOException $e){
        $error = array(
            "message"=>$e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->get('/organizations/open-after', function (Request $request, Response $response){
    $sql = "SELECT * FROM orgranization as o LEFT JOIN schedule as s ON o.id = s.organization_id";

    try {
        $db = new DB();
        $conn = $db->connect();

        $stmt = $conn->query($sql);
        $organization = $stmt->fetchAll(PDO::FETCH_OBJ);

        $db = null;
        // $response->getBody()->write(json_encode($organization));

        $array_encoded = json_encode($organization);
        $array = json_decode($array_encoded, true);

        $now_day = date('d');
        // var_dump($now_day);
        if($now_day === '01'){
            $now_day = 'Duisenbi';
            // var_dump($now_day);
        }
        if($now_day === '02'){
            $now_day = 'Seisenbi';
            // var_dump($now_day);
        }
        if($now_day === '03'){
            $now_day = 'Sarsenbi';
            // var_dump($now_day);
        }
        if($now_day === '04'){
            $now_day = 'Beisenbi';
            // var_dump($now_day);
        }
        if($now_day === '05'){
            $now_day = 'Juma';
            // var_dump($now_day);
        }
        if($now_day === '06'){
            $now_day = 'Senbi';
            // var_dump($now_day);
        }
        if($now_day === '07'){
            $now_day = 'Jeksenbi';
            // var_dump($now_day);
        }

        $now_hours = date('H');
        $now_minutes = date('i');
        // var_dump($now_minutes);
        $new_array = array_filter($array, function ($var) use ($now_day, $now_hours, $now_minutes) {
            // var_dump($var['day_of_week']);
            if($var['day_of_week'] === $now_day && $var['close'] < $now_hours && $now_hours < 24){
                $from_before_morning = $var['open'];
                $hours_from_night_decim = 0;
                $minutes_from_night_rem = 0;
                
                $hours_from_night = 22 - $now_hours;// count till 24:00
                $minutes_from_night = 60 - $now_minutes;
                
                if($minutes_from_night != 0){
                    $whole_hours_from_night = $hours_from_night * 60;
                    $counted_whole_diff_minutes_f_n = $whole_hours_from_night - $now_minutes;
                    $counted_diff_hours_f_n = $counted_whole_diff_minutes_f_n/60;
                    $hours_from_night_decim = floor($counted_diff_hours_f_n);
                    $minutes_from_night_rem = ($counted_diff_hours_f_n - $hours_from_night_decim)*60; 
                    // var_dump($hours_from_night_decim."h, ".$minutes_from_night_rem."m");
                }

                $til_open_hours = $hours_from_night_decim + $from_before_morning;
                $til_open_minutes = $minutes_from_night_rem;

                echo $var['name'].' open after: '.$til_open_hours." hours : ".$til_open_minutes.' minutes'."<br>";
            
            
            }    
            
        });

        $response->getBody()->write(json_encode($new_array));

        // var_dump($response);
        // return $response
        // ->withHeader('content-type', 'application/json')
        // ->withStatus(200);



    }catch(PDOException $e){
        $error = array(
            "message"=>$e->getMessage()
        );

        $response->getBody()->write(json_encode($error));
        return $response
        ->withHeader('content-type', 'application/json')
        ->withStatus(500);
    }
});

$app->get('/tree/task', function (Request $request, Response $response){
    $array = [[1, 1, "Comment 1"],
              [2, 1, "Comment 2"],
              [3, 2, "Comment 3"],
              [4, 1, "Comment 4"],
              [5, 2, "Comment 5"],
              [6, 3, "Comment 6"],
              [7, 7, "Comment 7"]];

    $first_parent = $array[0][0];
    // echo $array[0][2]."<br>";
    $changed_index_value = $first_parent;
    $child_value = $first_parent;
    for($i=0; $i < count($array); $i++){
        for($j=0; $j < count($array); $j++){
            // var_dump($array[$i][$j]);
            echo 'i:'.$i, ' -- j:'.$j;
            if(($i == 0 && $j == 0) || $array[$i][0] == $array[$j][1] && $i==$j){
                continue;
            }
            if($array[$i][0] == $array[$j][1]){
                $changed_index_value++;

                if($changed_index_value == $array[$j][0]){
             
                    echo ' counter value: ('.$changed_index_value.");  ";

                    echo 'i:'.$i, ' ---- j:'.$j;

                    // var_dump($i);
                    // echo $array[$i][2];
                    $space = str_repeat("&nbsp;", 4);
                    $multiplied_space = str_repeat($space, $changed_index_value);
                    // var_dump($multiplied_space);
                    echo $multiplied_space.$array[$i][2]."<br>";

                    // echo 'changed index value: '.$changed_index_value."; child_value: ".$child_value."<br>";
                }
                if($changed_index_value < $array[$j][0]){
                    echo ' first element is greater: '.$array[$j][0]. '; ';
                }
            }
            // if($child_value > $array[$j][1]){
            //     $j++;
            // }

            // echo $a[2].'<br>';

        }
    }

   
});


?>