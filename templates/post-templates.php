<?php 

function template_post($row) {
	$timezone = $_GET['timezone'];
	$f = new Functions();
	$like = $f->getLike($row['id']);
	$likes = $f->getLikes($row['id']);
	?>
	<li id="post_<?php echo $row['id']; ?>">
		<div class="avatar"><img src="<?php echo $f->userLink($row['poster']); ?>/photos/<?php echo $row['mainimgid']; ?>_small.jpg" /></div>
		<a href="/<?php echo $row['poster']; ?>" class="poster"><?php echo $row['poster']; ?></a> <?php if ($row['location'] != '') { ?><span class="location">( <?php echo $row['location']; ?> )</span><?php } ?>
		<div class="content">
			<?php echo $row['details']; ?>
			<?php 
			if ($row['postimg'] != 0) {
				$imgLink = $f->userLink($row['poster']).'/posts/'.$row['postimg'].'_medium.jpg'
				?>
				<div class="img-mask">
					<img class="post-image" src="<?php echo $imgLink; ?>" />
				</div>
				<?php
			}
			?>
		</div>
		
		<div class="like-bar">
			<?php if ($like == 0) { ?>
			<a href="#" data-id="<?php echo $row['id']; ?>" class="like-button" href="#">Îmi place</a>
			<?php } else { ?>
			<a href="#" data-id="<?php echo $row['id']; ?>" class="unlike-button" href="#">Nu-mi place</a>
			<?php } ?>
			(<span class="like-count"><?php echo $likes; ?></span>)
		</div>
		
		<div class="time">
			<?php echo strftime("%A, %d %B, %Y @ %H:%M %S",strtotime($row['dtm'].$timezone."hours")); ?>
		</div>
	</li>
	<?php
}

function showMap () {
	$f = new Functions();
	$sql = "SELECT `username`, `location`, `mainimgid`, `lat`, `long` FROM signup 
			WHERE `lat` != '' 
			AND `long` != '' 
			ORDER BY lastlogin DESC;";
	$res = mysql_query($sql);
	$counter = mysql_num_rows($res);
	$data = array("count" => $counter, "stores" => array());
	$tmpData = '';
	while ($row = mysql_fetch_assoc($res)) {
		$imglink = $f->userLink($row['username']).'/photos/'.$row['mainimgid'].'_small.jpg';
		//var_dump($row);
		$profilelink = '<img class="map-card-image" src="'.$imglink.'" />';
		if (_USERNAME != '') {
			$profilelink = '<a href="/'.$row['username'].'" class="map-card-image"><img src="'.$imglink.'" /></a>';
		}
		$tmpData = array(
				"username" => $row['username'],
				"latitude" => $row['lat'],
				"longitude" => $row['long'],
				"img" => $profilelink,
				"location" => $row['location']
		);
		array_push($data["stores"],$tmpData);
	}	
	?>
	<script>
		var data = <?php echo json_encode($data); ?>;
		function initialize() {
			var center = new google.maps.LatLng(37.4419, -122.1419);
			var infoWindow = new google.maps.InfoWindow();

			var map = new google.maps.Map(document.getElementById('map'), {
				zoom: 3,
				center: center,
				mapTypeId: google.maps.MapTypeId.ROADMAP
			});

			var markers = [];
			for (var i = 0; i < data.count; i++) {
				var dataStores = data.stores[i];
				var latLng = new google.maps.LatLng(dataStores.latitude,dataStores.longitude);
				var markerContent = '<div class="map-card-wrapper">'
					+ dataStores.img 
					+ '<div class="map-card">' 
					+ dataStores.username
					+ "<br>" 
					+ dataStores.location 
					+ "</div>" ;
				var marker = new google.maps.Marker({
					position: latLng,
					html: markerContent
				});


				google.maps.event.addListener(marker, 'click', function () {
					//console.log(marker,dataStores);
					infoWindow.setContent(this.html);
					infoWindow.open(map, this);
				});
				
				
				markers.push(marker);

			}
			
			
			var mcOptions = {"zoomOnClick":false};
			var markerCluster = new MarkerClusterer(map, markers, mcOptions);
			
			google.maps.event.addListener(markerCluster, 'clusterclick', function(cluster) {
				var cMarkers = cluster.getMarkers();
				
				var clusterHtml = "";
				console.log(google.maps);
				for (var iM = 0; iM < cMarkers.length; iM++) {
					//console.log(cMarkers[iM].html);
					clusterHtml += "<p>"+cMarkers[iM].html+"</p>";
				}
				var info = new google.maps.MVCObject;
				info.set('position', cluster.center_);
				console.log(clusterHtml);
				infoWindow.close();
				infoWindow.setContent(clusterHtml);
				infoWindow.open(map,info);
				
			});
		}
		google.maps.event.addDomListener(window, 'load', initialize);
	</script>
	<div id="map-container"><div id="map"></div></div>
	<?php
}

?>