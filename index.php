<?php
    $homeWorkNum = '1.4';
    $homeWorkCaption = 'Стандартные функции';
    $arrRandomCity = array('Moscow', 'London', 'Washington', 'Berlin', 'Paris', 'Tokyo');
    $appID = '1abefcc474699cf49d2cd84c50bb0cb0';
    $city = null;
    $title = '';
    $iconTitle = '';
    $temperature = '';
    $pressure = '';
    $humidity = '';
    $windSpeed = '';
    $iconHint = '';
    $iconPath = '';
    $date = '';

    /*Значение города если нажата кнопка "Отправить"*/
    if (!empty($_GET['send-button'])) {
        if (!empty($_GET['input_value'])) {
            $city = $_GET['input_value'];
        }
    }

    /*Значение города если нажата кнопка "Случайный город"*/
    if (!empty($_GET['random-button'])) {
        $city = $arrRandomCity[rand(0, count($arrRandomCity) - 1)];

    }

    if (!is_null($city)) {
        $inputHint = 'Погода в городе: ' . $city . '.';
        $filenameAPI = 'http://api.openweathermap.org/data/2.5/weather?q=' . $city . '&APPID=' . $appID;
        $filenameCache = 'cache.tmp';
        $useCache = false;

        /*Проверяем нужные ли данные лежат в кеше*/
        if (is_file($filenameCache)) {
            $content = file_get_contents($filenameCache);
            $content = json_decode(file_get_contents($filenameCache), true);
            if ($content['cod'] == 200) {
                if (($content['name'] == $city) and (time() - $content['dt']) < (60 * 60)) {
                    $useCache = true;
                }
            }
        }

        /*Если в кеше неподходящие данные или при отсутствии кеша - получаем актуальные через АПИ и пишем их в кеш*/
        if ($useCache == false) {
            $content = file_get_contents($filenameAPI);
            if (!($content === false)) {
                $content = json_decode($content, true);
                switch ($content['cod']) {
                    case 200:
                        file_put_contents($filenameCache, json_encode($content));
                        $useCache = true;
                        break;
                    case 404:
                        $title = 'Город не найден';
                        break;
                    default:
                        if (is_numeric($content['cod'])) {
                            $title = 'Ошибка, код: ' . $content['cod'] . ', пояснение: ' . $content['message'];
                        } else {
                            $title = 'Неизвестная ошибка';
                        }
                }
            } else {
                $title = 'Во время запроса произошла ошибка, возможно город не найден';
            }
        }
        /*берем данные из кеша (на этом этапе мы уже проверили, что в кеше лежат нужные данные)*/
        if ($useCache == true) {
            $content = json_decode(file_get_contents($filenameCache), true);
            /*echo '<pre>'; print_r($content); echo '</pre>';*/
            $title = 'Погода в городе ' . $city . '.';
            $iconTitle = 'Состояние погоды: ' . $content['weather'][0]['main'] . ',';
            $temperature = 'температура воздуха составляет ' . tempC($content['main']['temp']) . ' °C (min: ' . tempC($content['main']['temp_min']) .
                ' °C - max: ' . tempC($content['main']['temp_max']) . ' °C),';
            $pressure = 'давление: ' . round($content['main']['pressure'] * 0.75006375541921) . ' мм ртутного столба,';
            $humidity = 'влажность воздуха ' . $content['main']['humidity'] . ' %,';
            $windSpeed = 'скорость ветра ' . $content['wind']['speed'] . ' м/с.';
            $iconHint = $content['weather'][0]['description'];
            $iconPath = 'http://openweathermap.org/img/w/' . $content['weather'][0]['icon'] . '.png';

            $date = 'Прогноз погоды актуален по состоянию на ' . date('H:i d.m.Y', $content['dt']);
        }

    }

    function tempC($tempK)
    {
        // преобразование температуры из K в C
        if (is_numeric($tempK)) {
            return $tempK - 273.15;
        }
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
    <img alt="<?= $iconHint ?>" src="<?= $iconPath ?>" style="float:left;">
    <p><?= $iconTitle ?> <?= $temperature ?> <?= $pressure ?> <?= $humidity ?> <?= $windSpeed ?></p>
    <p><?= $date ?></p>
  </body>
</html>