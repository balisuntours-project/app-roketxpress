 <?php
	 header("Content-type: application/vnd-ms-excel");
	 header("Content-Disposition: attachment; filename=DataRekapitulasiSurveyPJU.xls");
	 header("Pragma: no-cache");
	 header("Expires: 0");
 ?>
 <center><b>Data Rekapitulasi Survey PJU</b></center><br/>
 <div>Unit PLN : <?=$namaUPJ?></div> 
 <div>Kecamatan <?=$namaKecamatan?></div>
 <div>Petugas <?=$namaPetugas?></div>
 <div>Tanggal <?=$tanggalAwal?> s/d <?=$tanggalAkhir?></div> <br/><br/>
 <table border="1" width="100%">
      <thead>
           <tr>
                <th>No Rekening</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Unit</th>
                <th>Kecamatan</th>
                <th>Petugas</th>
                <th>Mulai Survey</th>
                <th>Akhir Survey</th>
                <th>Total Tiang</th>
           </tr>
      </thead>
      <tbody>
           <?php 
		   foreach($dataRekap as $key) {
		   ?>
           <tr>
                <td><?=$key->IDPELANGGAN?></td>
                <td><?=$key->NAMA?></td>
                <td><?=$key->ALAMAT?></td>
                <td><?=$key->NAMAUPJ?></td>
                <td><?=$key->NAMAKECAMATAN?></td>
                <td><?=$key->NAMAPETUGAS?></td>
                <td><?=$key->TANGGALAWAL?></td>
                <td><?=$key->TANGGALAKHIR?></td>
                <td><?=$key->TOTALTIANG?></td>
           </tr>
           <?php
		   }
		   ?>
      </tbody>
 </table>