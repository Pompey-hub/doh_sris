<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>404 - Page Not Found</title>
    <link rel="icon" type="image/png" href="/error/ro8logo.png">
    <link href="/error/style.css?v1.1" rel="stylesheet">
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="/error/ro8logo.png" alt="DOH-EVCHD Logo">
        </div>

        <div class="content">
            <h1>404</h1>
            <h2>We cannot find the page you are looking for.</h2>
            <p>The page might have been removed, had its name changed, or is temporarily unavailable.</p>
        </div>
        
        <div class="btn">
            <a href="javascript:void(0);" id="homepage">Go to Homepage</a>
        </div>
    </div>

    <script type="text/javascript">
        document.getElementById("homepage").addEventListener("click", myFunction);
        function myFunction() {
            var str = window.location.protocol + '//' + window.location.hostname;
            window.location.href = str;
        }
    </script>
</body>
</html>
