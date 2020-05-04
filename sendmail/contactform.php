<?php
function ValidateEmail($email) {
    $pattern = '/^([0-9a-z]([-.\w]*[0-9a-z])*@(([0-9a-z])+([-\w]*[0-9a-z])*\.)+[a-z]{2,6})$/i';
    return preg_match($pattern, $email);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $mailto = 'gavrilyuk82@gmail.com';
    $mailfrom = isset($_POST['email']) ? $_POST['email'] : $mailto;
    $subject = 'Заявка на обратный звонок';
    $message = 'Заполнена форма обратной связи:';
    //$success_url = './form-ok.php';
    $error_url = '';
    $error = '';
    $eol = "\n";


    $boundary = md5(uniqid(time()));

    $header  = 'From: '.$mailfrom.$eol;
    $header .= 'Reply-To: '.$mailfrom.$eol;
    $header .= 'MIME-Version: 1.0'.$eol;
    $header .= 'Content-Type: multipart/mixed; boundary="'.$boundary.'"'.$eol;
    $header .= 'X-Mailer: PHP v'.phpversion().$eol;
    if (!ValidateEmail($mailfrom)) {
        $error .= "The specified email address is invalid!\n<br>";
    }

    if (!empty($error)) {
        $errorcode = file_get_contents($error_url);
        $replace = "##error##";
        $errorcode = str_replace($replace, $error, $errorcode);
        echo $errorcode;
        exit;
    }

    $internalfields = array ("submit", "reset", "send", "captcha_code");
    $message .= $eol;
    $message .= "IP Address : ";
    $message .= $_SERVER['REMOTE_ADDR'];
    $message .= $eol;
    foreach ($_POST as $key => $value)
    {
        if (!in_array(strtolower($key), $internalfields))
        {
            if (!is_array($value))
            {
                $message .= ucwords(str_replace("_", " ", $key)) . " : " . $value . $eol;
            }
            else
            {
                $message .= ucwords(str_replace("_", " ", $key)) . " : " . implode(",", $value) . $eol;
            }
        }
    }

    $body  = 'This is a multi-part message in MIME format.'.$eol.$eol;
    $body .= '--'.$boundary.$eol;
    $body .= 'Content-Type: text/plain; charset=ISO-8859-1'.$eol;
    $body .= 'Content-Transfer-Encoding: 8bit'.$eol;
    $body .= $eol.stripslashes($message).$eol;

    $body .= '--'.$boundary.'--'.$eol;
    mail($mailto, $subject, $body, $header);
    //header('Location: '.$success_url);
    echo 'success';
    exit;
}