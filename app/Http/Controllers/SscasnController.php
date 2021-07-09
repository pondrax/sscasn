<?php

namespace App\Http\Controllers;

use Laravel\Lumen\Routing\Controller;
use App\Models\Sscasn;
use Illuminate\Http\Request;

class SscasnController extends Controller
{
	function scrap(){
		$maxAttempt = 15;
		$jenisPengadaan = 3;
		$kode = null;
		$jenis = [
			1 => 'GURU',
			2 => 'CPNS',
			3 => 'PPPK',
		];
		$allInstansi = file_get_contents("https://data-sscasn.bkn.go.id/spf/getInstansi?jenisPengadaan=$jenisPengadaan");

		$allInstansi = json_decode($allInstansi, true);
		$allKode = array_map(function($q){
			return $q['kode'];
		},$allInstansi);
		//var_dump($allKode);
		$exists = Sscasn::where('jenis', $jenis[$jenisPengadaan])->groupBy('kode')->pluck('kode')->toArray();
		$notExist = array_diff($allKode,$exists);
		$notExistLimit = array_slice($notExist,0, $maxAttempt);
		var_dump($notExistLimit);
		echo "<br>";
		foreach($notExistLimit as $kode){

			$namaInstansi = array_filter($allInstansi, function($d) use($kode){
				return $d['kode'] == $kode;
			});
			if(!$namaInstansi){
				return "Completed. Total : $total";
			}
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
			if(Sscasn::where('jenis', $jenis[$jenisPengadaan])->where('kode', $kode)->count()==0){
				Sscasn::insert($scrap);
				echo "inserted $kode <br>";
			}

		}
	}
	function scrap2(){
		$maxAttempt = 15;
		$jenisPengadaan = 3;
		$kode = null;
		$jenis = [
			1 => 'GURU',
			2 => 'CPNS',
			3 => 'PPPK',
		];
		$allInstansi = file_get_contents("https://data-sscasn.bkn.go.id/spf/getInstansi?jenisPengadaan=$jenisPengadaan");

		$allInstansi = json_decode($allInstansi, true);
		shuffle($allInstansi);
		echo "Max attempt $maxAttempt<br>";
		foreach($allInstansi as $checkInstansi){
			if(Sscasn::where('jenis', $jenis[$jenisPengadaan])->where('kode', $checkInstansi['kode'])->count()==0){
				$total  = Sscasn::count();
				if($maxAttempt <= 0){
					return "Max Attempt Reached. Total : $total";
				}
				$maxAttempt--;
				$kode = $checkInstansi['kode'];

				$namaInstansi = array_filter($allInstansi, function($d) use($kode){
					return $d['kode'] == $kode;
				});
				if(!$namaInstansi){
					return "Completed. Total : $total";
				}
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
				echo "inserted $kode <br>";
			}else{
				$total = Sscasn::count();
				return "Completed. Total : $total";
			}
		}

	}
}
