<?php
/*api.openweathermap.org/data/2.5/weather?q=*/

    $homeWorkNum = '1.4';
    $homeWorkCaption = 'Стандартные функции';
    $arrRandomCity = array('Moscow','London','Washington','Berlin','Paris');
    $appID='1abefcc474699cf49d2cd84c50bb0cb0';
    $city=null;

    if (!empty($_GET['send-button'])) {
        if (!empty($_GET['input_value'])) {
            $city = $_GET['input_value'];
        }
    }

    if (!empty($_GET['random-button'])) {
        $city = $arrRandomCity[rand(0, count($arrRandomCity)-1)];
    }

    if (!is_null($city)) {
        $inputHint = 'Погода в городе: ' . $city . '.';
        $filenameAPI='http://api.openweathermap.org/data/2.5/weather?q='.$city.'&APPID='.$appID;

        $filenameCache='cache.tmp';
        $useCache = false;

        if (is_file($filenameCache)) {
            /*echo '<pre>';*/
            $content=json_decode(file_get_contents($filenameCache),true);
            if ($content['cod']==200) {
              if (($content['name']==$city) and ((time() - $content['dt']) < (60*60))) {
                  $useCache = true;
                  echo 'Используем кеш (до АПИ)';
                  /*print_r($content) ;*/
                }
            }
        }

        if ($useCache == false) {
            $content=json_decode(file_get_contents($filenameAPI),true);
            echo $filenameAPI;
            switch ($content['cod']) {
                case 200:
                    file_put_contents($filenameCache, json_encode($content));
                    $useCache = true;
                    echo $filenameAPI;
                    echo 'Используем кеш (обновили АПИ)';
                    break;
                case 401:
                    $title='Город не найден';
                    break;
                default:
                    $title = 'Ошибка, код: '.$content['cod'].', пояснение: '.$content['message'];
            }
        }

        if ($useCache = true) {
            $content=json_decode(file_get_contents($filenameCache),true);
            echo '<pre>'; print_r($content); echo '</pre>';
            $title = 'Погода в городе '.$city.'.';
            $temperature = 'Температура: '.($content['main']['temp']-273.15)." °C";
            $date = 'Прогноз погоды актуален по состоянию на '.date('H:i d.m.Y',$content['dt']);
            $iconTitle = $content['dt'];
            /*$icon =*/
        }

    } else {
        $title = '';
        $temperature = '';
    }

?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Домашнее задание по теме <?= $homeWorkNum ?> <?= $homeWorkCaption ?></title>
    <meta charset="utf-8">
    <style>
        body {
            font-family: sans-serif;
        }

        input {
            margin: 10px 0;
        }
    </style>
</head>

<body>
<h1>Здравствуйте!</h1>
<form action="" method="GET">
    <label>Введите название города на латинице: <input type="text" name="input_value" value="<?= $city ?>"></label>
    <div style="display: block;">
        <input type="submit" name="send-button" value="Отправить">
        <input type="submit" name="random-button" value="Случайный город">
    </div>
</form>

<h2><?= $title ?></h2>
<p><?= $temperature ?></p>
<img alt="clear sky" src="http://openweathermap.org/img/w/01n.png">
<p><?= $date ?></p>
</body>
</html>