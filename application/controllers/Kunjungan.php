<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Kunjungan extends CI_Controller
{

    public function index()
    {

        if (!$this->session->has_userdata('logged_in')) {
            redirect('auth');
        }

        $data = [
            'title' => 'Data Kunjungan',
            'nav_id' => 'nav_kunjungan',
            'tbody' => $this->list_(),
            'js' => array(
                'plugin/datatables/datatables.min.js',
                'plugin/bs-custom-file-input/bs-custom-file-input.min.js',
            )
        ];

        $this->template->view('VKunjungan', $data);
    }


    private function list_()
    {

        $data = $this->MCore->list_data('kunjungan');

        $no = 1;
        ob_start();
        foreach ($data->result_array() as $value) {

            $status = '<span class="badge badge-primary">Aktif</span>';

            $button = '<button id="btn-detail" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-secondary btn-lg" data-original-title="Assign Petugas">
            <i class="fa fa-sign-in-alt text-secondary"></i>
            <button id="btn-edit" type="button" data-toggle="tooltip" data-id="' . $value['id_kunjungan'] . '" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Edit">
            <i class="fa fa-edit"></i>
        </button>';
            if ($value['status'] == 0) {
                $status = '<span class="badge badge-danger">Tidak Aktif</span>';
                $button .= '
                <button id="btn-aktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Aktifkan">
                    <i class="fa fa-check text-success"></i>
                </button>';
            } else {
                $button .= '
                <button id="btn-nonaktif" data-id="' . $value['id_kunjungan'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Nonaktifkan">
                    <i class="fa fa-times text-danger"></i>
                </button>';
            }
?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $status ?></td>
                <td><?= $value['nama_kunjungan'] ?></td>
                <td><?= $value['nomor_pelanggan'] ?></td>
                <td><?= $value['nomor_meteran'] ?></td>
                <td><?= $value['alamat'] ?></td>
                <td class="text-center">
                    <?= $button; ?>
                </td>
            </tr>
<?php
            $no++;
        }
        $tbody = ob_get_contents();
        ob_clean();
        return $tbody;
    }

    public function edit($id = 0)
    {
        $data = $this->MCore->get_data('kunjungan', 'id_kunjungan = ' . $id);

        echo json_encode($data->row_array());
    }

    public function save()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama_kunjungan', 'Nama Kunjungan', 'trim|required');
        $this->form_validation->set_rules('nomor_pelanggan', 'Nomor Pelanggan', 'trim|required');
        $this->form_validation->set_rules('nomor_meteran', 'Nomor Meteran', 'trim|required');
        $this->form_validation->set_rules('catatan', 'Catatan', 'trim|required');
        $this->form_validation->set_rules('latitude', 'Latitude', 'trim|required');
        $this->form_validation->set_rules('longitude', 'Longitude', 'trim|required');

        $this->form_validation->set_message('required', '{field} tidak boleh kosong.');
        $this->form_validation->set_message('matches', 'Password harus sama.');
        $this->form_validation->set_message('max_length', '{field} melebihi {param} karakter.');

        if ($this->form_validation->run() == FALSE) {
            $errors = $this->form_validation->error_array();
            $arr['status'] = 0;
            $arr['message'] = reset($errors);
            echo json_encode($arr);
            exit();
        }

        $data = array(
            'nama_kunjungan' => $this->input->post('nama_kunjungan'),
            'nomor_pelanggan' => $this->input->post('nomor_pelanggan'),
            'nomor_meteran' => $this->input->post('nomor_meteran'),
            'catatan' => $this->input->post('catatan'),
            'latitude' => $this->input->post('latitude'),
            'longitude' => $this->input->post('longitude')
        );


        if ($id == '') {
            if ($this->MCore->get_data('kunjungan', array('LOWER(nomor_pelanggan)' => strtolower($data['nomor_pelanggan'])))->num_rows() > 0) {
                $arr['status'] = 0;
                $arr['message'] = 'Nomor Pelanggan telah ada. Periksa lagi.';
                echo json_encode($arr);
                exit();
            }

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['nama_kunjungan'] . '_' . time();

                $config['upload_path']          = './upload/foto';
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
                $config['max_size']             = 5000;
                $config['file_name']            = $new_name;
                // $config['max_width']            = 1024;
                // $config['max_height']           = 768;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload("foto")) {
                    $img = array('upload_data' => $this->upload->data());

                    $image = $img['upload_data']['file_name'];
                    //Compress Image
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './upload/foto/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/foto/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_kunjungan'] = config_item('upload') . 'foto/' . $image;
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            $sql = $this->MCore->save_data('kunjungan', $data);
        } else {

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['nama_kunjungan'] . '_' . time();

                $config['upload_path']          = './upload/foto';
                $config['allowed_types']        = 'gif|jpg|png|jpeg';
                $config['max_size']             = 5000;
                $config['file_name']            = $new_name;
                // $config['max_width']            = 1024;
                // $config['max_height']           = 768;

                $this->load->library('upload', $config);
                $this->upload->initialize($config);
                if ($this->upload->do_upload("foto")) {
                    $img = array('upload_data' => $this->upload->data());

                    $image = $img['upload_data']['file_name'];
                    //Compress Image
                    $config['image_library'] = 'gd2';
                    $config['source_image'] = './upload/foto/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/foto/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_kunjungan'] = config_item('upload') . 'foto/' . $image;

                    //delete
                    $img_user = $this->MCore->select_data('foto_kunjungan', 'kunjungan', 'id_kunjungan = ' . $id)->row_array();
                    $exp = explode('/', $img_user['foto_kunjungan']);
                    $file_name = end($exp);

                    $path = FCPATH . 'upload/foto/' . $file_name;
                    if (isset($path)) {
                        unlink($path);
                    }
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            $sql = $this->MCore->save_data('kunjungan', $data, true, array('id_kunjungan' => $id));
        }
        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil disimpan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal disimpan';
        }
        echo json_encode($arr);
    }

    public function aktif($id = 0)
    {
        $sql = $this->MCore->save_data('kunjungan', array('status' => 1), true, array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil diaktifkan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal diaktifkan';
        }
        echo json_encode($arr);
    }

    public function nonaktif($id = 0)
    {
        $sql = $this->MCore->save_data('kunjungan', array('status' => 0), true, array('id_kunjungan' => $id));

        $arr['status'] = $sql;
        if ($sql) {
            $arr['message'] = 'Data berhasil dinonaktifkan';
            $arr['tbody'] = $this->list_();
        } else {
            $arr['message'] = 'Data gagal dinonaktifkan';
        }
        echo json_encode($arr);
    }
}
