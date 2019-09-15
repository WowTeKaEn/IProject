<?php

//fetch.php

require_once "SQLsrvConnect.php";

$query = "SELECT * FROM dbo.Voorwerp WHERE verkoopprijs BETWEEN '".$_POST["minimum_range"]."' AND '".$_POST["maximum_range"]."' ORDER BY verkoopprijs ASC";

$statement = $connect->prepare($query);

$statement->execute();

$result = $statement->fetchAll();

$total_row = $statement->rowCount();

$output = '
<h4 align="center">Total Item - '.$total_row.'</h4>
<div class="row">
';
if($total_row > 0)
{
	foreach($result as $row)
	{
		$output .= '
		<div class="col-md-2">
			<div >
				<img src="images/'.$row["thumbnail"].'" class="img-responsive img-thumnail img-circle" />
				<h4 align="center">'.$row["titel"].'</h4>
				<h3 align="center" class="text-danger">'.$row["verkoopprijs"].'</h3>
				<br />
			</div>
		</div>
		';
	}
}
else
{
	$output .= '
		<h3 align="center">No Product Found</h3>
	';
}

$output .= '
</div>
';

echo $output;

?>