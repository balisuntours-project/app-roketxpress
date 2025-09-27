<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?=APP_PUBLIC_NAME?></title>
	<style>
		*,::after,::before{box-sizing:border-box;}
		body{margin:0;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,"Noto Sans",sans-serif,"Apple Color Emoji","Segoe UI Emoji","Segoe UI Symbol","Noto Color Emoji";font-size:1rem;font-weight:400;line-height:1.5;color:#212529;text-align:left;background-color:#fff;}
		h4,h5,h6{margin-top:0;margin-bottom:.5rem;}
		b{font-weight:bolder;}
		small{font-size:80%;}
		img{vertical-align:middle;border-style:none;}
		table{border-collapse:collapse;}
		th{text-align:inherit;}
		h4,h5,h6{margin-bottom:.5rem;font-weight:500;line-height:1.2;}
		h4{font-size:1.5rem;}
		h5{font-size:1.25rem;}
		h6{font-size:1rem;}
		small{font-size:80%;font-weight:400;}
		.row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px;}
		.col-12,.col-2,.col-3,.col-4,.col-9{position:relative;width:100%;padding-right:15px;padding-left:15px;}
		.col-2{-ms-flex:0 0 16.666667%;flex:0 0 16.666667%;max-width:16.666667%;}
		.col-3{-ms-flex:0 0 25%;flex:0 0 25%;max-width:25%;}
		.col-4{-ms-flex:0 0 33.333333%;flex:0 0 33.333333%;max-width:33.333333%;}
		.col-9{-ms-flex:0 0 75%;flex:0 0 75%;max-width:75%;}
		.col-12{-ms-flex:0 0 100%;flex:0 0 100%;max-width:100%;}
		.m-0{margin:0!important;}
		.mx-1{margin-right:.25rem!important;}
		.mx-1{margin-left:.25rem!important;}
		.mt-5{margin-top:3rem!important;}
		.mb-5{margin-bottom:3rem!important;}
		.p-0{padding:0!important;}
		.pr-0,.px-0{padding-right:0!important;}
		.px-0{padding-left:0!important;}
		.py-2{padding-top:.5rem!important;}
		.py-2{padding-bottom:.5rem!important;}
		.mx-auto{margin-right:auto!important;}
		.mx-auto{margin-left:auto!important;}
		.text-right{text-align:right!important;}
		.text-center{text-align:center!important;}
		@media print{
		*,::after,::before{text-shadow:none!important;box-shadow:none!important;}
		thead{display:table-header-group;}
		img,tr{page-break-inside:avoid;}
		body{min-width:992px!important;}
		}
		*,*::after,*::before{-webkit-box-sizing:border-box;box-sizing:border-box;}
		body{height:100%;}
		body{background-color:#ffffff;font-size:13px;line-height:1.75;font-style:normal;font-weight:normal;visibility:visible;font-family:"Open Sans", sans-serif;color:#666666;position:relative;}
		h4,h5,h6{font-family:"Open Sans", sans-serif;color:#343434;font-weight:600;margin-top:0;line-height:1.5;}
		h4{font-size:18px;}
		h5{font-size:16px;}
		h6{font-size:13px;}
		img{-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s;}
		*:focus{outline:none!important;}
		img{max-width:100%;}
		.main-wrapper{float:left;width:100%;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;-ms-flex-direction:column;flex-direction:column;min-height:100%;}
		.content-body{padding:30px;margin-top:80px;-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s;}
		@media only screen and (min-width: 768px) and (max-width: 991px){
		.content-body{margin-top:80px;}
		}
		@media only screen and (max-width: 767px){
		.content-body{margin-top:0;}
		}
		@media only screen and (max-width: 479px){
		.content-body{padding-left:15px;padding-right:15px;}
		}
		/*! CSS Used from: Embedded */
		table tr td,table tr th{padding-top:8px;padding-bottom:8px;}
		#mainContainer{border:1px solid #e0e0e0;padding:18px;border-radius:6px;padding-right:24px;}
		*,::after,::before{box-sizing:border-box;}
		h4,h5{margin-top:0;margin-bottom:.5rem;}
		b{font-weight:bolder;}
		small{font-size:80%;}
		img{vertical-align:middle;border-style:none;}
		table{border-collapse:collapse;}
		th{text-align:inherit;}
		h4,h5{margin-bottom:.5rem;font-weight:500;line-height:1.2;}
		h4{font-size:1.5rem;}
		h5{font-size:1.25rem;}
		small{font-size:80%;font-weight:400;}
		.row{display:-ms-flexbox;display:flex;-ms-flex-wrap:wrap;flex-wrap:wrap;margin-right:-15px;margin-left:-15px;}
		.col-12,.col-2,.col-3,.col-9{position:relative;width:100%;padding-right:15px;padding-left:15px;}
		.col-2{-ms-flex:0 0 16.666667%;flex:0 0 16.666667%;max-width:16.666667%;}
		.col-3{-ms-flex:0 0 25%;flex:0 0 25%;max-width:25%;}
		.col-9{-ms-flex:0 0 75%;flex:0 0 75%;max-width:75%;}
		.col-12{-ms-flex:0 0 100%;flex:0 0 100%;max-width:100%;}
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
		img{-webkit-transition:all 0.3s ease 0s;-o-transition:all 0.3s ease 0s;transition:all 0.3s ease 0s;}
		*:focus{outline:none!important;}
		img{max-width:100%;}
		.mt-5{margin-top:5px!important;}
		.mb-5{margin-bottom:5px!important;}
		.mt-10{margin-top:10px!important;}
		.mb-10{margin-bottom:10px!important;}
		.mt-15{margin-top:15px!important;}
		.mt-30{margin-top:30px!important;}
		.previewFont{font-size:13px;}
		.previewValue{line-height:14px;vertical-align:top;}
		/*! CSS Used fontfaces */
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
	</style>
  </head>
  <body id="mainbody">
	<div class="main-wrapper mt-15">
		<div class="main-wrapper">
			<div class="content-body m-0 p-0" style="background-color: #f6f6f6;padding: 4px;">
				<p style="background-color: #fff; border-radius:6px; width:90%; margin:40px auto; margin-bottom:10px; padding: 20px;">
					Dear <?=$customerName?>,<br/><br/>
					We are pleased to inform you that your invoice <b><?=$invoiceNumber?></b> for the amount of <b>Rp. <span><?=$totalInvoiceAmount?></span></b> has been issued by <?=COMPANY_NAME?>.<br/><br/>
					You may proceed with the payment at your earliest convenience using the available methods. Kindly refer to the invoice below for details.<br/><br/>
					Should you require any assistance or further clarification, please do not hesitate to contact us. We truly appreciate your business and look forward to serving you again.
				</p>
				<div id="mainContainer" class="row" style="background-color: #fff; width:90%; margin:40px auto; margin-bottom:10px; border-radius:6px; padding: 20px;">
					<table border="0" width="100%" cellspacing="0">
						<tr>
							<td width="20%" align="center" valign="top" style="padding-top:16px">
								<img src="<?=DOMAIN_HTTP_TYPE?>:<?=BASE_URL_ASSETS?>img/logo-color-2025.png" alt="" height="80px">
							</td>
							<td width="80%" valign="top" style="padding-left:10px">
								<h4 style="margin-bottom:6px"><?=COMPANY_NAME?></h4>
								<small><?=COMPANY_ADDRESS?><br/><?=COMPANY_PHONE?> | <?=COMPANY_EMAIL?></small>
							</td>
						</tr>
					</table>
					<div class="col-12 mx-1" style="border-top: 2px solid #e0e0e0; margin-top:10px"></div>
					<div class="col-12 mt-15" style="margin-top: 15px">
						<table border="0" width="100%" cellspacing="0">
							<tr>
								<td width="50%" valign="top" style="padding-top:16px">
									<b class="previewFont">INVOICE NO.</b><br/>
									<span class="previewFont previewValue text-right" id="invoiceNumberComposerContainer"><?=$invoiceNumber?></span><br/><br/>
									<b class="previewFont" style="margin-bottom:12px">BILL TO</b><br/>
									<b class="previewFont"><?=$customerName?></b><br/>
									<span class="previewFont previewValue" id="customerPhoneNumberComposerContainer"><?=$customerContact?></span><br/><br/>
								</td>
								<td width="50%" valign="top" align="right" style="padding-top:16px">
									<b class="previewFont">DATE</b><br/>
									<span class="previewFont previewValue text-right" id="invoiceDateComposerContainer"><?=$invoiceDateStr?></span><br/><br/>
									<b class="previewFont">DUE</b><br/>
									<span class="previewFont previewValue">On Receipt</span>
								</td>
							</tr>
						</table>
					</div>
					<div class="col-12 mx-1" style="border-top: 2px solid #e0e0e0;"></div>
					<div class="col-12 mt-5 mx-1 px-0">
						<table border="0" width="100%" cellspacing="0" cellpadding="8px">
							<thead>
								<tr>
									<th style="border-bottom: 2px solid #e0e0e0;" class="previewFont" align="left">DESCRIPTION</th>
									<th style="border-bottom: 2px solid #e0e0e0;" class="previewFont text-right" align="right" width="120">AMOUNT</th>
								</tr>
							</thead>
							<tbody id="invoiceItemBody">
								<?=$itemInvoiceEmail?>
							</tbody>
						</table>
					</div>
					<div class="col-12 mt-5 mx-1 px-0">
						<table border="0" width="100%" cellspacing="0" cellpadding="8px">
							<tbody id="invoiceItemBody">
								<tr class="previewFont">
									<td></td>
									<td style="border-bottom: 1px solid #e0e0e0;" width="80"><b>TOTAL</b></td>
									<td style="border-bottom: 1px solid #e0e0e0;" align="right" width="120"><b>Rp. <span id="totalInvoiceItem"><?=$totalItemAmount?></span></b></td>
								</tr>
								<tr class="previewFont">
									<td></td>
									<td style="border-bottom: 2px solid #e0e0e0;" width="100"><b>BALANCE</b></td>
									<td style="border-bottom: 2px solid #e0e0e0;" align="right" width="120"><b>Rp. <span id="totalBalanceInvoice"><?=$totalBalance?></span></h6></td>
								</tr>
								<tr class="previewFont">
									<td></td>
									<td style="border-bottom: 2px solid #e0e0e0;" width="100"><b>DUE</b></td>
									<td style="border-bottom: 2px solid #e0e0e0;" align="right" width="120"><b>Rp. <span id="totalBalanceDueInvoice"><?=$totalInvoiceAmount?></span></b></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="col-12 mt-30 mx-1" style="margin-top: 20px">
						<b class="mb-5">Payment Instructions</b>
						<b class="previewFont">BANK TRANSFER</b><br/>
						<?=COMPANY_BANK_NAME?><br/>
						Bank : <?=COMPANY_BANK_PROVIDER?><br/>
						Account Number : <?=COMPANY_BANK_ACCOUNT_NUMBER?>
					</div>
				</div>
				<center style="width:75%; margin:0px auto; margin-bottom: 30px;color: #999999;"><small>Bali SUN Tours is a local Bali Tour Company with slogan The Doctor of Bali Tours that provide and organize: Adventure Tours, Fun Things to Do, Sight Seeing Tours, Private Car Driver Hire, and various Attractions Tickets in competitive price.</small></center>
			</div>
		</div>
	</div>
  </body>
</html>