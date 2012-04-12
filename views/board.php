<style>
#mata_hari_debug {
	margin-bottom: 5px;
	font-family: 'Courier New', sans-serif;
	width: 85%;
	height: 700px;
	margin: auto;
	background-color: #141414;
	color: #e2e2e2;
	border: 5px solid #ccc;
	text-align: left;
	overflow: auto;
}

#mata_hari_debug #title {
	display: block;
	width: 100%;
	padding: 5px;
	background-color: #ccc;
	color: #f00;
	font-size: 20px;
	font-weight: bold;
	clear: both;
}
.spy-marker {
	padding: 5px;
	border-bottom: 1px solid #3d3d3d;
}
.spy-marker-time {
	padding: 5px;
	border-bottom: 1px solid #3d3d3d;
	padding-bottom: 10px;
	padding-top: 10px;
}
.time {
	color: #ffe13d;
}
.memory {
	color: #89af62;
}
.marker-name {
	color: #7eaccc;
}
.even {

}
.odd {
	background-color: #282828;
}
.meta-info {
	font-weight: bold;
	padding: 5px;
	padding-top: 10px;
	padding-bottom: 10px;
	margin-bottom: 20px;
	border-bottom: 1px dashed #3d3d3d;
	line-height: 20px;
}
</style>

<?php echo $output; ?>