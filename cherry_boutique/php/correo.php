<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoLogin($destino, $nombre)
{
    require_once __DIR__ . '/config_correo.php';

    $asunto = 'Inicio de sesión en Cherry Boutique';
    $fecha = date('d/m/Y H:i');
    $html = "<div style='font-family:Arial, sans-serif; max-width:480px; margin:auto; background:#FFEBF5; padding:24px; border-radius:12px'>
        <h2 style='color:#F113A3; margin:0'>Cherry <small style='color:#3A1C1C'>BOUTIQUE</small></h2>
        <p>Hola <b>" . htmlspecialchars($nombre) . "</b>,</p>
        <p>Detectamos un inicio de sesion en tu cuenta el <b>$fecha</b>.</p>
        <p>Si fuiste tú, ¡disfruta tu compra! Si no, cambia tu contraseña.</p>
        <p style='color:#F113A3'>Tu estilo, tu esencia, tu Cherry.</p></div>";

    $ruta = __DIR__ . '/PHPMailer/src/PHPMailer.php';
    if (file_exists($ruta)) {
        require_once $ruta;
        require_once __DIR__ . '/PHPMailer/src/SMTP.php';
        require_once __DIR__ . '/PHPMailer/src/Exception.php';
        try {
            $m = new PHPMailer(true);
            $m->isSMTP();
            $m->Host = MAIL_HOST;
            $m->SMTPAuth = true;
            $m->Username = MAIL_USUARIO;
            $m->Password = MAIL_PASSWORD;
            $m->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $m->Port = MAIL_PUERTO;
            $m->CharSet = 'UTF-8';
            $m->SMTPOptions = [
                'ssl' => [
                    'verify_peer' => false,
                    'verify_peer_name' => false,
                    'allow_self_signed' => true,
                ]
            ];
            $m->SetFrom(MAIL_USUARIO, MAIL_NOMBRE);
            $m->addAddress($destino, $nombre);
            $m->isSMTP(true);
            $m->Subject = $asunto;
            $m->Body = $html;
            $m->Send();
            return true;
        } catch (Exception $ex) {
            return false;
        }
    }
    $cab = "MIME-Version: 1.0\r\nContent-type: text/html; charset=UTF-8\r\nFrom: " . MAIL_NOMBRE . " <" . MAIL_USUARIO . ">\r\n";
    return @mail($destino, $asunto, $html, $cab);
}