<?php
$page = "contact";
$method = $_POST;
$success = false;
$e = "";

require '../vendor/autoload.php';

use Dotenv\Dotenv;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
$dotenv = Dotenv::createImmutable('../');
$dotenv->load();

$sender = $_ENV['MAIL_LOGIN'];
$phpMail = new PHPMailer(true);
$prenom = "";
$nom = "";
$tel = "";
$mail = "";
$sujet = "";
$text = "";

if (isset($method["prenom"]) && isset($method["nom"]) && isset($method["mail"]) && isset($method["sujet"]) && isset($method["text"])) {
    $prenom = $method["prenom"];
    $nom = $method["nom"];
    $mail = $method["mail"];
    $sujet = $method["sujet"];
    $text = $method["text"];
    if(isset($method["tel"])) {
        $tel = $method["tel"];
    }

    if (!empty($prenom) && !empty($nom) && !empty($mail) && !empty($sujet) && !empty($text)) {
        try {
            $prenom = ucwords($prenom);
            $nom = strtoupper($nom);

            $phpMail->SMTPDebug = SMTP::DEBUG_OFF;
            $phpMail->isSMTP();
            $phpMail->Host = $_ENV['MAIL_DSN'];
            $phpMail->SMTPAuth = true;
            $phpMail->Username = $sender;
            $phpMail->Password = $_ENV['MAIL_PSSWD'];
            $phpMail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $phpMail->Port = 587;
            $phpMail->CharSet = PHPMailer::CHARSET_UTF8;

            $phpMail->setFrom($sender, $prenom . " " . $nom);
            $phpMail->AddAddress($sender, "Self");
            $phpMail->isHTML(true);
            $phpMail->Subject = $sujet;

            $body = "<b>De : </b>" . $prenom . " " . $nom;
            if ($tel != "") $body = $body . "<br><b>Numéro de téléphone : </b>" . $tel;
            $body = $body . "<br><b>Adresse mail : </b>" . $mail . "<br><b>Texte : </b><br>" . $text;

            $phpMail->Body = $body;
            if ($phpMail->send()) {
                $success = true;
            }
        } catch (Exception $err) {
            $e = $err;
        }
    } else {
        $e = "Un ou plusieurs paramètres requis sont vides";
    }
} else {
    $e = "Un ou plusieurs paramètres requis sont manquants";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <title>Mail</title>
    <?php include("../includes/head.php") ?>
</head>
<body class="bg-dark text-white">
<?php include("../includes/navbar.php") ?>
<main class="container">
    <div class="row">
        <div class="col-12">
            <?php
            if ($success) {
                echo "<h1 class='border-bottom pb-1 mb-3 text-center'>Message envoyé!</h1>";
                echo "<h3 class='pb-1 mb-1 text-center'>Récapitulatif</h3>";
                echo "<h4>Vous êtes :</h4>" . $prenom . " " . $nom;
                echo "<h4>Votre adresse mail :</h4><em>" . $mail . "</em>";
                if ($tel != "") {
                    echo "<h4>Votre numéro de téléphone :</h4>" . $tel;
                }
                echo "<h4>Sujet de votre mail :</h4>" . $sujet;
                echo "<h4 class='pb-1 mb-1'>Contenu de votre mail :</h4><pre class='text-white mail-text'>" . $text . "</pre>";
            } else {
                echo "<h1 class='border-bottom pb-1 mb-3 text-center'>Erreur!</h1>";
                if (empty($e)) {
                    echo "Impossible d'envoyer le mail, veuillez réessayer plus tard..";
                } else {
                    echo $e;
                }
            }
            ?>
        </div>
    </div>
</main>
<?php include("../includes/bodyEnd.php") ?>
</body>
</html>