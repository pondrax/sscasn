<?php
    include("simple_html_dom.php");
		$jenisPengadaan = 3;
		$jenis = [
			1 => 'GURU',
			2 => 'CPNS',
			3 => 'PPPK',
		];
		$allInstansi = file_get_contents("https://data-sscasn.bkn.go.id/spf/getInstansi?jenisPengadaan=$jenisPengadaan");
		$allInstansi = json_decode($allInstansi, true);
		shuffle($allInstansi);
		foreach($allInstansi as $checkInstansi){
      // Run once at a time
			if(Sscasn::where('jenis', $jenis[$jenisPengadaan])->where('kode', $checkInstansi['kode'])->count()==0){
				$kode = $checkInstansi['kode'];
				break;
			}
		}

		$namaInstansi = array_filter($allInstansi, function($d) use($kode){
			return $d['kode'] == $kode;
		});
		$instansi = array_values($namaInstansi)[0]['nama'];

		$url = "https://data-sscasn.bkn.go.id/spf?jenisPengadaan=$jenisPengadaan&instansi=$kode";
		$html = file_get_html($url);
		$scrap = [];
		foreach($html->find('table>tbody>tr') as $tr){
			$scrap[] = [
				'jenis'     	=> $jenis[$jenisPengadaan],
				'kode'      	=> $kode,
				'instansi'      => $instansi,
				'jabatan'   	=> $tr->children(2)->plaintext,
				'lokasi'    	=> $tr->children(3)->plaintext,
				'pendidikan'	=> $tr->children(4)->plaintext,
				'formasi'   	=> $tr->children(5)->plaintext,
				'disabilitas'	=> @$tr->children(6)->plaintext,
				'kebutuhan' 	=> @$tr->children(7)->plaintext,
			];
		}
		if(Sscasn::where('jenis', $jenis[$jenisPengadaan])->where('kode', $checkInstansi['kode'])->count()==0){
			Sscasn::insert($scrap);
		}
		echo "inserted $kode";
