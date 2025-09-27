 <?php
	 header("Content-type: application/vnd-ms-excel");
	 header("Content-Disposition: attachment; filename=DataDetailTagihanPJU-".$tahun.".xls");
	 header("Pragma: no-cache");
	 header("Expires: 0");
 ?>
 <center><b>Data Detail Tagihan PJU</b></center><br/>
 <div>Tahun : <?=$tahun?></div> 
 <div>Bulan : <?=$bulan?></div> 
 <div>Kecamatan <?=$namaKecamatan?></div><br/><br/>
 <table border="1" width="100%">
      <thead>
			<tr align="center">
				<th rowspan="2">No Rekening</th>
				<th rowspan="2">Nama</th>
				<th rowspan="2">Alamat</th>
				<th rowspan="2">Unit</th>
				<th rowspan="2">Kecamatan</th>
				<th colspan="2">Stand</th>
				<th colspan="2">Total</th>
			</tr>
			<tr align="center">
				<th>Awal</th>
				<th>Akhir</th>
				<th>KWh</th>
				<th>Tagihan</th>
			</tr>
      </thead>
      <tbody>
          <?php 
			if(count($dataDetail) > 0){
		  
				foreach($dataDetail as $key) {
		   ?>
				   <tr>
						<td><?=$key->IDPELANGGAN?></td>
						<td><?=$key->NAMA?></td>
						<td><?=$key->ALAMAT?></td>
						<td><?=$key->NAMAUPJ?></td>
						<td><?=$key->NAMAKECAMATAN?></td>
						<td><?=$key->STANDAWAL?></td>
						<td><?=$key->STANDAKHIR?></td>
						<td><?=$key->KWH?></td>
						<td><?=$key->RPTAGIHAN?></td>
				   </tr>
           <?php
				}
			} else {
		   ?>
			   <tr>
					<td colspan="9">Tidak ada data yang ditampilkan</td>
			   </tr>
           <?php
			}
		   ?>
      </tbody>
 </table>