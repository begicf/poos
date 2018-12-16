<?php
ini_set('memory_limit', '1000');

use CV\CascadeClassifier;
use CV\Face\LBPHFaceRecognizer;
use CV\Point;
use CV\Scalar;
use CV\Size;
use function CV\{addWeighted,
    bilateralFilter,
    blur,
    cvtColor,
    dilate,
    equalizeHist,
    erode,
    GaussianBlur,
    getStructuringElement,
    imread,
    imwrite,
    medianBlur,
    rectangleByRect};
use const CV\{COLOR_BGR2GRAY, MORPH_CROSS, MORPH_ELLIPSE, MORPH_RECT};

//Flukcija za kontrast i osvijeteljenje
function contrastImage($ulaz, $contrastType, $input, $tmp)
{
    $imagick = new \Imagick(realpath($ulaz . $input));
    if ($contrastType != 2) {
        $imagick->brightnessContrastImage(2, 3, 1);
    }


    $imagick->writeImage($tmp . $input);
}

//Maskiranje naostrina
function maskSharp($src)
{
    $dst = null;
    for ($i = 1; $i < 6; $i = $i + 2) {

        GaussianBlur($src, $dst, new Size($i, $i), 0, 0);

    }

    return $dst;
}

//Ulkanjanje suma
function nois($src)
{
    $dst = null;

    for ($i = 1; $i < 6; $i = $i + 2) {
        bilateralFilter($src, $dst, $i, $i * 2, $i / 2);
    }
    return $dst;
}

//Diletacija
function dilateImage($src)
{
    $dilation_type = MORPH_CROSS;
    $dilation_size = 4;
    $dst = null;
    $element = getStructuringElement($dilation_type,
        new Size(2 * $dilation_size + 1, 2 * $dilation_size + 1),
        new Point($dilation_size, $dilation_size));

    dilate($src, $dst, $element);

    return $dst;
}


//Folderi
$ulaz = 'slike/';
$izlaz = 'rezultati/';
$anotacija = 'anotacija/';
$tmp = 'tmp/';


$input = filter_input(INPUT_POST, 'slika');

$mod = filter_input(INPUT_POST, 'model');

$noise = filter_input(INPUT_POST, 'noise');

$hist = filter_input(INPUT_POST, 'hist');

$dilate = filter_input(INPUT_POST, 'dilate');

//kontrast i osvijetljenje
contrastImage($ulaz, 0, $input, $tmp);


if (file_exists($tmp . $input)):

    // obrisi ako postoji izlazni file
    if (file_exists($izlaz . $input)):
        unlink($izlaz . $input);
    endif;

    $src = imread($tmp . $input);
    $dst = null;

else:

    die('Ulazni fajl se ne može procitat');

endif;


//detekcija lica pomocu kaskadnog klasifikatora
$faceClassifier = new CascadeClassifier();
$faceClassifier->load('modeli/lbpcascades/' . $mod);

// LBPHFaceRecognizer
//$faceRecognizer = LBPHFaceRecognizer::create();


//maskiranje neostrina i uklanjanje šuma
if ($noise == 1):
    $src = nois($src);
    $src = maskSharp($src);

endif;

//diletacija
if ($dilate == 1):
    $src = dilateImage($src);
endif;

//konvertovanje slike u sivu
$gray = cvtColor($src, COLOR_BGR2GRAY);


if ($hist == 1):
//Histogram
    equalizeHist($gray, $gray);
endif;

//detektovanje lica
$faceClassifier->detectMultiScale($gray, $faces);

$faceImages = $faceLabels = [];

if ($faces) {


    /* Treniranje objekta
    foreach ($faces as $k => $face) {
        $faceImages[] = $gray->getImageROI($face); // face coordinates to image
        $faceLabels[] = 9;
    }

    $faceRecognizer->read("trenirani_model".DIRECTORY_SEPARATOR."train.yml");
    $faceRecognizer->update($faceImages, $faceLabels);
    $faceRecognizer->write("trenirani_model".DIRECTORY_SEPARATOR."train.yml");
*/
    
    $scalar = new Scalar(0, 0, 255);
    //anotacije na detektovanom licu
    foreach ($faces as $face) {

        $data['x'] = 'x:' . $face->x . ';';
        $data['y'] = 'y:' . $face->y . ';';
        $data['width'] = 'width:' . $face->width . ';';
        $data['height'] = 'height:' . $face->height . ';' . PHP_EOL;

        file_put_contents($anotacija . strtok($input, '.') . '.txt', $data);

        rectangleByRect($src, $face, $scalar, 2);
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
        <span class="text-muted">&copy; 2018 Begić Fuad, Ahetmović Selma</span>
    </div>
</footer>


</body>
</html>
