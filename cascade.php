<?php

use CV\CascadeClassifier;
use CV\Face\LBPHFaceRecognizer;
use CV\Scalar;
use function CV\{cvtColor, equalizeHist, imread, imwrite, rectangleByRect};
use const CV\{COLOR_BGR2GRAY};

$ulaz = 'slike/';
$izlaz = 'rezultati/';


$input = filter_input(INPUT_POST, 'slika');
$mod = filter_input(INPUT_POST, 'model');
$src = imread($ulaz . $input);


// modeli lbpcascade_frontalface
$faceClassifier = new CascadeClassifier();
$faceClassifier->load('modeli/lbpcascades/' . $mod);

// LBPHFaceRecognizer
$faceRecognizer = LBPHFaceRecognizer::create();
//equalizeHist($gray, $gray);

$gray = cvtColor($src, COLOR_BGR2GRAY);

$faceClassifier->detectMultiScale($gray, $faces);


$faceImages = $faceLabels = [];
if ($faces) {


    foreach ($faces as $k => $face) {
        $faceImages[] = $gray->getImageROI($face); // face coordinates to image
        $faceLabels[] = 9;


    }
    $faceRecognizer->read("trenirani_model".DIRECTORY_SEPARATOR."train.yml");
    $faceRecognizer->update($faceImages, $faceLabels);

   // $faceRecognizer->write("trenirani_model".DIRECTORY_SEPARATOR."train.yml");


    $scalar = new Scalar(0, 0, 255); //blue

    foreach ($faces as $face) {
        rectangleByRect($src, $face, $scalar, 3);
    }
}


imwrite($izlaz . $input, $src);


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
            <h5>Model: <?= $mod ?></h5>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <h4>Ulaz</h4>
            <img src="<?= $ulaz . $input ?>" class="img-fluid img-thumbnail" alt="Ulaz">
        </div>
        <div class="col">
            <h4>Izlaz</h4>
            <img src="<?= $izlaz . $input ?>" class="img-fluid img-thumbnail" alt="Izlaz">
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
