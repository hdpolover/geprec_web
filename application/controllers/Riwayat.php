<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Riwayat extends CI_Controller
{

    public function index()
    {

        if (!$this->session->has_userdata('logged_in')) {
            redirect('auth');
        }

        $data = [
            'title' => 'Data Riwayat',
            'nav_id' => 'nav_pengguna',
            'tbody' => $this->list_(),
            'js' => array(
                'plugin/datatables/datatables.min.js',
            )
        ];

        $this->template->view('VRiwayat', $data);
    }


    private function list_()
    {

        $data_pengguna = $this->MCore->list_data('pengguna');

        $no = 1;
        ob_start();
        foreach ($data_pengguna->result_array() as $value) {

            $status = '<span class="badge badge-primary">Aktif</span>';

            $button = '<button id="btn-edit" type="button" data-toggle="tooltip" data-id="' . $value['id_pengguna'] . '" title="" class="btn btn-link btn-simple-primary btn-lg" data-original-title="Edit">
            <i class="fa fa-edit"></i>
        </button>';
            if ($value['status'] == 0) {
                $status = '<span class="badge badge-danger">Tidak Aktif</span>';
                $button .= '
                <button id="btn-aktif" data-id="' . $value['id_pengguna'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Aktifkan">
                    <i class="fa fa-check text-success"></i>
                </button>';
            } else {
                $button .= '
                <button id="btn-nonaktif" data-id="' . $value['id_pengguna'] . '" type="button" data-toggle="tooltip" title="" class="btn btn-link btn-simple-danger" data-original-title="Nonaktifkan">
                    <i class="fa fa-times text-danger"></i>
                </button>';
            }
            $akun = '<span><i class="fa fa-user"></i> ' . $value['username'] . ' <br> <i class="fa fa-key"></i> ' . $value['password'] . '</span>';

?>
            <tr>
                <td class="text-center"><?= $no ?></td>
                <td><?= $value['nama'] ?></td>
                <td><?= $akun ?></td>
                <td><?= $status ?></td>
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
        $data = $this->MCore->get_data('pengguna', 'id_pengguna = ' . $id);

        echo json_encode($data->row_array());
    }

    public function save()
    {
        $id = $this->input->post('id');

        $this->load->library('form_validation');
        $this->form_validation->set_rules('nama', 'Nama', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]');

        if ($id == '') {
            // untuk create data
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[100]');
        } else {
            // untuk update data ganti password?
            if ($this->input->post('password') != NULL) {
                $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[100]');
            }
        }

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
            'nama' => $this->input->post('nama'),
            'username' => $this->input->post('username')
        );


        if ($id == '') {
            if ($this->MCore->get_data('pengguna', array('LOWER(username)' => strtolower($data['username'])))->num_rows() > 0) {
                $arr['status'] = 0;
                $arr['message'] = 'Username telah digunakan';
                echo json_encode($arr);
                exit();
            }

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['username'] . '_' . time();

                $config['upload_path']          = './upload/profil';
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
                    $config['source_image'] = './upload/profil/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/profil/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_pengguna'] = config_item('upload') . 'profil/' . $image;
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            $data['password'] =  $this->input->post('password');
            $sql = $this->MCore->save_data('pengguna', $data);
        } else {

            if (isset($_FILES['foto']['name']) && !empty($_FILES['foto']['name'])) {
                // UPLOAD FOTO
                $new_name = $data['username'] . '_' . time();

                $config['upload_path']          = './upload/profil';
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
                    $config['source_image'] = './upload/profil/' . $image;
                    $config['create_thumb'] = FALSE;
                    $config['maintain_ratio'] = FALSE;
                    $config['quality'] = '50%';
                    $config['new_image'] = './upload/profil/' . $image;
                    $this->load->library('image_lib', $config);
                    $this->image_lib->resize();

                    $data['foto_pengguna'] = config_item('upload') . 'profil/' . $image;

                    //delete
                    $img_user = $this->MCore->select_data('foto_pengguna', 'pengguna', 'id_pengguna = ' . $id)->row_array();
                    $exp = explode('/', $img_user['foto_pengguna']);
                    $file_name = end($exp);

                    $path = FCPATH . 'upload/profil/' . $file_name;
                    if (isset($path)) {
                        unlink($path);
                    }
                } else {
                    $arr['status'] = 0;
                    $arr['message'] = $this->upload->display_errors();
                    exit();
                }
            }

            //jika ada update password
            if ($this->input->post('password') != NULL) {
                $data['password'] = $this->input->post('password');
            }

            $sql = $this->MCore->save_data('pengguna', $data, true, array('id_pengguna' => $id));
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
        $sql = $this->MCore->save_data('pengguna', array('status' => 1), true, array('id_pengguna' => $id));

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
        $sql = $this->MCore->save_data('pengguna', array('status' => 0), true, array('id_pengguna' => $id));

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
