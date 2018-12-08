<?php

use CV\CascadeClassifier;
use CV\Face\LBPHFaceRecognizer;
use CV\Scalar;
use CV\Point;
use function CV\{cvtColor, equalizeHist, imread, imwrite, putText, rectangle, rectangleByRect};
use const CV\{COLOR_BGR2GRAY};

//use Point;
$ulaz = 'slike/';
$izlaz = 'rezultati/';

$input = filter_input(INPUT_POST, 'slika');


$src = imread($ulaz . $input);

// modeli lbpcascade_frontalface
$faceClassifier = new CascadeClassifier();
$faceClassifier->load('modeli/lbpcascades/lbpcascade_frontalface_improved.xml');

$gray = cvtColor($src, COLOR_BGR2GRAY);
$faceClassifier->detectMultiScale($gray, $faces);

$faceRecognizer = LBPHFaceRecognizer::create();

$faceRecognizer->read("trenirani_model" . DIRECTORY_SEPARATOR . "train.yml");

$labels = [0=>'Nepoznato',8 => 'Fuad Begic','9'=> 'Mel Gibosn'];

//equalizeHist($gray, $gray);
foreach ($faces as $face) {
    $faceImage = $gray->getImageROI($face);

    $faceLabel = $faceRecognizer->predict($faceImage, $faceConfidence);




    $scalar = new \CV\Scalar(0, 0, 255);
    rectangleByRect($src, $face, $scalar, 2);
    $text = $labels[$faceLabel];
    rectangle($src, $face->x, $face->y, $face->x + ($faceLabel == 1 ? 50 : 130), $face->y - 30, new Scalar(255, 255, 255), -2);
    putText($src, "$text", new Point($face->x, $face->y - 2), 0, 1.5, new Scalar(), 2);
}
imwrite($izlaz . $input . '_det', $src);

?>

<!doctype html>
<html lang="bs-BA">
<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css"
          integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
    <link href="sticky-footer.css" rel="stylesheet">
    <title>POOS!</title>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-light bg-light">

    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav">
            <li class="nav-item active">
                <a class="nav-link" href="/">Naslovna <span class="sr-only">(current)</span></a>
            </li>
    </div>
</nav>

<!-- Begin page content -->
<main role="main" class="container">
    <h1 class="mt-5">POOS, Projekat Face detection&recognition</h1>
    <p class="lead">PHP 7.2, OpenCV 3.5</p>

    <?php


    ?>


</main>
<div class="container">

    <div class="row">
        <div class="col">
            <h4>Ulaz</h4>
            <h5><?= "Predict: {$faceLabel}, Conf:{$faceConfidence}\n";?></h5>
            <img src="<?= $izlaz . $input.'_det' ?>" class="img-fluid img-thumbnail" alt="Ulaz">
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container">
        <span class="text-muted">&copy; 2018 Begić Fuad, Ahmetović Selma</span>
    </div>
</footer>


</body>
</html>
