<div class="row justify-content-between align-items-center mb-10">
	<div class="col-12 col-lg-auto mb-20">
		<div class="page-heading">
			<h3 class="title">System Settings <span> / List of system setting variables</span></h3>
		</div>
	</div>
	<div class="col-12 col-lg-auto mb-10">
		<div class="page-date-range">
			<button class="button button-success button-sm pull-right" id="btnSaveSystemSettings"><span><i class="fa fa-floppy-o"></i>Save</span></button>
			<button type="button" class="button button-primary button-sm pull-right mr-5" id="btnRefreshSystemSettings"><span><i class="fa fa-refresh"></i>Refresh</span></button>
		</div>
	</div>
</div>
<div class="box">
	<form class="box-body" id="content-systemSettings">
		<div class="alert alert-warning col-12" role="alert">
			<i class="fa fa-info"></i>Please note that any changes made will have a direct impact on the system, both web applications and mobile applications
		</div>
		<div class="row mt-5">
			<table class="table" id="table-settingVariables">
				<tbody>
					<tr>
						<td colspan="4"><h5>Reconfirmation</h5></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName12">-</b></td>
						<td id="variableDescription12">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput12" data-idSetting="12" value="0" onkeypress="maskNumberInput(0,10, 'dataInput12')"></td>
					</tr>
					<tr>
						<td colspan="4"><h5>Date Change Setting</h5></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName5">-</b></td>
						<td id="variableDescription5">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput5" data-idSetting="5" value="0" onkeypress="maskNumberInput(0,24, 'dataInput5')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName6">-</b></td>
						<td id="variableDescription6">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput6" data-idSetting="6" value="0" onkeypress="maskNumberInput(0,24, 'dataInput6')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName7">-</b></td>
						<td id="variableDescription7">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput7" data-idSetting="7" value="0" onkeypress="maskNumberInput(0,24, 'dataInput7')"></td>
					</tr>
					<tr>
						<td colspan="4"><h5>Driver Auto Schedule Variables</h5></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName1">-</b></td>
						<td id="variableDescription1">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput1" data-idSetting="1" value="0" onkeypress="maskNumberInput(0,999, 'dataInput1')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName2">-</b></td>
						<td id="variableDescription2">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput2" data-idSetting="2" value="0" onkeypress="maskNumberInput(0,999, 'dataInput2')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName3">-</b></td>
						<td id="variableDescription3">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput3" data-idSetting="3" value="0" onkeypress="maskNumberInput(0,999, 'dataInput3')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName4">-</b></td>
						<td id="variableDescription4">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput4" data-idSetting="4" value="0" onkeypress="maskNumberInput(0,999, 'dataInput4')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName8">-</b></td>
						<td id="variableDescription8">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput8" data-idSetting="8" value="0" onkeypress="maskNumberInput(0,999, 'dataInput8')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName9">-</b></td>
						<td id="variableDescription9">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput9" data-idSetting="9" value="0" onkeypress="maskNumberInput(0,999, 'dataInput9')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName10">-</b></td>
						<td id="variableDescription10">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput10" data-idSetting="10" value="0" onkeypress="maskNumberInput(0,999, 'dataInput10')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName11">-</b></td>
						<td id="variableDescription11">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput11" data-idSetting="11" value="0" onkeypress="maskNumberInput(0,999, 'dataInput11')"></td>
					</tr>
					<tr>
						<td colspan="4"><h5>Driver Review Bonus & Punishment</h5></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName13">-</b></td>
						<td id="variableDescription13">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput13" data-idSetting="13" value="0" onkeypress="maskNumberInput(1,28, 'dataInput13')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName14">-</b></td>
						<td id="variableDescription14">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput14" data-idSetting="14" value="0" onkeypress="maskNumberInput(0,100000, 'dataInput14')"></td>
					</tr>
					<tr>
						<td width="40"></td>
						<td width="300"><b id="variableName15">-</b></td>
						<td id="variableDescription15">-</td>
						<td width="150"><input type="text" class="form-control maskNumber text-right dataInput" id="dataInput15" data-idSetting="15" value="0" onkeypress="maskNumberInput(0,100000, 'dataInput15')"></td>
					</tr>
				</tbody>
			</table>
		</div>
	</form>
</div>
<script>
	var url = "<?=BASE_URL_ASSETS?>js/page-module/Setting/systemSetting.js";
	$.getScript(url);
</script>