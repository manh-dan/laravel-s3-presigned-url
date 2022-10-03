<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="ManhDan Blogs">
        <meta name="author" content="ManhDan Blogs">
        <meta name="generator" content="ManhDan Blogs 0.84.0">
        <title>ManhDan Blogs</title>
        <link rel="icon" href="https://manhdandev.com/web/img/favicon.webp" type="image/x-icon"/>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css">
        <link href="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone.css" rel="stylesheet" type="text/css" />
    </head>
<body>
<div class="col-lg-8 mx-auto p-3 py-md-5">
    <header class="d-flex align-items-center pb-3 mb-5 border-bottom">
        <a href="https://manhdandev.com" class="d-flex align-items-center text-dark text-decoration-none" target="_blank">
            <img src="https://manhdandev.com/web/img/logo.webp" width="100px" height="100px">
        </a>
    </header>
    <main>
        <h1>Laravel, Presigned URLs S3 & Dropzone (Basic)</h1>
        <p>RDS get random user information: {{ $user->id ?? '' }} - {{ $user->name ?? '' }} - {{ $user->email ?? '' }}</p>
        <div class="mb-5">
            <form class="form" action="#" method="post">
                <div class="fv-row">
                    <div class="dropzone" id="dropzone">
                        <div class="dz-message needsclick">
                            <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>
                            <div class="ms-4">
                                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Drop files here or click to upload.</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </main>
    <footer class="pt-5 my-5 text-muted border-top">
        &copy;Copyright &copy;2021 All rights reserved | This template is made with
        <i class="fa fa-heart-o"></i> by <a href="https://www.facebook.com/beater.2708" rel="noopener" target="_blank">ManhDan</a>
    </footer>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"></script>
<script src="https://unpkg.com/dropzone@6.0.0-beta.1/dist/dropzone-min.js"></script>
<script>
    var dropzone = new Dropzone('#dropzone',{
    url: '#',
    method: 'put',
    autoQueue: false,
    autoProcessQueue: false,
    init: function() {
        /*
            When a file is added to the queue
                - pass it along to the signed url controller
                - get the response json
                - set the upload url based on the response
                - add additional data (such as the uuid filename)
                    to a temporary parameter
                - start the upload
        */
        this.on('addedfile', function(file) {
            fetch('/s3-presigned-url?name='+file.name, {
                method: 'get'
            }).then(function (response) {
                return response.json();
            }).then(function (json) {
                dropzone.options.url = json.url;
                file.additionalData = json.additionalData;
                dropzone.processFile(file);
            });
        });

        /*
            When uploading the file
                - make sure to set the upload timeout to near unlimited
                - add all the additional data to the request
        */
        this.on('sending', function(file, xhr, formData) {
            xhr.timeout = 99999999;
            for (var field in file.additionalData) {
                formData.append(field, file.additionalData[field]);
            }
        });

        /*
            Handle the success of an upload
        */
        this.on('success', function(file) {
            // Let the Laravel application know the file was uploaded successfully
        });
    },
    sending: function(file, xhr) {
        var _send = xhr.send;
        xhr.send = function() {
            _send.call(xhr, file);
        };
    },
});
</script>
</body>
</html>
