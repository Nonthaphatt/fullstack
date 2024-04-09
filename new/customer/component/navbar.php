

<?php include_once '../conn.php'; ?>
<?php include('./component/session.php'); ?>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <meta charset="UTF-8">
    <style>
        #lblCartCount{
            /* background-color: #99d9f2; */
            color: white;
            position: absolute;
            top: 0%;
            right: -10%;
            transform: translate(-50%, -25%);
            /* border-radius: 50%;
            border: solid black 2px;; */
        }
    </style>
    <title>HomePage</title>
</head>
<body>
<br>
    <nav class="navbar navbar-expand-lg mx-auto mt-3 rounded-2 mb-5" style="width:90%;background-color: #99d9f2">
        <div class="container-fluid">
            <a class="navbar-brand me-auto ms-3" href="index.php"><h3>Shop</h3></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <div class="me-auto"></div>
                <ul class="navbar-nav mb-2 mb-lg-0 me-3">

                    <li class="nav-item me-2 position-relative">
                    <a class="nav-link active" href="cart.php"><h2><i class="bi bi-cart"></i></h2>
                    <?php
                        $cartIconCount = 0;
                        if ((isset($_SESSION['cart'])) && (isset($_SESSION['guest']))) {
                            $cartIconCount = (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) ? count($_SESSION['cart']) : 0;
                        } elseif (isset($_SESSION['member']) && isset($_SESSION['status']) === true) {
                            $uid = (isset($_SESSION['id_username'])) ? $_SESSION['id_username'] : '';
                            $cur = "SELECT * FROM cart WHERE CusID = '$uid'";
                            $msresults = mysqli_query($conn, $cur);
                            $cartIconCount = (mysqli_num_rows($msresults) > 0) ? mysqli_num_rows($msresults) : 0;
                        }
                    ?>
                        <?php if (!empty($cartIconCount)) : ?>
                            <span class='badge' id='lblCartCount'><h5><?php echo $cartIconCount; ?></h5></span>
                        <?php endif; ?>
                    </a>
                    </li>
                    
                    <?php if (isset($_SESSION['member']) && isset($_SESSION['status']) && $_SESSION['status'] === true) : ?>

                        <li class="nav-item me-2">
                            <a class="nav-link"  href="history.php"><h3>History</h3></a>
                        </li>

                        <li class="nav-item me-2">
                            <a class="nav-link"  href="profile.php"><h3><i class="bi bi-person-circle"></i></h3></a>
                        </li>

                        <li class="nav-item me-2">
                            <a class="nav-link" href="logoutProcess.php"><h4 class="mb-0">Log out</h4></a>
                        </li>

                    <?php else : ?>

                        <li class="nav-item me-2">
                            <a class="nav-link" href="login.php"><h4 class="mb-0">Login</h4></a>
                        </li>

                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js" integrity="sha384-0pUGZvbkm6XF6gxjEnlmuGrJXVbNuzT9qBBavbLwCsOGabYfZo0T0to5eqruptLy" crossorigin="anonymous"></script>
</body>