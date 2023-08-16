<?php include "password.php"; ?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Lead Report</title>
<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.19/css/dataTables.bootstrap.min.css">
<link rel="stylesheet" href="lib/flags.css">

    <script src="//ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js" type="text/javascript"></script>
    <script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript" src="//cdn.datatables.net/1.10.19/js/dataTables.bootstrap.min.js"></script>
	<script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
	
<style>
.rg-container {font-family: 'Lato', Helvetica, Arial, sans-serif;font-size: 16px;line-height: 1.4;margin: 0;padding: 1em 0.5em;color: #222;}.rg-header {margin-bottom: 1em;text-align: left;}.rg-header > * {display: block;}.rg-hed {font-weight: bold;font-size: 1.4em;}.rg-dek {font-size: 1em;}.rg-source {margin: 0;font-size: 0.75em;text-align: right;}.rg-source .pre-colon {text-transform: uppercase;}.rg-source .post-colon {font-weight: bold;}
input.form-control{
  max-width: 200px;
}select.form-control{
  max-width: 200px;float:right;
}
.crown {
    display: inline-block;
    width: 16px;
    height: 16px;
    background: url('lib/king.png') no-repeat;
}
@keyframes fadeIn { 
  from { opacity: 0; } 
}
.flicker {
    animation: fadeIn 1s infinite alternate;
}
</style>
</head>
<body>
<?php
date_default_timezone_set('Europe/London');
$now = date('Y-m-d h:i:s');
$reports = "Conversions";

$date = $_GET["date"];
if ($date == 'today') {
    $tanggal = date('Y-m-d', strtotime('today'));
} elseif ($date == 'yesterday') {
    $tanggal = date('Y-m-d', strtotime('yesterday'));
} else{
echo "<meta http-equiv='refresh' content='0;url=?date=today'>";
};

$filename = "temp/".$reports."-".$tanggal.".json";

if (file_exists($filename)) {
	$json = @file_get_contents($filename);		
	$results = json_decode($json,true);
}
$table = array(
	"ID" => "click_id",
	"Traffic" => "traffic_type",
	"Country" => "country",
	"Earning" => "payout",
);
?>


<div class="container" style="width:700px;">
    
<div class="alert alert-warning"><strong>Lead Report: <?php echo $tanggal ?></strong>
</div>

    <div class="panel panel-default searchbox">
        <div class="panel-heading">
    <select class="form-control input-sm" name="select_date" id="select_date">
      <option selected="selected" disabled value="0">Select date</option>
      <option value="today">Today</option>
      <option value="yesterday">Yesterday</option>
      </select>
        
        <input class="form-control input-sm" type="text" id="search" placeholder="Search..."></div>
        <table class="rg-table zebra table" id="userlead">
        <thead><tr><th data-field="n" data-sortable="true" class="no">#</th><th class="click_id">ID</th><th class="traffic_type">Traffic</th><th class="country">Country</th><th class="payout">Earning</th></tr></thead><tbody>
<?php 
	$n = 1;
	if (count($results["conversions"]["conversion"])>0){
		foreach ($results["conversions"]["conversion"] as $hasil){
    $sum += $hasil["payout"];
    if($hasil["click_id"] == "CIKALS"){
        $hasil["click_id"] = '<b>'.$hasil["click_id"].'</b> <span class="crown flicker"></span>';
    }
    echo '<tr class="'.$reports.'">';
    echo '<td class="no">'.$n++.'</td>';
    echo '<td>'.$hasil["click_id"].'</td>';
    echo '<td>'.$hasil["traffic_type"].'</td>';
    echo '<td><span class="flag flag-'.strtolower($hasil["country"]).'"></span> '.$hasil["country"].'</td>';
    echo '<td>'.$hasil["payout"].'</td>';
    echo '</tr>';
		}
		echo '<script>var lastId = '. $n .'</script>'; 
	}
?>	
	</tbody>
	<tfoot>
		<tr>
			<td class='no'></td>
		<?php
			$len = count($table);
			foreach ($table as $k=>$v){
				$s = "";
				if ($k == "Earning"){
					$s = '<span id="sum">'.round($sum,2).'</span>';
				}
				echo '<td class="'.$v.'"><b>'.$s.'</b></td>';					
			}
		?>
		
		</tr>
	</tfoot>
        </table>
		</div><div class="rg-source"><span class="pre-colon">SOURCE</span>: <span class="post-colon">Ngixcard!</span></div>
	</div></div>
	<script>
	$(document).ready(function() {
    var usersTable = $('#userlead').dataTable( {
        "oLanguage": { "sSearch": "" , "sSearchPlaceholder": "Search..."},
		"sPaginationType": "bootstrap",
        "paging": false,
        "ordering": true,
        "info": false,
        "bFilter": true,
        "sDom": "<'top'>l<'searchbox' t>ip",
        "fnRowCallback": function(nRow, aData, iDisplayIndex) {
            var api = this.api(),
                data;
            var intVal = function(i) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '') * 1 : typeof i === 'number' ? i : 0;
            };
            total = api.column(4).data().reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);
            pageTotal = api.column(4, {
                page: 'current'
            }).data().reduce(function(a, b) {
                return intVal(a) + intVal(b);
            }, 0);
            $(api.column(4).footer()).html('<b>$'+pageTotal.toFixed(2)+'</b>');
        }});
          var oTable = $('#userlead').DataTable();

      $('#search').keyup(function () {
      oTable.search($(this).val()).draw();
  });
          $("#select_date").change(function () {
            var date = $("#select_date").val();
            window.location.href = '?date=' + date;
            return true;

        });
    
});
</script>
</body>
</html>