<?php 
include ('config.php');
include (_DOCROOT.'/includes/pre-header.php');
include (_DOCROOT.'/includes/header.php');
include (_DOCROOT.'/includes/class.functions.php');
?>
<div class="main-billboard">
Conectați-vă cu prieteni Români!
</div>

<?php 
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
	$tmpData = array(
			"username" => $row['username'],
			"latitude" => $row['lat'],
			"longitude" => $row['long'],
			"img" => '<img class="map-card-image" src="'.$imglink.'" />',
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

<form method="POST" id="signin-form" class="registration-forms" action="/ajax/registration.php?action=trylogin">
	<label>Nume de utilizator:</label>
	<div><input type="text" name="signin-username" id="signin-username" /></div>
	<label>Parolă:</label>
	<div><input type="password" name="signin-password" id="signin-password" /></div>
	<div><button id="signin-button" />Conectați-vă</button></div>
</form>
<form method="POST" id="signup-form" class="registration-forms" action="/ajax/registration.php?action=trysignup">
	<label>E-mail:</label>
	<div><input type="text" name="signup-email" id="signup-email" /></div>
	<label>Nume de utilizator:</label>
	<div><input type="text" name="signup-username" id="signup-username" /></div>
	<label>Parolă:</label>
	<div><input type="password" name="signup-password" id="signup-password" /></div>
	<div><button type="submit" id="signup-button" />Înregistrați-vă</button></div>
</form>
<?php
include (_DOCROOT.'/includes/footer.php');
$db->close();
?>
