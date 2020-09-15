<!-- Footer -->
	<section id="footer">  
		<div class="container">
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 mt-2 mt-sm-5">
					<ul class="list-unstyled list-inline social text-center">
						<?php if ( get_theme_mod( 'streamium_footer_facebook_url' ) ) : ?>

						    <li class="list-inline-item"><a href="<?php echo get_theme_mod( 'streamium_footer_facebook_url' ); ?>" target="_blank"><i class="fa fa-facebook"></i></a></li>

						<?php endif; ?>

						<?php if ( get_theme_mod( 'streamium_footer_twitter_url' ) ) : ?>

						    <li class="list-inline-item"><a href="<?php echo get_theme_mod( 'streamium_footer_twitter_url' ); ?>" target="_blank"><i class="fa fa-twitter"></i></a></li>

						<?php endif; ?>

						<?php if ( get_theme_mod( 'streamium_footer_instagram_url' ) ) : ?>

						    <li class="list-inline-item"><a href="<?php echo get_theme_mod( 'streamium_footer_instagram_url' ); ?>" target="_blank"><i class="fa fa-instagram"></i></a></li>

						<?php endif; ?>

						<?php if ( get_theme_mod( 'streamium_footer_google_url' ) ) : ?>

						    <li class="list-inline-item"><a href="<?php echo get_theme_mod( 'streamium_footer_google_url' ); ?>" target="_blank"><i class="fa fa-google-plus"></i></a></li>

						<?php endif; ?>
						
						<?php if ( get_theme_mod( 'streamium_footer_email_url' ) ) : ?>

						    <li class="list-inline-item"><a href="<?php echo get_theme_mod( 'streamium_footer_email_url' ); ?>" target="_blank"><i class="fa fa-envelope"></i></a></li>

						<?php endif; ?>
						
					</ul>
				</div>
				</hr>
			</div>

			<div class="row text-center text-xs-center text-sm-left text-md-left">
				<ul class="nav navbar-nav navbar-center footer-menu">
				<?php
				echo str_replace('sub-menu', 'sub-menu is-hidden', wp_nav_menu( array(
						    'echo' => false,
						    'container' => false, 
						    'theme_location' => 'streamium-footer-menu',
						  ) )
						);
						?>
				</ul>
				
			</div>	
			<div class="row">
				<div class="col-xs-12 col-sm-12 col-md-12 mt-2 mt-sm-2 text-center text-white powered-by-footer">
					<?php if ( get_theme_mod( 'streamium_remove_powered_by_s3bubble' ) ) : ?>

					    <p><?php echo get_theme_mod( 'streamium_remove_powered_by_s3bubble' ); ?></p>

					<?php else : ?>

					    <p>Powered by <a href="https://s3bubble.com">S3Bubble.com</a></p>

					<?php endif; ?>
				</div>
				</hr>
			</div>	
		</div>
	</section>
	<!-- ./Footer -->

	<div class="streamium-review-panel from-right">
		
		<header class="streamium-review-panel-header">
			<h1><?php _e( 'Reviews', 'streamium' ); ?></h1>
			<a href="#0" class="streamium-review-panel-close"><i class="fa fa-times" aria-hidden="true"></i></a>
		</header>

		<div class="streamium-review-panel-container">

			<div style="height: 50px;width: 100%;"></div>
			
			<img id="streamium-slider-poster" class="img-responsive" src="" />

			<video id="streamium-slider" class="video-js vjs-default-skin vjs-streamium vjs-streamium-sidebar vjs-16-9" preload="auto" controls="true" playsinline="true" data-setup='{"autoplay":false,"muted":true,"loop":true}'></video>

			<div class="streamium-review-panel-content">
				
			</div> <!-- streamium-review-panel-content -->

		</div> <!-- streamium-review-panel-container -->

	</div> <!-- streamium-review-panel -->

	<?php wp_footer(); ?>

	<?php

		// Get the tracking code if it exists
		$trackingCode = get_theme_mod( 'streamium_google_analytics_section_code', '');

		if(empty($trackingCode)){
			
			echo '<!-- Google Tracking Code Below You Can Set This In The Customizer -->';

		}else{ ?>

			<!-- Google Tracking Code Below You Can Set This In The Customizer -->
			<script>
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

			  ga('create', '<?php echo $trackingCode; ?>', 'auto');
			  ga('send', 'pageview');

			</script>

		<?php } ?> 	
</body>
</html>