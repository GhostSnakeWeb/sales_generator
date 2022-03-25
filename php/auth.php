<?php
$subdomain = 'sgamotest'; //Поддомен нужного аккаунта
$link = 'https://' . $subdomain . '.amocrm.ru/oauth2/access_token'; //Формируем URL для запроса

/** Соберем данные для запроса */
$data = [
    'client_id' => '5308fc60-c7a8-4f57-996b-5dea6dfdea77',
    'client_secret' => 'fDzIKBSJkprbYLCAZU3HD9MCFXdI8kgxmjo87lNRmhn79Tx0sGJBUHN3txCNtvXu',
    'grant_type' => 'authorization_code',
    'code' => 'def50200c7bec695be6fb921d3d2051f1665606425909577ae39e8d262ec8f29e14ed06c773b2c859c19f0d495bd849cf438d9cdf6e41e866437a590574f47c1fdb505a546e9b6c50a2e65f096e475edab4b649dcb58a29aa607c84e18642e76fb5439080d4f67f4d547e35bf9c8198a68be942fb1449209b971a7f8d95393ff704d4269775789314accbd651575b7015851f280fcc3e0d7ac3d04a82e2e2fb0a6e46330519844b71cea0e04840d6ccbeb34e93f94d050531733ccd573ba370a2809cd742cf1b305d7565bf76cf48d70ac0611cb48e3227b73fcb3a07d15612e49a12534ddad304f6cc95af04c2db749a13c1d44085a47d48a67320806075dde1e1c5c63499b33af902edef7b0c3c2b62fbfcb1adf1ec528a25624bc8cb98350957f3eeaa42f95a9161f1fdd45782160a52819ea510846413f242f192a62e9f6a42a28adab4e2f9cb5c73773bd151e7d58936bed6660a6380afedcecf3fac00665b332bd9f9a401b72b4754506aeb5a28a30ce6ea3f0dd72ef28f73eb2e99517c10171b223a2865d3952d730355627d9dc550f893e05ab6305ddf9dc62e1da59d95b54d55ab7dc60a89c14ffaf46a6f503214521f9d5083d542daf30ee',
    'redirect_uri' => 'http://crmtest.tk/',
];

/**
 * Нам необходимо инициировать запрос к серверу.
 * Воспользуемся библиотекой cURL (поставляется в составе PHP).
 * Вы также можете использовать и кроссплатформенную программу cURL, если вы не программируете на PHP.
 */
$curl = curl_init(); //Сохраняем дескриптор сеанса cURL
/** Устанавливаем необходимые опции для сеанса cURL  */
curl_setopt($curl,CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl,CURLOPT_USERAGENT,'amoCRM-oAuth-client/1.0');
curl_setopt($curl,CURLOPT_URL, $link);
curl_setopt($curl,CURLOPT_HTTPHEADER,['Content-Type:application/json']);
curl_setopt($curl,CURLOPT_HEADER, false);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER, 1);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST, 2);
$out = curl_exec($curl); //Инициируем запрос к API и сохраняем ответ в переменную
$code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
curl_close($curl);
/** Теперь мы можем обработать ответ, полученный от сервера. Это пример. Вы можете обработать данные своим способом. */
$code = (int)$code;
$errors = [
    400 => 'Bad request',
    401 => 'Unauthorized',
    403 => 'Forbidden',
    404 => 'Not found',
    500 => 'Internal server error',
    502 => 'Bad gateway',
    503 => 'Service unavailable',
];

try
{
    /** Если код ответа не успешный - возвращаем сообщение об ошибке  */
    if ($code < 200 || $code > 204) {
        throw new Exception(isset($errors[$code]) ? $errors[$code] : 'Undefined error', $code);
    }
}
catch(Exception $e)
{
    die('Ошибка: ' . $e->getMessage() . PHP_EOL . 'Код ошибки: ' . $e->getCode());
}

/**
 * Данные получаем в формате JSON, поэтому, для получения читаемых данных,
 * нам придётся перевести ответ в формат, понятный PHP
 */
$response = json_decode($out, true);

$access_token = $response['access_token']; //Access токен
$refresh_token = $response['refresh_token']; //Refresh токен
$token_type = $response['token_type']; //Тип токена
$expires_in = $response['expires_in']; //Через сколько действие токена истекает

function saveDataInFile($data){
    $f = fopen('data.txt', 'w+');
    foreach ($data as $value)
        fwrite($f, $value."\n");
}

saveDataInFile($response);
var_dump($response);