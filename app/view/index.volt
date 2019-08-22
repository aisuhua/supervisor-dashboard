<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Supervisor Dashboard</title>
    <title></title>
    <link rel="shortcut icon" type="image/png" href="/favicons/favicon.ico?v=2" />

    <!-- bootstrap -->
    <link href="/css/bootstrap.min.css" rel="stylesheet">

    <!-- datatables -->
    <link href="/plugins/datatables/dataTables.bootstrap.min.css" rel="stylesheet">
    <link href="/plugins/datatables/button/buttons.bootstrap.min.css" rel="stylesheet">
    <link href="/plugins/datatables/select/select.bootstrap.min.css" rel="stylesheet">
    <link href="/plugins/datatables/searchHighlight/dataTables.searchHighlight.css" rel="stylesheet">

    <!-- nprogress -->
    <link href="/plugins/nprogress/nprogress.css" rel="stylesheet">

    <link rel="stylesheet" href="/css/override.css">

    <!-- bootstrap -->
    <script src="/js/jquery.min.js"></script>
    <script src="/js/bootstrap.min.js"></script>

    <!-- datatables -->
    <script src="/plugins/datatables/jquery.dataTables.min.js"></script>
    <script src="/plugins/datatables/dataTables.bootstrap.min.js"></script>
    <script src="/plugins/datatables/button/dataTables.buttons.min.js"></script>
    <script src="/plugins/datatables/button/buttons.bootstrap.min.js"></script>
    <script src="/plugins/datatables/select/dataTables.select.min.js"></script>
    <!-- https://datatables.net/blog/2014-10-22 -->
    <script src="/plugins/datatables/searchHighlight/jquery.highlight.js"></script>
    <script src="/plugins/datatables/searchHighlight/dataTables.searchHighlight.min.js"></script>

    <!-- pjax -->
    <script src="/plugins/pjax/jquery.pjax.js"></script>

    <!-- nprogress -->
    <script src="/plugins/nprogress/nprogress.js"></script>

    <!-- pnotify -->
    <script src="/plugins/pnotify/PNotify.js"></script>
    <script src="/plugins/pnotify/PNotifyButtons.js"></script>

    <!-- https://github.com/jacwright/date.format -->
    <script src="/js/date.format.js"></script>
    <script src="/js/function.js"></script>
    <script src="/js/override.js"></script>

    <!--[if lt IE 9]>
    <script src="https://cdn.bootcss.com/html5shiv/r29/html5.min.js"></script>
    <script src="https://cdn.bootcss.com/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
</head>
<body>

    <div id="wrapper">
        <div class="container">
            {{ content() }}
        </div>
    </div>

</body>
</html>