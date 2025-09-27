<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APP_PUBLIC_NAME?></title>

	<link rel="stylesheet" href="<?=DOMAIN_HTTP_TYPE?>:<?=BASE_URL_ASSETS?>css/bootstrap.min.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=DOMAIN_HTTP_TYPE?>:<?=BASE_URL_ASSETS?>css/style.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="<?=DOMAIN_HTTP_TYPE?>:<?=BASE_URL_ASSETS?>css/material-design-iconic-font.min.css" rel="stylesheet" type="text/css">	
  </head>
  <body id="mainbody">
	<div class="main-wrapper mt-15">
		<div class="main-wrapper">
			<div class="content-body m-0 p-0">
				<div id="mainContainer" class="row mx-auto" style="width:80%">
					<div class="col-2 text-center">
						<img src="<?=DOMAIN_HTTP_TYPE?>:<?=BASE_URL_ASSETS?>img/logo-color-2025.png" alt="" height="80px">
					</div>
					<div class="col-4">
						<h4><?=COMPANY_NAME?></h4>
						<small><?=COMPANY_ADDRESS?><br/><?=COMPANY_PHONE?> | <?=COMPANY_EMAIL?></small>
					</div>
					<div class="col-6"></div>
					<div class="col-12 mt-15 mx-1" style="border-top: 2px solid #e0e0e0;"></div>
					<div class="col-6 mt-15">
						<b class="previewFont">INVOICE NO.</b><br/>
						<span class="previewFont previewValue text-right" id="invoiceNumberComposerContainer"><?=$invoiceNumber?></span><br/><br/>
						<b class="previewFont">BILL TO</b><br/>
						<h6><?=$customerName?></h6>
						<span class="previewFont previewValue" id="customerPhoneNumberComposerContainer"><?=$customerContact?></span><br/><br/>
					</div>
					<div class="col-6 mt-15 text-right">
						<b class="previewFont">DATE</b><br/>
						<span class="previewFont previewValue text-right" id="invoiceDateComposerContainer"><?=$invoiceDateStr?></span><br/><br/>
						<b class="previewFont">DUE</b><br/>
						<span class="previewFont previewValue">On Receipt</span><br/>
					</div>
					<div class="col-12 mx-1" style="border-top: 2px solid #e0e0e0;"></div>
					<div class="col-12 mt-5 mx-1 px-0">
						<table border="0" width="100%" cellspacing="0">
							<thead>
								<tr style="border-bottom: 2px solid #e0e0e0;">
									<th class="previewFont">DESCRIPTION</th>
									<th class="previewFont text-right" align="right" width="160">AMOUNT</th>
								</tr>
							</thead>
							<tbody id="invoiceItemBody">
								<?=$itemInvoiceContent?>
							</tbody>
						</table>
					</div>
					<div class="col-12 mt-5 mx-1 px-0">
						<table border="0" width="100%" cellspacing="0">
							<tbody id="invoiceItemBody">
								<tr class="previewFont">
									<td></td>
									<td style="border-bottom: 1px solid #e0e0e0;" width="160"><b>TOTAL</b></td>
									<td style="border-bottom: 1px solid #e0e0e0;" align="right" width="160"><b>Rp. <span id="totalInvoiceItem"><?=$totalItemAmount?></span>,-</b></td>
								</tr>
								<tr class="previewFont">
									<td></td>
									<td style="border-bottom: 2px solid #e0e0e0;" width="160"><b>BALANCE</b></td>
									<td style="border-bottom: 2px solid #e0e0e0;" align="right" width="160"><b>Rp. <span id="totalBalanceInvoice"><?=$totalBalance?></span>,-</h6></td>
								</tr>
								<tr class="previewFont">
									<td></td>
									<td style="border-bottom: 2px solid #e0e0e0;" width="160"><b>DUE</b></td>
									<td style="border-bottom: 2px solid #e0e0e0;" align="right" width="160"><b>Rp. <span id="totalBalanceDueInvoice"><?=$totalInvoiceAmount?></span>,-</b></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-12 mt-10 mx-1">
						<h5 class="mb-5">Payment Instructions</h5>
						<b class="previewFont">BANK TRANSFER</b><br/>
						<?=COMPANY_BANK_NAME?><br/>
						Bank : <?=COMPANY_BANK_PROVIDER?><br/>
						Account Number : <?=COMPANY_BANK_ACCOUNT_NUMBER?>
					</div>
				</div>
			</div>
		</div>
	</div>
  </body>
<style>
table tr td, table tr th {
  padding-top: 8px;
  padding-bottom: 8px;
}
#mainContainer{
	border: 1px solid #e0e0e0;
	padding: 18px;
	border-radius: 6px;
	padding-right: 24px;
}
*,::after,::before{box-sizing:border-box;}
h4,h5{margin-top:0;margin-bottom:.5rem;}
b{font-weight:bolder;}
small{font-size:80%;}
img{vertical-align:middle;border-style:none;}
table{border-collapse:collapse;}
th{text-align:inherit;}
label{display:inline-block;margin-bottom:.5rem;}
button{border-radius:0;}
button:focus{outline:1px dotted;outline:5px auto -webkit-focus-ring-color;}
button,input{margin:0;font-family:inherit;font-size:inherit;line-height:inherit;}
button,input{overflow:visible;}
button{text-transform:none;}
button{-webkit-appearance:button;}
button::-moz-focus-inner{padding:0;border-style:none;}
input[type=radio]{box-sizing:border-box;padding:0;}
h4,h5{margin-bottom:.5rem;font-weight:500;line-height:1.2;}
h4{font-size:1.5rem;}
h5{font-size:1.25rem;}
small{font-size:80%;font-weight:400;}
.row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px;}
.col-1,.col-12,.col-2,.col-3,.col-6,.col-9{position:relative;width:100%;padding-right:15px;padding-left:15px;}
.col-1{-ms-flex:0 0 8.333333%;flex:0 0 8.333333%;max-width:8.333333%;}
.col-2{-ms-flex:0 0 16.666667%;flex:0 0 16.666667%;max-width:16.666667%;}
.col-3{-ms-flex:0 0 25%;flex:0 0 25%;max-width:25%;}
.col-6{-ms-flex:0 0 50%;flex:0 0 50%;max-width:50%;}
.col-9{-ms-flex:0 0 75%;flex:0 0 75%;max-width:75%;}
.col-12{-ms-flex:0 0 100%;flex:0 0 100%;max-width:100%;}
.form-control{display:block;width:100%;height:calc(1.5em + .75rem + 2px);padding:.375rem .75rem;font-size:1rem;font-weight:400;line-height:1.5;color:#495057;background-color:#fff;background-clip:padding-box;border:1px solid #ced4da;border-radius:.25rem;transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out;}
@media (prefers-reduced-motion:reduce){
.form-control{transition:none;}
}
.form-control::-ms-expand{background-color:transparent;border:0;}
.form-control:focus{color:#495057;background-color:#fff;border-color:#80bdff;outline:0;box-shadow:0 0 0 .2rem rgba(0,123,255,.25);}
.form-control::-webkit-input-placeholder{color:#6c757d;opacity:1;}
.form-control::-moz-placeholder{color:#6c757d;opacity:1;}
.form-control:-ms-input-placeholder{color:#6c757d;opacity:1;}
.form-control::-ms-input-placeholder{color:#6c757d;opacity:1;}
.form-control::placeholder{color:#6c757d;opacity:1;}
.form-control:disabled{background-color:#e9ecef;opacity:1;}
.form-control-sm{height:calc(1.5em + .5rem + 2px);padding:.25rem .5rem;font-size:.875rem;line-height:1.5;border-radius:.2rem;}
.mx-1{margin-right:.25rem!important;}
.mx-1{margin-left:.25rem!important;}
.mt-5{margin-top:3rem!important;}
.mb-5{margin-bottom:3rem!important;}
.mx-auto{margin-right:auto!important;}
.mx-auto{margin-left:auto!important;}
.text-right{text-align:right!important;}
.text-center{text-align:center!important;}
@media print{
*,::after,::before{text-shadow:none!important;box-shadow:none!important;}
thead{display:table-header-group;}
img,tr{page-break-inside:avoid;}
}
*,*::after,*::before{-webkit-box-sizing:border-box;box-sizing:border-box;}
h4,h5{font-family:"Open Sans", sans-serif;color:#343434;font-weight:600;margin-top:0;line-height:1.5;}
h4{font-size:18px;}
h5{font-size:16px;}
button{color:inherit;display:inline-block;line-height:inherit;text-decoration:none;cursor:pointer;}
button,img,input{-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s;}
*:focus{outline:none!important;}
button{cursor:pointer;}
img{max-width:100%;}
input::-webkit-input-placeholder{opacity:1;}
input:-moz-placeholder{opacity:1;}
input::-moz-placeholder{opacity:1;}
input:-ms-input-placeholder{opacity:1;}
.button{display:inline-block;background-color:#eff8fe;border-color:#eff8fe;color:#666666;border-radius:4px;text-transform:capitalize;font-size:15px;line-height:24px;padding:7px 20px;border-width:1px;border-style:solid;margin-bottom:5px;margin-right:2px;}
.button:last-child{margin-right:0;}
.button i{font-size:18px;line-height:24px;margin-right:6px;float:left;}
.button span{display:inline-block;}
.button:focus{-webkit-box-shadow:none;box-shadow:none;}
.button:hover{background-color:#a7d9fa;border-color:#a7d9fa;color:#666666;}
.button-sm{font-size:13px;padding:3px 15px;}
.button-sm i{font-size:16px;margin-right:6px;}
.button-primary{background-color:#428bfa;border-color:#428bfa;color:#ffffff;}
.button-primary:hover{background-color:#297cf9;border-color:#297cf9;color:#ffffff;}
label{display:block;line-height:1;margin-bottom:10px;}
label:last-child{margin-bottom:0;}
.form-control{width:100%;background-color:#ffffff;border:1px solid #dddddd;border-radius:4px;font-family:"Open Sans", sans-serif;font-size:13px;line-height:24px;padding:10px 20px;color:#666666;height:auto;}
.form-control:focus{-webkit-box-shadow:none;box-shadow:none;border-color:#428bfa;}
.form-control[disabled]{background-color:transparent;border-color:#efefef;color:#cccccc;}
.form-control[disabled]::-webkit-input-placeholder{color:#cccccc;}
.form-control[disabled]:-moz-placeholder{color:#cccccc;}
.form-control[disabled]::-moz-placeholder{color:#cccccc;}
.form-control[disabled]:-ms-input-placeholder{color:#cccccc;}
.form-control.form-control-sm{padding:5px 15px;font-size:12px;}
input[type="radio"]{margin-right:5px;position:relative;top:1px;}
.adomx-radio-2{display:block;position:relative;padding-left:25px;line-height:18px;margin:0;cursor:default;}
.adomx-radio-2 input{position:absolute;left:0;top:50%;-webkit-transform:translateY(-50%);-ms-transform:translateY(-50%);transform:translateY(-50%);opacity:0;width:18px;height:18px;visibility:hidden;}
.adomx-radio-2 input:checked + .icon{background-color:#428bfa;}
.adomx-radio-2 input:checked + .icon::before{-webkit-transform:scale(1);-ms-transform:scale(1);transform:scale(1);opacity:1;}
.adomx-radio-2 .icon{width:18px;height:18px;display:block;border-radius:50%;position:absolute;left:0;top:50%;-webkit-transform:translateY(-50%);-ms-transform:translateY(-50%);transform:translateY(-50%);background-color:#cccccc;-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s;}
.adomx-radio-2 .icon::before{content:"";display:block;border-radius:50%;position:absolute;left:5px;top:5px;width:8px;height:8px;background-color:#ffffff;opacity:0;-webkit-transform:scale(3);-ms-transform:scale(3);transform:scale(3);-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s;}
.adomx-checkbox-radio-group{margin:-8px;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;}
.adomx-checkbox-radio-group.inline{-webkit-box-orient:horizontal;-webkit-box-direction:normal;-webkit-flex-direction:row;-ms-flex-direction:row;flex-direction:row;-webkit-flex-wrap:wrap;-ms-flex-wrap:wrap;flex-wrap:wrap;}
.adomx-checkbox-radio-group [class*="adomx-radio"]{margin:8px;}
.mt-5{margin-top:5px!important;}
.mb-5{margin-bottom:5px!important;}
.mt-10{margin-top:10px!important;}
.mb-10{margin-bottom:10px!important;}
.mt-15{margin-top:15px!important;}
.mb-15{margin-bottom:15px!important;}
.fa{display:inline-block;font:normal normal normal 14px/1 FontAwesome;font-size:inherit;text-rendering:auto;-webkit-font-smoothing:antialiased;-moz-osx-font-smoothing:grayscale;}
.fa-plus:before{content:"\f067";}
.previewFont{font-size:13px;}
.previewValue{line-height:14px;vertical-align:top;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtE6F15M.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWvU6F15M.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtU6F15M.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuk6F15M.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWu06F15M.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtk6F15M.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWt06F15M.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuU6F.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtE6F15M.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWvU6F15M.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtU6F15M.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuk6F15M.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWu06F15M.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtk6F15M.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWt06F15M.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuU6F.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtE6F15M.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWvU6F15M.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtU6F15M.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuk6F15M.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWu06F15M.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtk6F15M.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWt06F15M.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuU6F.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtE6F15M.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWvU6F15M.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtU6F15M.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuk6F15M.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWu06F15M.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtk6F15M.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWt06F15M.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuU6F.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtE6F15M.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWvU6F15M.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtU6F15M.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuk6F15M.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWu06F15M.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWtk6F15M.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWt06F15M.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:italic;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memtYaGs126MiZpBA-UFUIcVXSCEkx2cmqvXlWqWuU6F.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSKmu1aB.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSumu1aB.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSOmu1aB.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSymu1aB.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS2mu1aB.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSCmu1aB.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSGmu1aB.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:300;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS-muw.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSKmu1aB.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSumu1aB.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSOmu1aB.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSymu1aB.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS2mu1aB.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSCmu1aB.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSGmu1aB.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:400;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS-muw.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSKmu1aB.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSumu1aB.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSOmu1aB.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSymu1aB.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS2mu1aB.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSCmu1aB.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSGmu1aB.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:600;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS-muw.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSKmu1aB.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSumu1aB.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSOmu1aB.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSymu1aB.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS2mu1aB.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSCmu1aB.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSGmu1aB.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:700;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS-muw.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSKmu1aB.woff2) format('woff2');unicode-range:U+0460-052F, U+1C80-1C88, U+20B4, U+2DE0-2DFF, U+A640-A69F, U+FE2E-FE2F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSumu1aB.woff2) format('woff2');unicode-range:U+0400-045F, U+0490-0491, U+04B0-04B1, U+2116;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSOmu1aB.woff2) format('woff2');unicode-range:U+1F00-1FFF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSymu1aB.woff2) format('woff2');unicode-range:U+0370-03FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS2mu1aB.woff2) format('woff2');unicode-range:U+0590-05FF, U+20AA, U+25CC, U+FB1D-FB4F;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSCmu1aB.woff2) format('woff2');unicode-range:U+0102-0103, U+0110-0111, U+0128-0129, U+0168-0169, U+01A0-01A1, U+01AF-01B0, U+1EA0-1EF9, U+20AB;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTSGmu1aB.woff2) format('woff2');unicode-range:U+0100-024F, U+0259, U+1E00-1EFF, U+2020, U+20A0-20AB, U+20AD-20CF, U+2113, U+2C60-2C7F, U+A720-A7FF;}
@font-face{font-family:'Open Sans';font-style:normal;font-weight:800;font-stretch:100%;src:url(https://fonts.gstatic.com/s/opensans/v27/memvYaGs126MiZpBA-UvWbX2vVnXBbObj2OVTS-muw.woff2) format('woff2');unicode-range:U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;}
@font-face{font-family:'FontAwesome';src:url('<?=BASE_URL_ASSETS?>fonts/fontawesome-webfont.eot?v=4.7.0');src:url('<?=BASE_URL_ASSETS?>fonts/fontawesome-webfont.eot#iefix&v=4.7.0') format('embedded-opentype'),url('<?=BASE_URL_ASSETS?>fonts/fontawesome-webfont.woff2?v=4.7.0') format('woff2'),url('<?=BASE_URL_ASSETS?>fonts/fontawesome-webfont.woff?v=4.7.0') format('woff'),url('<?=BASE_URL_ASSETS?>fonts/fontawesome-webfont.ttf?v=4.7.0') format('truetype'),url('<?=BASE_URL_ASSETS?>fonts/fontawesome-webfont.svg?v=4.7.0#fontawesomeregular') format('svg');font-weight:normal;font-style:normal;}
</style>
</html>