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

    <div class="card">
        <h5 class="card-header">Image conversion</h5>
        <div class="card-body">
            <h5 class="card-title">Image conversion</h5>
        </div>
        <div class="row justify-content-md-center">
            <div class="col col-md-10">
                <form method="post" action="folder.php">


                    <div class="form-check">
                        <input name="detekcija" class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1">
                            Anotacija i detekcija (maskiranje)
                        </label>

                    </div>

                    <div class="form-check">
                        <input name="noise" class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1">
                            Uklanjanje šuma
                        </label>

                    </div>

                    <div class="form-check">
                        <input name="mask" class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1">
                            Maskiranje naostrina
                        </label>

                    </div>

                    <div class="form-check">
                        <input name="contrast" class="form-check-input" value="1" type="checkbox">
                        <label class="form-check-label">
                            Filter za kontrast
                        </label>

                    </div>

                    <div class="form-check">
                        <input name="bright" class="form-check-input" value="1" type="checkbox">
                        <label class="form-check-label">
                            Filter za osvijetljenje
                        </label>
                    </div>



                    <div class="form-check">
                        <input name="hist" class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1">
                            Ujednačavanje histograma
                        </label>

                    </div>

                    <div class="form-check">
                        <input name="dilate" class="form-check-input" type="checkbox" value="1" id="defaultCheck1">
                        <label class="form-check-label" for="defaultCheck1">
                            Dilate
                        </label>

                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right">Konvertuj grupu slika</button>
                    </div>
                    <div class="form-group">
                        <p class="font-italic">* Rezultati grupne konverzije bit ce prikazani u folderu rezultati</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br/>

    <div class="card">
        <h5 class="card-header">Algortiam Face detection i treniranje</h5>
        <div class="card-body">
            <h5 class="card-title">Kaskadni filter za detekciju i treniranje</h5>
        </div>
        <div class="row justify-content-md-center">
            <div class="col col-md-10">
                <form method="post" action="cascade.php">
                    <div class="form-group">
                        <label for="slika">Izaberite sliku</label>

                        <?php $file = array_diff(scandir('slike'), array('..', '.')); ?>

                        <select name="slika" id="slika" class="form-control">
                            <?php foreach ($file as $row): ?>
                                <option value="<?= $row ?>"><?= $row ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="slika">Izaberite model</label>

                        <?php $models = array_diff(scandir('modeli/lbpcascades'), array('..', '.')); ?>

                        <select name="model" id="slika" class="form-control">
                            <?php foreach ($models as $key => $row): ?>

                                <?php if ($key < 4): ?>
                                    <option value="<?= $row ?>" disabled><?= $row ?></option>
                                <?php else: ?>
                                    <option value="<?= $row ?>"><?= $row ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </select>
                    </div>



                    <br/>


                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="trainModel" name="train_model" value="1"
                               id="trainModel">
                        <label class="form-check-label" for="trainModel">
                            Uključi modul za treniranje
                        </label>

                    </div>


                    <div class="form-group">

                        <label for="train">Treniraj model</label>
                        <select name="train" id="train" class="form-control" disabled>
                            <option value="1">Fuad Begic</option>
                            <option value="2">Selma Ahmetović</option>
                            <option value="3">Nepoznato</option>
                        </select>
                    </div>


                    <div class="form-group">
                        <button type="submit" class="btn btn-primary float-right">Pokreni</button>
                    </div>

                    <div class="form-group">
                        <p class="font-italic">* Odaberite parametre i pokrenite algoritam</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <br/>
    <div class="card">
        <h5 class="card-header">Face recognition</h5>
        <div class="card-body">
            <h5 class="card-title">Prepoznavanje lica</h5>
        </div>
        <div class="row justify-content-md-center">
            <div class="col col-md-10">
                <form method="post" action="recognizer.php">
                    <div class="form-group">
                        <label for="slika">Izaberite sliku</label>

                        <?php $file = array_diff(scandir('slike'), array('..', '.')); ?>

                        <select name="slika" id="slika" class="form-control">
                            <?php foreach ($file as $row): ?>
                                <option value="<?= $row ?>"><?= $row ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="slika">Izaberite model</label>

                        <?php $models = array_diff(scandir('modeli/lbpcascades'), array('..', '.')); ?>

                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-info float-right">Detektuj</button>
                    </div>

                    <div class="form-group">
                        <p class="font-italic">* Izaberite sliku</p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <br/>

    <div class="card">
        <h5 class="card-header">Direktorij (ulaz, izlaz, modeli)</h5>
        <div class="card-body">
            <ul class="list-group">
                <li class="list-group-item"><a href="slike">Ulaz </a></li>
                <li class="list-group-item"><a href="rezultati">Rezultati</a></li>
                <li class="list-group-item"><a href="modeli/lbpcascades/">Modeli</a></li>
                <li class="list-group-item"><a href="trenirani_model/">Trenirani modeli za prepoznavanje</a></li>
                <li class="list-group-item"><a href="anotacija">Anotacije</a></li>
                <li class="list-group-item"><a href="10_posto">10%</a></li>
            </ul>


        </div>
    </div>
    <br/>

</main>
<footer class="footer">
    <div class="container">
        <span class="text-muted">&copy; 2018 Begić Fuad, Ahetmović Selma</span>
    </div>
</footer>

<!-- Optional JavaScript -->
<!-- jQuery first, then Popper.js, then Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
        crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"
        integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49"
        crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js"
        integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy"
        crossorigin="anonymous"></script>
</body>
</html>

<script type="application/javascript">
    $("#trainModel").click(function () {
        if ($("#trainModel").is(':checked'))
            $("#train").prop('disabled', false);
        else
            $("#train").prop('disabled', true);
    })

</script>