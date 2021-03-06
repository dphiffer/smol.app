<!DOCTYPE html>
<html>
	<head>
		<!--<meta http-equiv="Content-Security-Policy" content="default-src 'self' data: gap: https://vector.mapzen.com 'unsafe-eval'; style-src 'self' 'unsafe-inline'; media-src *">-->
		<meta charset="utf-8">
		<meta name="format-detection" content="telephone=no">
		<meta name="msapplication-tap-highlight" content="no">
		<meta name="viewport" content="user-scalable=no, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width">
		<link rel="stylesheet" type="text/css" href="/lib/font-awesome/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="/lib/leaflet.locatecontrol/dist/L.Control.Locate.css">
		<link rel="stylesheet" type="text/css" href="/lib/leaflet/dist/leaflet.css">
		<link rel="stylesheet" type="text/css" href="/lib/leaflet.locatecontrol/dist/L.Control.Locate.css">
		<link rel="stylesheet" type="text/css" href="/lib/leaflet-geocoder-mapzen/dist/leaflet-geocoder-mapzen.css">
		<link rel="stylesheet" type="text/css" href="/css/smol.css">
		<title>smol map</title>
		<script type="text/javascript" src="/lib/local-forage/dist/localforage.js"></script>
	</head>
	<body<?php if (! empty($_GET['print'])) { echo ' class="print"'; } ?>>
		<div id="app">
			<div id="map"></div>
			<div id="menu">
				<span class="close"><span class="fa fa-close"></span></span>
				<form action="/data.php" method="post" id="edit-map">
					<h1>Edit map</h1>
					<input type="hidden" name="map_id" id="edit-map-id" value="">
					<label>
						Map name
						<input type="text" name="name" id="edit-map-name" value="">
					</label>
					<label class="column">
						Default latitude
						<input type="text" name="name" id="edit-map-latitude" value="">
					</label>
					<label class="column">
						Default longitude
						<input type="text" name="name" id="edit-map-longitude" value="">
					</label>
					<label class="column">
						Default zoom level
						<input type="text" name="name" id="edit-map-zoom" value="">
						<div class="help">ranges from 13 to 16</div>
					</label>
					<div class="clear"></div>
					<p><a href="#" id="edit-map-set-view">Use current map view as the default</a></p>
					<div class="headroom">
						<div class="column wide-column">
							<img id="edit-map-preview" src="/img/preview-refill-black.jpg">
						</div>
						<div class="column">
							<label class="no-headroom">
								Base map
								<select id="edit-map-base">
									<option>refill</option>
									<option>walkabout</option>
								</select>
							</label>
							<div id="edit-map-options-refill" class="edit-map-options">
								<label>
									Map theme
									<select id="edit-map-refill-theme">
										<option>black</option>
										<option>blue</option>
										<option>blue-gray</option>
										<option>brown-orange</option>
										<option>gray</option>
										<option>gray-gold</option>
										<option>high-contrast</option>
										<option>inverted</option>
										<option>pink</option>
										<option>pink-yellow</option>
										<option>purple-green</option>
										<option>sepia</option>
									</select>
								</label>
							</div>
							<div id="edit-map-options-walkabout" class="edit-map-options">
								<div class="checkbox">
									<input type="checkbox" name="walkabout_path" id="edit-map-walkabout-path">
									<label for="edit-map-walkabout-path">
										Show trail overlay
									</label>
								</div>
								<div class="checkbox">
									<input type="checkbox" name="walkabout_bike" id="edit-map-walkabout-bike">
									<label for="edit-map-walkabout-bike">
										Show bike overlay
									</label>
								</div>
							</div>
							<!--<label>
								Map labels
								<select id="edit-map-labels">
									<option>normal</option>
									<option>no-labels</option>
									<option>more-labels</option>
								</select>
							</label>-->
						</div>
						<div class="clear"></div>
					</div>
					<div class="edit-buttons">
						<input type="submit" name="action" class="btn btn-save" value="Save">
						<input type="submit" name="action" class="btn btn-cancel" value="Cancel">
					</div>
					<div class="edit-rsp"></div>
					<div id="edit-map-links">
						<a href="#" id="edit-map-print" target="_blank">Print this map</a>
						<a href="#" class="edit-delete">Delete this map?</a>
					</div>
				</form>
				<form action="/data.php" method="post" id="edit-venue">
					<h1>Edit venue</h1>
					<input type="hidden" name="venue_id" id="edit-venue-id" value="">
					<label>
						Venue name
						<input type="text" name="name" id="edit-venue-name" value="">
					</label>
					<label>
						Venue address
						<input type="text" name="address" id="edit-venue-address" value="">
					</label>
					<label>
						Venue tags
						<div class="help">comma separated: <code>cats, cat cafe, coffee</code></div>
						<input type="text" name="tags" id="edit-venue-tags" value="">
					</label>
					<label for="edit-venue-icon">
						Venue icon
						<div class="help">icons are from <a href="http://fontawesome.io/icons/" target="_blank">FontAwesome</a></div>
					</label>
					<div id="edit-venue-icon-display"><span class="fa"></span></div>
					<select name="icon" id="edit-venue-icon">
						<?php include 'icons.txt'; ?>
					</select>
					<label>
						Venue color
						<div class="help"><a href="https://en.wikipedia.org/wiki/Web_colors" target="_blank">hex color</a> code, for inspiration check out <a href="http://paletton.com" target="_blank">Paletton</a> or <a href="https://color.adobe.com/explore/most-popular/?time=week" target="_blank">Adpbe Colors</a></div>
						<input type="text" name="color" id="edit-venue-color" value="">
					</label>
					<div class="edit-buttons">
						<input type="submit" name="action" class="btn btn-save" value="Save">
						<input type="submit" name="action" class="btn btn-cancel" value="Cancel">
					</div>
					<div class="edit-rsp"></div>
					<a href="#" class="edit-delete">Delete this venue?</a>
				</form>
			</div>
		</div>
		<!--<script type="text/javascript" src="cordova.js"></script>-->
		<script>
			<?php if (! file_exists('config.json')) { ?>
				console.error('no config.json found');
			<?php } else { ?>
				var config = <?php echo trim(file_get_contents('config.json')); ?>;
			<?php } ?>
		</script>
		<script type="text/javascript" src="/lib/jquery/dist/jquery.js"></script>
		<script type="text/javascript" src="/lib/leaflet/dist/leaflet-src.js"></script>
		<script type="text/javascript" src="/lib/tangram/dist/tangram.debug.js"></script>
		<script type="text/javascript" src="/lib/leaflet.locatecontrol/dist/L.Control.Locate.min.js"></script>
		<script type="text/javascript" src="/lib/leaflet-geocoder-mapzen/dist/leaflet-geocoder-mapzen.js"></script>
		<script type="text/javascript" src="/lib/leaflet-hash/leaflet-hash.js"></script>
		<script type="text/javascript" src="/lib/file-saver/FileSaver.js"></script>
		<script type="text/javascript" src="/js/leaflet-add-venue.js"></script>
		<script type="text/javascript" src="/js/slippymap.crosshairs.js"></script>
		<script type="text/javascript" src="/js/smol.js"></script>
	</body>
</html>
