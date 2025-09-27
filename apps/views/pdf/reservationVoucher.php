<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="viewport" content="width=3Ddevice-width, initial-scale="1"/>
		<meta charset="UTF-8"/>
	</head>
	<body style="font-size:9pt;font-family:Arial,sans-serif;width:1020px;margin-left:auto;margin-right:auto;">
		<div class="header-div" style="background-color:white;padding-bottom:0.5em;margin-top:5px;margin-bottom:1em;margin-left:auto;margin-right:auto;padding-left: 2%;padding-right: 2%;">
			<table id="header" class="header" style="color:#23415e;width:100%;padding:1em;">
				<tbody>
					<tr>
						<td style="width:10%">
							<img width="60" src="<?=DOMAIN_HTTP_TYPE?>:<?=BASE_URL_ASSETS?>img/logo-color.png" style="max-width:30vw"/>
						</td>
						<td style="width:65%; padding-bottom:8px">
							<h5 style="margin-top:2px; margin-bottom:2px; padding-top:2px; padding-bottom:2px"><?=COMPANY_NAME?></h5>
							<small><?=COMPANY_ADDRESS?></small><br/>
							<small>Mail : <?=COMPANY_EMAIL?></small><br/>
							<small>Phone : <?=COMPANY_PHONE?></small>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<hr class="grey-divider"/>
						</td>
					</tr>
				</tbody>
			</table>
			<table id="header" class="header" style="color:#23415e;width:100%;padding:1em; margin-top:32px; margin-left:30px">
				<tbody>
					<tr>
						<td colspan="2" align="center"><h5 style="margin-top:2px; margin-bottom:2px; padding-top:2px; padding-bottom:2px">EXCURSION VOUCHER</h5></td>
					</tr>
					<tr>
						<td colspan="2" align="center"><h5 style="margin-top:2px; margin-bottom:2px; padding-top:2px; padding-bottom:2px">Code : <?=$VOUCHERCODE?></h5></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr><td colspan="2"> </td></tr>
					<tr><td colspan="2"> </td></tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<td style="width:14%;">Partner</td>
						<td style="width:60%;">: <b><?=$VENDORNAME?></b></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<td style="width:14%;">Guest Name</td>
						<td style="width:60%;">: <?=$GUESTNAME?></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<td style="width:14%;">Service</td>
						<td style="width:60%;">: <?=$SERVICENAME?></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<td style="width:14%;">Date of Service</td>
						<td style="width:60%;">: <?=$SERVICEDATESTR?></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<td style="width:14%;">Number of Traveller</td>
						<td style="width:60%;">: <?=$PAXDETAILS?></td>
					</tr>
					<tr><td colspan="2"> </td></tr>
					<tr>
						<td style="width:14%;">Notes</td>
						<td style="width:60%;">: <?=$NOTES?></td>
					</tr>
				</tbody>
			</table>
			<table style="color:#23415e;width:100%;padding:1em; margin-top:80px;margin-left:120px;">
				<tbody>
					<tr>
						<td style="width:50%;" align="center"><i><b>Payment Guarantee by <?=COMPANY_NAME?></b></i></td>
					</tr>
				</tbody>
			</table>							
		</div>
	</body>
</html>