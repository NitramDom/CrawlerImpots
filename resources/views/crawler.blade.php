<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>CrawlerImpots</title>

    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
    <div class="container py-5">
        <div class="row mb-4">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-6">CrawlerImpots</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6 mx-auto">
                <div class="card pt-4 pl-2 pr-2 pb-2" style="padding: 0 20px;">
                    <div class="alert alert-danger" role="alert" style="display: none;">
                    </div>
                    <form id="form" method="post" action="{{ route('form') }}">
                        @csrf

                        <div class="form-group">
                            <label for="number">
                                <h6>Numéro fiscal</h6>
                            </label>
                            <input type="text" name="number" required class="form-control">
                        </div>
                        <div class="form-group mt-2">
                            <label for="reference">
                                <h6>Référence de l'avis</h6>
                            </label>
                            <input type="text" name="reference" required class="form-control">
                        </div>
                        <button type="submit" class="btn btn-success mt-3">Valider</button>
                    </form>

                    <span id="result"></span>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"
        integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ=="
        crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        $alertDanger = $(".alert-danger");

        $("#form").on('submit', function(e) {

            e.preventDefault();

            $.ajax({
                method: $(this).attr('method'),
                url: $(this).attr('action'),
                data: {
                    "_token": "{{ csrf_token() }}",
                    "number": $("input[name=number]").val(),
                    "reference": $("input[name=reference]").val()
                },
                success: function(response) {

                    if (response.status == "error") {

                        $alertDanger.html(response.message).fadeIn();
                        $("#result").html("");
                    }
                    if (response.status == "success") {

                        $alertDanger.hide();

                        $("#result").html(response.message.infos.total_gross_income);
                    }
                }
            });
        });
    </script>
</body>

</html>
