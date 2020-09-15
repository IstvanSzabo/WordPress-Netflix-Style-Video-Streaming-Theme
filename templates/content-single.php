<?php

	$image = get_the_post_thumbnail_url(get_the_ID(),'full'); 

?>

<video id="player" class="video-js vjs-default-skin vjs-streamium vjs-fluid" controls poster="<?php echo esc_url( $image ); ?>" crossorigin="anonymous" playsinline="true"></video>

<script type="text/javascript" src="https://imasdk.googleapis.com/js/sdkloader/ima3.js"></script>