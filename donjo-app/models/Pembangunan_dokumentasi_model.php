<?php

class Pembangunan_dokumentasi_model extends CI_Model
{
    protected $table = 'pembangunan_ref_dokumentasi';

    const ORDER_ABLE = [
        3 => 'd.persentase',
        4 => 'd.keterangan',
        5 => 'd.created_at',
    ];

    public function get_data($id, string $search = '')
    {
        $builder = $this->db->select([
            'd.*',
        ])
        ->from("{$this->table} d")
        ->join('pembangunan p', 'd.id_pembangunan = p.id')
        ->where('d.id_pembangunan', $id);

        if (empty($search)) {
            $condition = $builder;
        } else {
            $condition = $builder->group_start()
                ->like('d.keterangan', $search)
                ->or_like('keterangan', $search)
                ->group_end();
        }

        return $condition;
    }

    public function insert(array $request, $gambar)
    {
        return $this->db->insert($this->table, [
            'id_pembangunan' => $request['id_pembangunan'],
            'gambar'         => $gambar,
            'persentase'     => $request['persentase'],
            'keterangan'     => $request['keterangan'],
            'created_at'     => date('Y-m-d H:i:s'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id_pembangunan, $id, array $request, $gambar)
    {
        return $this->db->where([
            'id_pembangunan' => $id_pembangunan,
            'id'             => $id
        ])
        ->update($this->table, [
            'gambar'     => $gambar,
            'persentase' => $request['persentase'],
            'keterangan' => $request['keterangan'],
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function delete($id)
    {
        return $this->db->where('id', $id)->delete($this->table);
    }

    public function find($id)
    {
        return $this->db->where('id', $id)
            ->get($this->table)
            ->row();
    }
}