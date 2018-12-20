<?php
ini_set('max_execution_time', 300);

use CV\CascadeClassifier;
use CV\Face\FacemarkLBF;
use CV\Face\LBPHFaceRecognizer;
use CV\Scalar;
use CV\Size;
use function CV\{bilateralFilter,
    blur,
    circle,
    cvtColor,
    equalizeHist,
    imread,
    imwrite,
    medianBlur,
    rectangleByRect,
    resize};
use const CV\{COLOR_BGR2GRAY, CV_HAAR_DO_ROUGH_SEARCH};


//trainFolder();
//Folderi
$ulaz = 'slike/';
$izlaz = 'tmp/';
$anotacija = 'anotacija/';

$input = filter_input(INPUT_POST, 'slika');

$mod = filter_input(INPUT_POST, 'model');

$trainModel = filter_input(INPUT_POST, 'train_model');

$train = filter_input(INPUT_POST, 'train', FILTER_VALIDATE_INT);

$file = array_diff(scandir('train'), array('..', '.'));


$src = imread($ulaz . $input);

//detekcija lica pomocu kaskadnog klasifikatora
$faceClassifier = new CascadeClassifier();
$faceClassifier->load('modeli/lbpcascades/' . $mod);


//LBPHFaceRecognizer

if ($trainModel == true):
    $faceRecognizer = LBPHFaceRecognizer::create();
endif;

//konvertovanje slike u sivu
$gray = cvtColor($src, COLOR_BGR2GRAY);


//Histogram
equalizeHist($gray, $gray);


//detektovanje lica
$faceClassifier->detectMultiScale($gray, $faces, 1.1, 3, CV_HAAR_DO_ROUGH_SEARCH, new Size(50, 50));

$faceImages = $faceLabels = [];


if ($faces) {


    $facemark = FacemarkLBF::create();
    $facemark->loadModel('modeli/opencv-facemark-lbf/lbfmodel.yaml');


    $facemark->fit($src, $faces, $landmarks);

    if ($landmarks) {
        $scalar = new Scalar(0, 0, 255);
        foreach ($landmarks as $face) {
            foreach ($face as $k => $point) {//var_export($point);
                circle($src, $point, 2, $scalar, 2);
            }
        }
    }


    //Treniranje objekta
    $MyFace = null;
    if ($trainModel == true):


        foreach ($faces as $k => $face) {
            $faceImages[] = $gray->getImageROI($face); // face coordinates to image
            $faceLabels[] = $train;

        }


        $faceRecognizer->read("trenirani_model" . DIRECTORY_SEPARATOR . "train.yml");
        $faceRecognizer->update($faceImages, $faceLabels);
        $faceRecognizer->write("trenirani_model" . DIRECTORY_SEPARATOR . "train.yml");
    endif;

    $facett = null;

    foreach ($faces as $k => $face):
        $facett = $gray->getImageROI($face); // face coordinates to image

    endforeach;


    $scalar = new Scalar(0, 0, 255);

    //anotacije na detektovanom licu
    foreach ($faces as $face) {

        $data['x'] .= 'x:' . $face->x . ';';
        $data['y'] .= 'y:' . $face->y . ';';
        $data['width'] .= 'width:' . $face->width . ';';
        $data['height'] .= 'height:' . $face->height . ';' . PHP_EOL;

        file_put_contents($anotacija . strtok($input, '.') . '.txt', $data);

        rectangleByRect($src, $face, $scalar, 2);
    }


}

if (file_exists($izlaz . $input)):
    unlink($izlaz . $input);
endif;

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
        <span class="text-muted">&copy; 2018 Begić Fuad, Ahetmović Selma</span>
    </div>
</footer>


</body>
</html>