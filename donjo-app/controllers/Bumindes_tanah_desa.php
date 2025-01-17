<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * File ini:
 *
 * Controller untuk Modul Bumindes Tanah Desa
 *
 * donjo-app/controllers/Bumindes_tanah_desa.php
 *
 */

/**
 *
 * File ini bagian dari:
 *
 * OpenSID
 *
 * Sistem informasi desa sumber terbuka untuk memajukan desa
 *
 * Aplikasi dan source code ini dirilis berdasarkan lisensi GPL V3
 *
 * Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 *
 * Dengan ini diberikan izin, secara gratis, kepada siapa pun yang mendapatkan salinan
 * dari perangkat lunak ini dan file dokumentasi terkait ("Aplikasi Ini"), untuk diperlakukan
 * tanpa batasan, termasuk hak untuk menggunakan, menyalin, mengubah dan/atau mendistribusikan,
 * asal tunduk pada syarat berikut:

 * Pemberitahuan hak cipta di atas dan pemberitahuan izin ini harus disertakan dalam
 * setiap salinan atau bagian penting Aplikasi Ini. Barang siapa yang menghapus atau menghilangkan
 * pemberitahuan ini melanggar ketentuan lisensi Aplikasi Ini.

 * PERANGKAT LUNAK INI DISEDIAKAN "SEBAGAIMANA ADANYA", TANPA JAMINAN APA PUN, BAIK TERSURAT MAUPUN
 * TERSIRAT. PENULIS ATAU PEMEGANG HAK CIPTA SAMA SEKALI TIDAK BERTANGGUNG JAWAB ATAS KLAIM, KERUSAKAN ATAU
 * KEWAJIBAN APAPUN ATAS PENGGUNAAN ATAU LAINNYA TERKAIT APLIKASI INI.
 *
 * @package OpenSID
 * @author  Tim Pengembang OpenDesa
 * @copyright Hak Cipta 2009 - 2015 Combine Resource Institution (http://lumbungkomunitas.net/)
 * @copyright Hak Cipta 2016 - 2020 Perkumpulan Desa Digital Terbuka (https://opendesa.id)
 * @license http://www.gnu.org/licenses/gpl.html  GPL V3
 * @link  https://github.com/OpenSID/OpenSID
 */

class Bumindes_tanah_desa extends Admin_Controller {

	public function __construct()
	{
		parent::__construct();
		$this->load->model(['tanah_desa_model', 'pamong_model']);
		$this->modul_ini = 301;
		$this->sub_modul_ini = 302;
		$this->set_minsidebar(1);
	}

	public function index()
	{
		if ($this->input->is_ajax_request())
		{
			$start = $this->input->post('start');
			$length = $this->input->post('length');
			$search = $this->input->post('search[value]');
			$order = $this->tanah_desa_model::ORDER_ABLE[$this->input->post('order[0][column]')];
			$dir = $this->input->post('order[0][dir]');

			return $this->output
				->set_content_type('application/json')
				->set_output(json_encode([
					'draw' => $this->input->post('draw'),
					'recordsTotal' => $this->tanah_desa_model->get_data()->count_all_results(),
					'recordsFiltered' => $this->tanah_desa_model->get_data($search)->count_all_results(),
					'data' => $this->tanah_desa_model->get_data($search)->order_by($order, $dir)->limit($length, $start)->get()->result(),
				]));
		}

		$this->render('bumindes/umum/main', [
			'subtitle' => 'Buku Tanah di Desa',
			'selected_nav' => 'tanah',
			'main_content' => 'bumindes/pembangunan/tanah_di_desa/content_tanah_di_desa',
		]);
	}

	public function clear()
	{
		$this->session->filter_tahun = date('Y');
		$this->session->filter_bulan = date('m');

		redirect("bumindes_tanah_desa");
	}

	public function view_tanah_desa($id)
	{
		$data = [
			'main' => $this->tanah_desa_model->view_tanah_desa_by_id($id),
			'main_content' => "bumindes/pembangunan/tanah_di_desa/form_tanah_di_desa",
			'subtitle' => 'Buku Tanah di Desa',
			'selected_nav' => 'tanah',
			'view_mark' => 1,
		];

		$this->render('bumindes/umum/main', $data);
	}

	public function form($id = '')
	{
		$this->redirect_hak_akses('u');
		if ($id)
		{
			$data = [
				'main' => $this->tanah_desa_model->view_tanah_desa_by_id($id),
				'form_action' => site_url("bumindes_tanah_desa/update_tanah_desa/$id"),
			];
		}
		else
		{
			$data = [
				'main' => NULL,
				'form_action' => site_url("bumindes_tanah_desa/add_tanah_desa"),
			];
		}
		
		$data['main_content'] = "bumindes/pembangunan/tanah_di_desa/form_tanah_di_desa";
		$data['penduduk'] = $this->tanah_desa_model->list_penduduk();
		$data['subtitle'] = 'Buku Tanah di Desa';
		$data['selected_nav'] = 'tanah';
		$data['view_mark'] =  0;

		$this->render('bumindes/umum/main', $data);
	}

	public function add_tanah_desa()
	{
		$this->redirect_hak_akses('u');
		$this->tanah_desa_model->add_tanah_desa();
		if ($this->session->success == -1)
		{
			$this->session->dari_internal = true;
			redirect("bumindes_tanah_desa/form");
		}
		else
		{
			redirect("bumindes_tanah_desa/clear");
		}
	}

	public function update_tanah_desa($id)
	{
		$this->redirect_hak_akses('u');
		$this->tanah_desa_model->update_tanah_desa();
		if ($this->session->success == -1)
		{
			$this->session->dari_internal = true;
			redirect("bumindes_tanah_desa/form/$id");
		}
		else
		{
			redirect("bumindes_tanah_desa/clear");
		}
	}

	public function delete_tanah_desa($id)
	{
		$this->redirect_hak_akses('h');
		$this->tanah_desa_model->delete_tanah_desa($id);

		redirect('bumindes_tanah_desa');
	}

	public function cetak_tanah_desa($aksi = '')
	{
		$data = [
			'aksi' => $aksi,
			'config' => $this->header['desa'],
			'pamong_ketahui' => $this->pamong_model->get_ttd(),
			'pamong_ttd' => $this->pamong_model->get_ub(),
			'main' => $this->tanah_desa_model->cetak_tanah_desa(),
			'bulan' => $this->session->filter_bulan,
			'tahun' => $this->session->filter_tahun,
			'tgl_cetak' => $this->input->post('tgl_cetak'),
			'file' => "Buku Tanah di Desa",
			'isi' => "bumindes/pembangunan/tanah_di_desa/tanah_di_desa_cetak",
			'letak_ttd' => ['1', '1', '23'],
		];

		$this->load->view('global/format_cetak', $data);
	}
}
