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
use const CV\{COLOR_BGR2GRAY, CV_HAAR_DO_ROUGH_SEARCH, IMREAD_GRAYSCALE};


function detectTrain($images = array(), $labels = array())
{

    $faceRecognizer = LBPHFaceRecognizer::create();

    $faceRecognizer->train($images, $labels);

    $faceRecognizer->write("trenirani_model" . DIRECTORY_SEPARATOR . "train2.yml");

}


function trainFolder()
{
    $file = array_diff(scandir('train/prepare'), array('..', '.'));

    $images = [];
    $labels = [];
    $real = realpath('train/prepare');
    foreach ($file as $row):

        if (strpos($row, 'fuad') !== false):

            $images[] = imread($real . DIRECTORY_SEPARATOR . $row, IMREAD_GRAYSCALE);
            $labels[] = 1;
        elseif (strpos($row, 'selma') !== false):
            $images[] = imread($real . DIRECTORY_SEPARATOR . $row, IMREAD_GRAYSCALE);
            $labels[] = 2;
        else:
            $images[] = imread($real . DIRECTORY_SEPARATOR . $row, IMREAD_GRAYSCALE);
            $labels[] = 3;

        endif;


    endforeach;

    detectTrain($images, $labels);
}

function prepareImage()
{

    $ulaz = 'train/';
    $izlaz = 'train/prepare/';
    $anotacija = 'anotacija/';

    $file = array_diff(scandir('train'), array('..', '.'));
    $faces = null;
    foreach ($file as $row):
        if (getimagesize($ulaz . $row)):
            $src = imread($ulaz . $row);

//detekcija lica pomocu kaskadnog klasifikatora
            $faceClassifier = new CascadeClassifier();
            $faceClassifier->load('modeli/lbpcascades/' . $_POST['model']);

            //$faces = null;

//konvertovanje slike u sivu
            $gray = cvtColor($src, COLOR_BGR2GRAY);


//Histogram
            equalizeHist($gray, $gray);


//detektovanje lica
            $faceClassifier->detectMultiScale($gray, $faces, 1.1, 3, CV_HAAR_DO_ROUGH_SEARCH, new Size(50, 50));

            $faceImages = $faceLabels = [];

            $landmarks = null;
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

                $data = null;
                $myFace = null;
                foreach ($faces as $face) {
                    $face = $gray->getImageROI($face);
                    $data['x'] .= 'x:' . $face->x . ';';
                    $data['y'] .= 'y:' . $face->y . ';';
                    $data['width'] .= 'width:' . $face->width . ';';
                    $data['height'] .= 'height:' . $face->height . ';' . PHP_EOL;

                    file_put_contents($anotacija . strtok($row, '.') . '.txt', $data);

                    rectangleByRect($src, $face, $scalar, 2);
                }

                if (file_exists($izlaz . $row)):
                    unlink($izlaz . $row);
                endif;

                //resize($face, $myFace, new Size(92, 112));
                imwrite($izlaz . $row, $face);

            }


        endif;
    endforeach;


}

if ($_POST['prepare_model']):
    prepareImage();

endif;

if ($_POST['train_model']):
    trainFolder();
endif;
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


</main>
<div class="container">
    <div class="card">
        <h5 class="card-header">Direktorij (ulaz, izlaz)</h5>
        <div class="card-body">


            <ul class="list-group">
                <li class="list-group-item"><a target="_blank" href="train/prepare/">Priprema klasa za treniranje</a>
                </li>
                <li class="list-group-item"><a target="_blank" href="trenirani_model">Test</a></li>
            </ul>

        </div>

    </div>
</div>
<footer class="footer">
    <div class="container">
        <span class="text-muted">&copy; 2018 Begić Fuad, Ahetmović Selma</span>
    </div>
</footer>


</body>
