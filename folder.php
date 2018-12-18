<?php

ini_set('max_execution_time', 300);
use CV\CascadeClassifier;
use CV\Face\LBPHFaceRecognizer;
use CV\Point;
use CV\Size;
use CV\Scalar;
use function CV\{addWeighted,
    bilateralFilter,
    blur,
    cvtColor,
    dilate,
    equalizeHist,
    GaussianBlur,
    getStructuringElement,
    imread,
    imwrite,
    medianBlur,
    rectangleByRect};
use const CV\{COLOR_BGR2GRAY, MORPH_CROSS};

//Fukcija za kontrast
function contrastImage()
{

    $ulaz = 'slike/';
    $rezultati = 'rezultati/kontrast/';
    $file = array_diff(scandir('slike'), array('..', '.'));

    foreach ($file as $row):
        $imagick = new \Imagick(realpath($ulaz . $row));

        if (file_exists($rezultati . $row)):
            unlink($rezultati . $row);
        endif;


        $imagick->brightnessContrastImage(0, 30);


        $imagick->writeImage($rezultati . $row);

    endforeach;

}


//Bright za osvijeteljenje
function brightImage()
{

    $ulaz = 'slike/';
    $rezultati = 'rezultati/osvjetljenje/';
    $file = array_diff(scandir('slike'), array('..', '.'));

    foreach ($file as $row):

        if (file_exists($rezultati . $row)):
            unlink($rezultati . $row);
        endif;

        $imagick = new \Imagick(realpath($ulaz . $row));


        $imagick->brightnessContrastImage(12, 0);


        $imagick->writeImage($rezultati . $row);

    endforeach;

}

//Ulkanjanje suma
function nois()
{
    $ulaz = 'slike/';
    $rezultati = 'rezultati/sum/';
    $file = array_diff(scandir('slike'), array('..', '.'));

    foreach ($file as $row):

        if (file_exists($rezultati . $row)):
            unlink($rezultati . $row);
        endif;

        $src = imread($ulaz . $row);
        $dst = null;
        for ($i = 1; $i < 6; $i = $i + 2) {

            GaussianBlur($src, $dst, new Size($i, $i), 0, 0);

        }
        imwrite($rezultati . $row, $dst);
    endforeach;
}

//Maskiranje naostriuna

function maskSharp()
{

    $ulaz = 'slike/';
    $rezultati = 'rezultati/maskiranje/';
    $file = array_diff(scandir('slike'), array('..', '.'));

    foreach ($file as $row):

        if (file_exists($rezultati . $row)):
            unlink($rezultati . $row);
        endif;

        $src = imread($ulaz . $row);
        $dst = null;
        $mask = null;
        for ($i = 1; $i < 6; $i = $i + 2) {

            medianBlur($src, $dst, $i);

        }

        addWeighted($src, 1.5, $dst, -0.5, 0, $mask);

        imwrite($rezultati . $row, $mask);
    endforeach;


}

//Diletacija slike
function dilateImage()
{

    $ulaz = 'slike/';
    $rezultati = 'rezultati/diletacija/';
    $file = array_diff(scandir('slike'), array('..', '.'));

    $dilation_type = MORPH_CROSS;
    $dilation_size = 4;

    foreach ($file as $row):

        if (file_exists($rezultati . $row)):
            unlink($rezultati . $row);
        endif;

        $src = imread($ulaz . $row);
        $dst = null;


        $element = getStructuringElement($dilation_type,
            new Size(2 * $dilation_size + 1, 2 * $dilation_size + 1),
            new Point($dilation_size, $dilation_size));

        dilate($src, $dst, $element);
        imwrite($rezultati . $row, $dst);
    endforeach;
}


function hist()
{
    $ulaz = 'slike/';
    $rezultati = 'rezultati/hist/';

    $file = array_diff(scandir('slike'), array('..', '.'));
    foreach ($file as $row):
        $src = imread($ulaz . $row);

        if (file_exists($rezultati . $row)):
            unlink($rezultati . $row);
        endif;

        //konvertovanje slike u sivu
        $gray = cvtColor($src, COLOR_BGR2GRAY);

        equalizeHist($gray, $gray);
        imwrite($rezultati . $row, $gray);
    endforeach;
}

function detection()
{

    $faceClassifier = new CascadeClassifier();
    $faceClassifier->load('modeli/lbpcascades/lbpcascade_frontalface_improved.xml');
    $ulaz = 'slike/';
    $rezultati = 'rezultati/detekcija/';

    $file = array_diff(scandir('slike'), array('..', '.'));
    $scalar = new Scalar(0, 0, 255);
    foreach ($file as $row):
        $src = imread($ulaz . $row);
        $faces = null;
        $data = null;
        $gray = cvtColor($src, COLOR_BGR2GRAY);

        $faceClassifier->detectMultiScale($gray, $faces);

        if ($faces) {

            foreach ($faces as $face) {

                $data['x'] .= 'x:' . $face->x . ';';
                $data['y'] .= 'y:' . $face->y . ';';
                $data['width'] .= 'width:' . $face->width . ';';
                $data['height'] .= 'height:' . $face->height . ';' . PHP_EOL;

                file_put_contents('anotacija/' . strtok($row, '.') . '.txt', $data);

                rectangleByRect($src, $face, $scalar, 2);
            }
            imwrite($rezultati . $row, $src);
        }

    endforeach;


}

if ($_POST['contrast'] == 1):;
    contrastImage();
endif;

if ($_POST['bright'] == 1):
    brightImage();
endif;

if ($_POST['noise'] == 1):
    $src = nois();
endif;


if ($_POST['dilate'] == 1):
    dilateImage();
endif;

if ($_POST['hist'] == 1):
    hist();
endif;

if ($_POST['mask'] == 1):
    maskSharp();
endif;

if ($_POST['detekcija'] == 1):
    detection();
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
                <li class="list-group-item"><a target="_blank" href="slike">Ulaz</a></li>
                <li class="list-group-item"><a target="_blank" href="rezultati">Rezultati</a></li>
                <li class="list-group-item"><a target="_blank" href="anotacija">Anotacije</a></li>
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
</html>