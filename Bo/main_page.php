<?php
session_start();
if ( !isset( $_SESSION['USER_NAME'] ) ) {
    header( "location:index.php?ERR=session" );
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="Baihong Qi" >

    <title>Online Photo Gallery</title>

    <!-- Bootstrap Core CSS -->
    <link href="ref/bower_components/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- MetisMenu CSS -->
    <link href="ref/bower_components/metisMenu/dist/metisMenu.min.css" rel="stylesheet">

    <!-- Custom CSS -->
    <link href="ref/dist/css/sb-admin-2.css" rel="stylesheet">

    <!-- Custom Fonts -->
    <link href="ref/bower_components/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body>

<div id="wrapper">

    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <!-- /.navbar-header -->
        <div class="navbar-header">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href=""><b>Online Photo Gallery</b></a>
        </div>

        <!-- Search Bar -->
        <ul class = "navbar-left">
            <form class="navbar-form" role="search" action="search.php" target="iframepage">
                <div class="form-group">
                    <input type="text" id="" class="form-control has-search-icon" placeholder="Search Pictures" style="">
                </div>
            </form>
        </ul>

        <!-- /.navbar-top-links -->
        <ul class="nav navbar-top-links navbar-right">
            <!-- /.dropdown-user -->
            <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="fa fa-user fa-fw"></i>  <?php echo $_SESSION["USER_NAME"] ?>  <i class="fa fa-caret-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-user">
                    <li><a href="#"><i class="fa fa-user fa-fw"></i> Profile</a>
                    </li>
                    <li><a href="#"><i class="fa fa-gear fa-fw"></i> Settings</a>
                    </li>
                    <li class="divider"></li>
                    <li><a href="logout.php"><i class="fa fa-sign-out fa-fw"></i> Logout</a>
                    </li>
                </ul>
            </li>
        </ul>
        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li>
                        <a href="all_photos.php" target="iframepage"><i class="fa fa-image"></i>  All Photos</a>
                    </li>
                    <li>
                        <a href="javascript:void(0)" ><i class="fa fa-upload"></i>  Upload Photos<span class="fa arrow"></span></a>
                        <ul class="nav nav-second-level">
                            <li>
                                <a href="upload_file.php" target="iframepage"><i class="fa fa-file"></i>  Upload One File</a>
                            </li>
                            <li>
                                <a href="upload_folder.php" target="iframepage"><i class="fa fa-folder"></i>  Upload Folder</a>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <a href="groups.php" target="iframepage"><i class="fa fa-users"></i>  Groups</a>
                    </li>
                    <li>
                        <a href="search.php" target="iframepage"><i class="fa fa-globe"></i>  Advanced Search</a>
                    </li>
                    <li>
                        <a href="blank_test.php" target="iframepage"><i class="fa fa-bar-chart"></i>  Data Analysis</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Content -->
    <div id="page-wrapper">
        <iframe src="blank_test.php" marginheight="0" marginwidth="0" frameborder="0" scrolling="no" width="100%" height="100%" id="iframecon" name="iframepage" onLoad="iFrameHeight();" ></iframe>
    </div>
</div>

<!-- make iframe full size -->
<script type="text/javascript" language="javascript">
    function iFrameHeight() {
        var ifm= document.getElementById("iframecon");
        var subWeb = document.frames ? document.frames["iframepage"].document :
            ifm.contentDocument;
        if(ifm != null && subWeb != null) {
            ifm.height = subWeb.body.scrollHeight + 100;
        }
    }
</script>

<!-- jQuery -->
<script src="ref/bower_components/jquery/dist/jquery.min.js"></script>

<!-- Bootstrap Core JavaScript -->
<script src="ref/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>

<!-- Metis Menu Plugin JavaScript -->
<script src="ref/bower_components/metisMenu/dist/metisMenu.min.js"></script>

<!-- Custom Theme JavaScript -->
<script src="ref/dist/js/sb-admin-2.js"></script>

</body>

</html>

