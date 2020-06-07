<?php


class GruppaMap extends BaseMap
{
	public function arrGruppas(){
        $res = $this->db->query("SELECT group_id AS id, name AS value FROM groups");
        return $res->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>