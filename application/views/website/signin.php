<!DOCTYPE html>
<html lang="en">
<head>
<?php $baseAssetsUrl = '../../pembelajarandb/assets/'; ?>
  <!-- SITE TITTLE -->
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Small Apps</title>
  
  <!-- PLUGINS CSS STYLE -->
  <!-- Bootstrap -->
  <link href="<?php echo $baseAssetsUrl; ?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <!-- Themefisher Font -->  
  <link href="<?php echo $baseAssetsUrl; ?>plugins/themefisher-font/style.css" rel="stylesheet">
  <!-- Owl Carousel -->
  <link href="<?php echo $baseAssetsUrl; ?>plugins/owl-carousel/assets/owl.carousel.min.css" rel="stylesheet" media="screen">
  <!-- Owl Carousel Theme -->
  <link href="<?php echo $baseAssetsUrl; ?>plugins/owl-carousel/assets/owl.theme.green.min.css" rel="stylesheet" media="screen">
  <!-- Fancy Box -->
  <link href="<?php echo $baseAssetsUrl; ?>plugins/fancybox/jquery.fancybox.min.css" rel="stylesheet">

  <!-- CUSTOM CSS -->
  <link href="<?php echo $baseAssetsUrl; ?>css/style.css" rel="stylesheet">

  <!-- FAVICON -->
  <link href="<?php echo $baseAssetsUrl; ?>images/favicon.png" rel="shortcut icon">

</head>

<body class="body-wrapper">


<section class="user-login section">
	<div class="container">
		<div class="row">
			<div class="col-12">
				<div class="block">
					<!-- Image -->
					<div class="image align-self-center"><img class="img-fluid" src="<?php echo $baseAssetsUrl; ?>images/Login/front-desk-sign-in.jpg" alt="desk-image"></div>
					<!-- Content -->
					<div class="content text-center">
						<div class="logo">
							<a href="homepage.html"><img src="<?php echo $baseAssetsUrl; ?>images/logo.svg" alt=""></a>
						</div>
						<div class="title-text">
							<h3>Sign in to  To Your Account</h3>
						</div>
						<form action="#">
							<!-- Username -->
							<input class="form-control main" type="text" placeholder="Username" required>
							<!-- Password -->
							<input class="form-control main" type="password" placeholder="Password" required>
							<!-- Submit Button -->
							<button class="btn btn-main-md">sign in</button>
						</form>
						<div class="new-acount">
							<a href="#">Forget your password?</a>
							<p>Don't Have an account? <a href="sign-up.html"> SIGN UP</a></p>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>


  <!-- JAVASCRIPTS -->
  
  <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyBI14J_PNWVd-m0gnUBkjmhoQyNyd7nllA" async defer></script>

  <script src="<?php echo $baseAssetsUrl; ?>plugins/jquery/jquery.js"></script>
  <script src="<?php echo $baseAssetsUrl; ?>plugins/popper/popper.min.js"></script>
  <script src="<?php echo $baseAssetsUrl; ?>plugins/bootstrap/js/bootstrap.min.js"></script>
  <script src="<?php echo $baseAssetsUrl; ?>plugins/owl-carousel/owl.carousel.min.js"></script>
  <script src="<?php echo $baseAssetsUrl; ?>plugins/fancybox/jquery.fancybox.min.js"></script>
  <script src="<?php echo $baseAssetsUrl; ?>plugins/smoothscroll/SmoothScroll.min.js"></script>
  <script src="<?php echo $baseAssetsUrl; ?>plugins/syotimer/jquery.syotimer.min.js"></script>
  
  <script src="<?php echo $baseAssetsUrl; ?>js/custom.js"></script>
</body>

</html>