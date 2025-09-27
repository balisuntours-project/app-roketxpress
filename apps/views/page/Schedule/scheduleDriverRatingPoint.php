<script>
	var thisMonth	=	"<?=$thisMonth?>";
</script>
<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">Driver Rating & Point<span>/ List of driver rank based on rating and point</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-warning button-sm pull-right" type="button" id="btnSettingRating" data-toggle="modal" data-target="#editor-modal-settingRating">
				<span><i class="fa fa-list"></i>Setting Rating</span>
			</button>
			<button class="button button-primary button-sm pull-right" type="button" id="btnOpenInputRating" onclick="showInputRating()">
				<span><i class="fa fa-edit"></i>Input Rating</span>
			</button>
			<button class="button button-primary button-sm pull-right d-none" type="button" id="btnInputAuto" data-toggle="modal" data-target="#editor-modal-inputAuto">
				<span><i class="fa fa-magic"></i>Input Auto</span>
			</button>
			<button class="button button-warning button-sm pull-right d-none" type="button" id="btnCloseInputRating">
				<span><i class="fa fa-arrow-circle-left"></i>Close</span>
			</button>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideLeft show" id="slideContainerLeft">
	<ul class="nav nav-tabs mb-15" id="tabsPanel">
		<li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#recapPerDriverTab"><i class="fa fa-id-card-o"></i> Recap per Driver</a></li>
		<li class="nav-item"><a class="nav-link" data-toggle="tab" href="#ratingCalendarTab"><i class="fa fa-calendar"></i> Rating Calendar</a></li>
	</ul>
	<div class="tab-content">
		<div class="tab-pane fade show active" id="recapPerDriverTab">
			<div class="box mb-10">
				<div class="box-body">
					<form class="row mt-10" id="formDriverSearch">
						<div class="col-sm-12">
							<div class="form-group">
								<label for="searchKeyword" class="control-label">Type something to search driver data</label>
								<input type="text" class="form-control mb-10" id="searchKeyword" name="searchKeyword" value="">
							</div>
						</div>
					</form>
				</div>
			</div>
			<div class="box">
				<div class="box-body">
					<div class="row mt-5">
						<div class="col-lg-12 col-sm-12 mb-10">
							<span id="tableDataCount"></span>
							<button class="button button-primary button-sm pull-right" type="button" id="btnOpenRefreshPoint" onclick="refreshDriverPoint()">
								<span><i class="fa fa-refresh"></i>Refresh Point</span>
							</button>
						</div>
					</div>
					<div class="row mt-5 responsive-table-container">
						<table class="table" id="table-dataDriver">
							<tbody></tbody>
						</table>
					</div>
					<div class="row mt-5">
						<div class="col-sm-12 mb-5">
							<ul class="pagination" id="tablePagination"></ul>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="tab-pane fade" id="ratingCalendarTab">
			<div class="box mb-10">
				<div class="box-body">
					<div class="row">
						<div class="col-lg-2 col-sm-6">
							<div class="form-group">
								<label for="optionMonth" class="control-label">Period</label>
								<select class="form-control" id="optionMonth" name="optionMonth"></select>
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="form-group">
								<label for="optionYear" class="control-label">.</label>
								<select class="form-control" id="optionYear" name="optionYear"></select>
							</div>
						</div>
						<div class="col-lg-2 col-sm-6">
							<div class="form-group">
								<label for="optionDriverType" class="control-label">Driver Type</label>
								<select id="optionDriverType" name="optionDriverType" class="form-control" option-all="All Driver Type"></select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-6">
							<div class="form-group">
								<label for="optionDriver" class="control-label">Driver</label>
								<select id="optionDriver" name="optionDriver" class="form-control" option-all="All Driver"></select>
							</div>
						</div>
						<div class="col-lg-3 col-sm-12">
							<div class="form-group">
								<label for="optionSourceRatingCalendar" class="control-label">Source</label>
								<select id="optionSourceRatingCalendar" name="optionSourceRatingCalendar" class="form-control" option-all="All Source"></select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="box">
				<div class="box-body">
					<div class="row">
						<div class="col-sm-12">
							<div class="row">
								<div class="col-lg-4 col-sm-12 px-1">
									<label class="adomx-checkbox mt-15">
										<input type="checkbox" id="checkboxShowRank" name="checkboxShowRank" value="1" checked> <i class="icon"></i> <b>Show Rank</b>
									</label>
								</div>
								<div class="col-lg-4 col-sm-12 text-right align-self-center px-1">Order By</div>
								<div class="col-lg-2 col-sm-6 px-1">
									<select id="optionOrderBy" name="optionOrderBy" class="form-control">
										<option value="1">Rank</option>
										<option value="2">Name</option>
									</select>
								</div>
								<div class="col-lg-2 col-sm-6 px-1">
									<select id="optionOrderType" name="optionOrderType" class="form-control">
										<option value="ASC">Asc</option>
										<option value="DESC">Desc</option>
									</select>
								</div>
							</div>
						</div>
					</div>
					<div class="row mt-10 responsive-table-container tableFixHead" style="height:550px">
						<table class="table" id="table-calendarRating">
							<thead class="thead-light">
								<tr id="headerDates">
									<th width="200" onclick="getClipboardRatingCalendarDriverName()">Driver</th>
								</tr>
							</thead>
							<tbody></tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="slideTransition slideContainer slideRight hide" id="slideContainerRight">
	<div class="row py-3 mx-0 mb-1 d-none">
		<div class="col-sm-12 px-0">
			<div class="row py-3 mx-0">
				<div class="col-lg-3 col-sm-12">
					<div class="form-group">
						<label for="dateRating" class="control-label">Date</label>
						<input type="text" class="form-control input-date-single mb-10 text-center" id="dateRating" name="dateRating">
					</div>
				</div>
				<div class="col-lg-9 col-sm-12">
					<div class="form-group">
						<label for="searchKeywordManual" class="control-label">Type something to search driver data</label>
						<input type="text" class="form-control mb-10" id="searchKeywordManual" name="searchKeywordManual" value="">
					</div>
				</div>
			</div>
			<div class="row pt-0 py-3 mx-3 responsive-table-container" id="generatedResultContainer">
				<div class="col-sm-12 px-0 d-none" id="noDataDriverRatingPoint">
					<div class="col-12 mt-40 mb-30 text-center">
						<img src="<?=BASE_URL_ASSETS?>img/no-data.png" width="120px">
						<h5>No Data Found</h5>
						<p>There are no data <b>on the date</b> you have selected</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="editor-modal-driverRatingPoint">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-driverRatingPoint">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-driverRatingPoint">Add New Driver Rating & Point</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12 mb-10">
						<div class="order-details-customer-info">
							<ul class="ml-10 px-1">
								<li> <span>Driver</span> <span id="inputDriverName"></span> </li>
								<li> <span>Date</span> <span id="inputDateRating"></span> </li>
								<li> <span>Last Rating & Point</span> <span id="lastRatingPoint"></span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row form-group required mt-10">
					<div class="col-sm-12 mb-10">
						<label for="optionSource" class="control-label">Source</label>
						<select id="optionSource" name="optionSource" class="form-control form-control-sm"></select>
					</div>
				</div>
				<div class="row form-group required">
					<div class="col-sm-6 mb-10">
						<label for="ratingInput" class="control-label">Rating</label>
						<span id="ratingInput"></span>
					</div>
					<div class="col-sm-6 mb-10">
						<label for="pointInput" class="control-label mb-10">Point</label>
						<b id="pointInput">0</b>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-info mr-auto" id="showPointHistoryModalBtn">
					<span><i class="fa fa-history"></i> Show Point History</span>
				</button>
				<input type="hidden" id="idDriver" name="idDriver" value="">
				<input type="hidden" id="ratingInputHidden" name="ratingInputHidden" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="editor-modal-driverBasicPoint">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-driverBasicPoint">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-driverBasicPoint">Driver Basic Point</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row" style="border-bottom: 1px solid #e0e0e0;">
					<div class="col-sm-12 mb-10">
						<div class="order-details-customer-info">
							<ul class="ml-10 px-1">
								<li> <span>Driver</span> <span id="driverNameBasicPoint"></span> </li>
							</ul>
						</div>
					</div>
				</div>
				<div class="row form-group required mt-10">
					<div class="col-sm-12 mb-10">
						<label for="optionSource" class="control-label">Basic Point</label>
						<div class="input-group">
							<input type="text" class="form-control mb-10" id="basicPointInput" name="basicPointInput" value="0" max-length="3" aria-describedby="suffixPoint" onkeypress="maskNumberInput(0, 999, 'basicPointInput')">
							<div class="input-group-append mb-10">
								<span class="input-group-text" id="suffixPoint">Point</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<input type="hidden" id="idDriverBasicPoint" name="idDriverBasicPoint" value="">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="editor-modal-settingRating">
	<div class="modal-dialog modal-md" role="document">
		<form class="modal-content form-horizontal" id="editor-settingRating">
			<div class="modal-header">
				<h4 class="modal-title" id="editor-title-settingRating">Setting Rating & Point</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<table class="table" id="tableSettingRatingPoint">
					<thead class="thead-light">
						<tr>
							<th width="100">Rating</th>
							<th colspan="2">Point</th>
						</tr>
					</thead>
					<tbody id="tbodySettingRatingPoint"></tbody>
				</table>
			</div>
			<div class="modal-footer">
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Cancel</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modal-ratingPointHistory">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" id="ratingPointHistory">
			<div class="modal-header">
				<h4 class="modal-title" id="title-ratingPointHistory">Rating & Point History : <span id="ratingPointHistoryDriverName"></span></h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row tableFixHead mx-3">
					<table class="table" id="tableRatingPointHistory" style="height: 250px;">
						<thead class="thead-light">
							<tr>
								<th>Source</th>
								<th width="120">Booking Code</th>
								<th width="100" class="text-center">Date</th>
								<th width="80" class="text-right">Rating</th>
								<th width="80" class="text-right">Point</th>
								<th>Input</th>
							</tr>
						</thead>
						<tbody id="tbodyRatingPointHistory"></tbody>
					</table>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="editor-modal-inputAuto">
	<div class="modal-dialog modal-xl" role="document">
		<form class="modal-content form-horizontal" id="editor-inputAuto">
			<div class="modal-header">
				<h4 class="modal-title" id="title-inputAuto">Auto Scan Rating & Point Input</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12">
						<div class="form-group">
							<label for="optionSourceInputAuto" class="control-label">Source</label>
							<select id="optionSourceInputAuto" name="optionSourceInputAuto" class="form-control"></select>
						</div>
					</div>
					<div class="col-sm-12">
						<div class="form-group mb-5">
							<textarea class="form-control mb-10" placeholder="Paste JSON data here then click Scan button" id="jsonDataInputAuto" name="jsonDataInputAuto"></textarea>
						</div>
					</div>
					<div class="col-sm-12 tableFixHead" style="height:300px">
						<table class="table" id="tableInputAuto">
							<thead class="thead-light">
								<tr>
									<th width="100">Date</th>
									<th width="220">Driver</th>
									<th>Review Content</th>
									<th width="90">Booking Code</th>
									<th width="70" class="text-right">Rating</th>
									<th width="70" class="text-right">Point</th>
								</tr>
							</thead>
							<tbody id="tbodyInputAuto"></tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="button button-info mr-auto" id="scanAutoInputButton">Scan</button>
				<button type="submit" class="button button-primary">Save</button>
				<button type="button" class="button button-default" data-dismiss="modal">Close</button>
			</div>
		</form>
	</div>
</div>
<div class="modal fade" id="modalDetailReviewContent">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content form-horizontal" id="editor-inputAuto">
			<div class="modal-header">
				<h4 class="modal-title" id="detailReviewContent-title">-</h4>
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-sm-12 order-details-customer-info mb-5 pb-5" style="border-bottom: 1px solid #e0e0e0;">
						<ul>
							<li> <span>Source - Book Code</span> <span id="detailReview-sourceBookingCode">-</span> </li>
							<li> <span>Driver</span> <span id="detailReview-driverName">-</span> </li>
							<li> <span>Rating Point</span> <span id="detailReview-ratingPoint">-</span> </li>
							<li> <span>Reservation Title</span> <span id="detailReview-reservationTitle">-</span> </li>
						</ul>
					</div>
				</div>
				<div class="row">
					<div class="col-sm-12" id="detailReviewContent-text"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Schedule/scheduleDriverRatingPoint.js?<?=date('YmdHis')?>";
	$.getScript(url);
</script>
<style>
.table td, .table th {
	border : none !important;
}
.rowDriver{
	border: 1px solid #e0e0e0;
	min-height: 120px;
	max-height: 250px;
	overflow: hidden;
}
.author-profile .image {
  width: 130px;
  height: 130px;
  overflow: hidden;
  position: relative;
  border-radius: 50%;
  margin: auto;
  display: -webkit-box;
  display: -webkit-flex;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: center;
  -webkit-justify-content: center;
      -ms-flex-pack: center;
          justify-content: center;
  -webkit-box-align: center;
  -webkit-align-items: center;
      -ms-flex-align: center;
          align-items: center;
  background-color: #f1f1f1;
}

.author-profile .image h2 {
  margin: 0;
  font-weight: 700;
}
</style>