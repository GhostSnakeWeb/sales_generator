<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $c = true;

    /**
     * Валидация данных из POST
     * @param $data
     * @return string
     */
    function checkInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    /**
     * Валидация телефона
     * @param $phone
     * @return bool
     */
    function validateRussianPhoneNumber($phone)
    {
        $phone = trim((string)$phone);
        if (!$phone) return false;
        $phone = preg_replace('#[^0-9+]+#uis', '', $phone);
        if (!preg_match('#^(?:\\+?7|8|)(.*?)$#uis', $phone, $m)) return false;
        $phone = '+7' . preg_replace('#[^0-9]+#uis', '', $m[1]);
        if (!preg_match('#^\\+7[0-9]{10}$#uis', $phone, $m)) return false;
        return true;
    }

    // Массив ошибок
    $errors = [];

    // Сохраняем базовые параметры формы
    $projectName = checkInput($_POST["project_name"]);
    $adminEmail = checkInput($_POST["admin_email"]);
    $formSubject = checkInput($_POST["form_subject"]);
    $formFrom = checkInput($_POST["form_from"]);

    // Сохраняем оставшиеся параметры формы
    if (empty($_POST["email"])) {
        $email = "";
    } else {
        $email = checkInput($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            array_push($errors, "Некорректно введён email");
        }
    }

    if (empty($_POST["phone"])) {
        array_push($errors, "Введите телефон");
    } else {
        if (validateRussianPhoneNumber($_POST["phone"]))
            $phone = checkInput($_POST["phone"]);
        else
            array_push($errors, "Некорректно введён телефон");
    }

    if (!empty($errors)) {
        http_response_code(400);
        exit(json_encode($errors, JSON_UNESCAPED_UNICODE));
    }

    // Сообщение для отправки
    $message = "
        <tr style='background-color: #f8f8f8;'>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Телефон</b></td>
            <td style='padding: 10px; border: #e9e9e9 1px solid;'>$phone</td>
        </tr>
    ";
    if (!empty($email)) {
        $message .= "
            <tr>
                <td style='padding: 10px; border: #e9e9e9 1px solid;'><b>Email</b></td>
                <td style='padding: 10px; border: #e9e9e9 1px solid;'>$email</td>
            </tr>
        ";
    }
    $message = "<table style='width: 100%;'>$message</table>";

    // Адаптирование заголовка
    function adopt($text)
    {
        return '=?UTF-8?B?' . base64_encode($text) . '?=';
    }

    $headers = "MIME-Version: 1.0" . PHP_EOL .
        "Content-Type: text/html; charset=utf-8" . PHP_EOL .
        'From: ' . adopt($projectName) . ' <' . $formFrom . '>' . PHP_EOL .
        'Reply-To: ' . $formFrom . '' . PHP_EOL;

    // Sending email to admin
    mail($adminEmail, adopt($formSubject), $message, $headers);
}