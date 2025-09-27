 <?php
	 header("Content-type: application/vnd-ms-excel");
	 header("Content-Disposition: attachment; filename=DataRekapitulasiTagihanPJU-".$tahun.".xls");
	 header("Pragma: no-cache");
	 header("Expires: 0");
 ?>
 <center><b>Data Rekapitulasi Tagihan PJU</b></center><br/>
 <div>Tahun : <?=$tahun?></div> 
 <div>Kecamatan <?=$namaKecamatan?></div><br/><br/>
 <table border="1" width="100%">
      <thead>
           <tr align="center">
				<th rowspan="2">Bulan</th>
				<th colspan="3">Total</th>
			</tr>
			<tr align="center">
				<th>Rekening</th>
				<th>KWh</th>
				<th>Tagihan</th>
			</tr>
      </thead>
      <tbody>
          <?php 
		   if(count($dataRekap) > 0){
				foreach($dataRekap as $key) {
		   ?>
				   <tr>
						<td><?=$key->BLTH?></td>
						<td><?=$key->TOTALREKENING?></td>
						<td><?=$key->TOTALKWH?></td>
						<td><?=$key->TOTALTAGIHAN?></td>
				   </tr>
           <?php
				}
		   } else {
		   ?>
			   <tr>
					<td colspan="4">Tidak ada data yang ditampilkan</td>
			   </tr>
           <?php
			}
		   ?>
      </tbody>
 </table>