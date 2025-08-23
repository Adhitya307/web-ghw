<?php
namespace App\Models\Rembesan;

use CodeIgniter\Model;

class DataGabunganModel extends Model
{
    protected $table = 't_data_pengukuran';
    protected $primaryKey = 'id';
    protected $DBGroup = 'default';
    protected $returnType = 'array';

    /**
     * Ambil semua data gabungan
     */
    public function getDataGabungan()
    {
        try {
            $builder = $this->db->table('t_data_pengukuran p')
                ->select('p.id as pengukuran_id, 
                          p.tahun, p.bulan, p.periode, p.tanggal, p.tma_waduk, p.curah_hujan,
                          sr.id as sr_id, sr.pengukuran_id as sr_pengukuran_id,
                          sr.sr_1_kode, sr.sr_1_nilai, sr.sr_40_kode, sr.sr_40_nilai,
                          sr.sr_66_kode, sr.sr_66_nilai, sr.sr_68_kode, sr.sr_68_nilai,
                          sr.sr_70_kode, sr.sr_70_nilai, sr.sr_79_kode, sr.sr_79_nilai,
                          sr.sr_81_kode, sr.sr_81_nilai, sr.sr_83_kode, sr.sr_83_nilai,
                          sr.sr_85_kode, sr.sr_85_nilai, sr.sr_92_kode, sr.sr_92_nilai,
                          sr.sr_94_kode, sr.sr_94_nilai, sr.sr_96_kode, sr.sr_96_nilai,
                          sr.sr_98_kode, sr.sr_98_nilai, sr.sr_100_kode, sr.sr_100_nilai,
                          sr.sr_102_kode, sr.sr_102_nilai, sr.sr_104_kode, sr.sr_104_nilai,
                          sr.sr_106_kode, sr.sr_106_nilai,
                          thomson.id as thomson_id, thomson.pengukuran_id as thomson_pengukuran_id,
                          thomson.a1_r, thomson.a1_l, thomson.b1, thomson.b3, thomson.b5,
                          bocoran.id as bocoran_id, bocoran.pengukuran_id as bocoran_pengukuran_id,
                          bocoran.elv_624_t1, bocoran.elv_624_t1_kode, 
                          bocoran.elv_615_t2, bocoran.elv_615_t2_kode,
                          bocoran.pipa_p1, bocoran.pipa_p1_kode,
                          ambang.id as ambang_id, ambang.tma as ambang_tma,
                          ambang.ambang_a1, ambang.ambang_b3, ambang.ambang_sr, ambang.ambang_b5')
                ->join('t_sr sr', 'sr.pengukuran_id = p.id', 'left')
                ->join('t_thomson_weir thomson', 'thomson.pengukuran_id = p.id', 'left')
                ->join('t_bocoran_baru bocoran', 'bocoran.pengukuran_id = p.id', 'left')
                ->join('t_ambang_batas ambang', 'ROUND(ambang.tma) = ROUND(p.tma_waduk)', 'left')
                ->orderBy('p.tanggal', 'DESC')
                ->orderBy('p.id', 'DESC');

            $results = $builder->get()->getResultArray();
            $final = [];

            foreach ($results as $row) {
                $final[] = $this->mapRow($row);
            }

            return $final;

        } catch (\Exception $e) {
            log_message('error', 'Error getDataGabungan: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * Ambil data gabungan berdasarkan pengukuran_id
     */
    public function getDataById($pengukuran_id)
    {
        try {
            $builder = $this->db->table('t_data_pengukuran p')
                ->select('p.id as pengukuran_id, 
                          p.tahun, p.bulan, p.periode, p.tanggal, p.tma_waduk, p.curah_hujan,
                          sr.id as sr_id, sr.pengukuran_id as sr_pengukuran_id,
                          sr.sr_1_kode, sr.sr_1_nilai, sr.sr_40_kode, sr.sr_40_nilai,
                          sr.sr_66_kode, sr.sr_66_nilai, sr.sr_68_kode, sr.sr_68_nilai,
                          sr.sr_70_kode, sr.sr_70_nilai, sr.sr_79_kode, sr.sr_79_nilai,
                          sr.sr_81_kode, sr.sr_81_nilai, sr.sr_83_kode, sr.sr_83_nilai,
                          sr.sr_85_kode, sr.sr_85_nilai, sr.sr_92_kode, sr.sr_92_nilai,
                          sr.sr_94_kode, sr.sr_94_nilai, sr.sr_96_kode, sr.sr_96_nilai,
                          sr.sr_98_kode, sr.sr_98_nilai, sr.sr_100_kode, sr.sr_100_nilai,
                          sr.sr_102_kode, sr.sr_102_nilai, sr.sr_104_kode, sr.sr_104_nilai,
                          sr.sr_106_kode, sr.sr_106_nilai,
                          thomson.id as thomson_id, thomson.pengukuran_id as thomson_pengukuran_id,
                          thomson.a1_r, thomson.a1_l, thomson.b1, thomson.b3, thomson.b5,
                          bocoran.id as bocoran_id, bocoran.pengukuran_id as bocoran_pengukuran_id,
                          bocoran.elv_624_t1, bocoran.elv_624_t1_kode, 
                          bocoran.elv_615_t2, bocoran.elv_615_t2_kode,
                          bocoran.pipa_p1, bocoran.pipa_p1_kode,
                          ambang.id as ambang_id, ambang.tma as ambang_tma,
                          ambang.ambang_a1, ambang.ambang_b3, ambang.ambang_sr, ambang.ambang_b5')
                ->join('t_sr sr', 'sr.pengukuran_id = p.id', 'left')
                ->join('t_thomson_weir thomson', 'thomson.pengukuran_id = p.id', 'left')
                ->join('t_bocoran_baru bocoran', 'bocoran.pengukuran_id = p.id', 'left')
                ->join('t_ambang_batas ambang', 'ROUND(ambang.tma) = ROUND(p.tma_waduk)', 'left')
                ->where('p.id', $pengukuran_id);

            $row = $builder->get()->getRowArray();
            
            if (!$row) {
                log_message('debug', "No data found for pengukuran_id: {$pengukuran_id}");
                return null;
            }

            return $this->mapRow($row);

        } catch (\Exception $e) {
            log_message('error', 'Error getDataById: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Debug method untuk melihat query dan data raw
     */
    public function getDataByIdDebug($pengukuran_id)
    {
        $builder = $this->db->table('t_data_pengukuran p')
            ->select('p.id as pengukuran_id, p.tma_waduk,
                     ambang.tma as ambang_tma, ambang.ambang_a1')
            ->join('t_ambang_batas ambang', 'ROUND(ambang.tma) = ROUND(p.tma_waduk)', 'left')
            ->where('p.id', $pengukuran_id);

        $query = $this->db->getLastQuery();
        $result = $builder->get()->getRowArray();

        return [
            'query' => $query,
            'result' => $result
        ];
    }

    /**
     * Mapping row database ke array final
     */
    private function mapRow(array $row)
    {
        return [
            'pengukuran_id' => $row['pengukuran_id'] ?? null,
            'pengukuran' => [
                'tahun'       => $row['tahun'] ?? null,
                'bulan'       => $row['bulan'] ?? null,
                'periode'     => $row['periode'] ?? null,
                'tanggal'     => $row['tanggal'] ?? null,
                'tma_waduk'   => isset($row['tma_waduk']) ? (float)$row['tma_waduk'] : null,
                'curah_hujan' => isset($row['curah_hujan']) ? (float)$row['curah_hujan'] : null,
            ],
            'thomson' => [
                'a1_r' => isset($row['a1_r']) ? (float)$row['a1_r'] : null,
                'a1_l' => isset($row['a1_l']) ? (float)$row['a1_l'] : null,
                'b1'   => isset($row['b1']) ? (float)$row['b1'] : null,
                'b3'   => isset($row['b3']) ? (float)$row['b3'] : null,
                'b5'   => isset($row['b5']) ? (float)$row['b5'] : null,
            ],
            'bocoran' => [
                'elv_624_t1'      => isset($row['elv_624_t1']) ? (float)$row['elv_624_t1'] : null,
                'elv_624_t1_kode' => $row['elv_624_t1_kode'] ?? null,
                'elv_615_t2'      => isset($row['elv_615_t2']) ? (float)$row['elv_615_t2'] : null,
                'elv_615_t2_kode' => $row['elv_615_t2_kode'] ?? null,
                'pipa_p1'         => isset($row['pipa_p1']) ? (float)$row['pipa_p1'] : null,
                'pipa_p1_kode'    => $row['pipa_p1_kode'] ?? null,
            ],
            'sr' => [
                'sr_1_kode'   => $row['sr_1_kode'] ?? null,
                'sr_1_nilai'  => isset($row['sr_1_nilai']) ? (float)$row['sr_1_nilai'] : null,
                'sr_40_kode'  => $row['sr_40_kode'] ?? null,
                'sr_40_nilai' => isset($row['sr_40_nilai']) ? (float)$row['sr_40_nilai'] : null,
                'sr_66_kode'  => $row['sr_66_kode'] ?? null,
                'sr_66_nilai' => isset($row['sr_66_nilai']) ? (float)$row['sr_66_nilai'] : null,
                'sr_68_kode'  => $row['sr_68_kode'] ?? null,
                'sr_68_nilai' => isset($row['sr_68_nilai']) ? (float)$row['sr_68_nilai'] : null,
                'sr_70_kode'  => $row['sr_70_kode'] ?? null,
                'sr_70_nilai' => isset($row['sr_70_nilai']) ? (float)$row['sr_70_nilai'] : null,
                'sr_79_kode'  => $row['sr_79_kode'] ?? null,
                'sr_79_nilai' => isset($row['sr_79_nilai']) ? (float)$row['sr_79_nilai'] : null,
                'sr_81_kode'  => $row['sr_81_kode'] ?? null,
                'sr_81_nilai' => isset($row['sr_81_nilai']) ? (float)$row['sr_81_nilai'] : null,
                'sr_83_kode'  => $row['sr_83_kode'] ?? null,
                'sr_83_nilai' => isset($row['sr_83_nilai']) ? (float)$row['sr_83_nilai'] : null,
                'sr_85_kode'  => $row['sr_85_kode'] ?? null,
                'sr_85_nilai' => isset($row['sr_85_nilai']) ? (float)$row['sr_85_nilai'] : null,
                'sr_92_kode'  => $row['sr_92_kode'] ?? null,
                'sr_92_nilai' => isset($row['sr_92_nilai']) ? (float)$row['sr_92_nilai'] : null,
                'sr_94_kode'  => $row['sr_94_kode'] ?? null,
                'sr_94_nilai' => isset($row['sr_94_nilai']) ? (float)$row['sr_94_nilai'] : null,
                'sr_96_kode'  => $row['sr_96_kode'] ?? null,
                'sr_96_nilai' => isset($row['sr_96_nilai']) ? (float)$row['sr_96_nilai'] : null,
                'sr_98_kode'  => $row['sr_98_kode'] ?? null,
                'sr_98_nilai' => isset($row['sr_98_nilai']) ? (float)$row['sr_98_nilai'] : null,
                'sr_100_kode' => $row['sr_100_kode'] ?? null,
                'sr_100_nilai'=> isset($row['sr_100_nilai']) ? (float)$row['sr_100_nilai'] : null,
                'sr_102_kode' => $row['sr_102_kode'] ?? null,
                'sr_102_nilai'=> isset($row['sr_102_nilai']) ? (float)$row['sr_102_nilai'] : null,
                'sr_104_kode' => $row['sr_104_kode'] ?? null,
                'sr_104_nilai'=> isset($row['sr_104_nilai']) ? (float)$row['sr_104_nilai'] : null,
                'sr_106_kode' => $row['sr_106_kode'] ?? null,
                'sr_106_nilai'=> isset($row['sr_106_nilai']) ? (float)$row['sr_106_nilai'] : null,
            ],
            'ambang' => [
                'tma'        => isset($row['ambang_tma']) ? (float)$row['ambang_tma'] : null,
                'ambang_a1'  => isset($row['ambang_a1']) ? (float)$row['ambang_a1'] : null,
                'ambang_b3'  => isset($row['ambang_b3']) ? (float)$row['ambang_b3'] : null,
                'ambang_sr'  => isset($row['ambang_sr']) ? (float)$row['ambang_sr'] : null,
                'ambang_b5'  => isset($row['ambang_b5']) ? (float)$row['ambang_b5'] : null,
            ]
        ];
    }
}
