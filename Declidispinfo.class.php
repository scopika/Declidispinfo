<?php
include_once(realpath(dirname(__FILE__)) . "/../../../classes/PluginsClassiques.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Contenu.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Contenudesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Declidisp.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Declidispdesc.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../classes/Dossier.class.php");

class Declidispinfo extends PluginsClassiques{

    var $declidisp;
    var $contenu;

    const TABLE="declidispinfo";
    var $table=self::TABLE;

    var $bddvars = array("declidisp", "contenu");

    function init(){
        $this->query('CREATE TABLE `' . self::TABLE . '` (
		  `declidisp` int(11) NOT NULL,
		  `contenu` int(11) NOT NULL,
		   UNIQUE  `declidisp` (  `declidisp`)
		) DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;');
    }

    function destroy() {

    }

    function charger($declidisp){
        if(!preg_match('/^[0-9]{1,}$/', $declidisp)) return false;
        return $this->getVars('SELECT * FROM ' . self::TABLE . ' WHERE declidisp=' . $declidisp);
    }

    function boucle($texte, $args){
        $declidisp = lireTag($args, 'declidisp');
        if(!preg_match('/^[0-9]{1,}$/', $declidisp)) $declidisp='';

        $contenu = lireTag($args,'contenu');
        if(!preg_match('/^[0-9]{1,}$/', $contenu)) $contenu='';

        if(empty($declidisp) && empty($contenu)) return;

        $res = '';
        $search = '';
        if($declidisp != '') $search .= ' AND declidisp=' . $declidisp;
        if($contenu != '') $search .= ' AND contenu=' . $contenu;

        $query = 'SELECT * FROM ' . $this->table . ' WHERE 1 ' . $search;
        $resul = CacheBase::getCache()->mysql_query($query, $this->link);
        foreach((array) $resul as $row) {
            $temp = str_replace("#DECLIDISP", $row->declidisp, $texte);
            $temp = str_replace("#CONTENU", $row->contenu, $temp);
            $res .= $temp;
        }
        return $res;
    }

    function moddeclinaison($declinaison) {

        foreach((array) $_POST['declidispinfo'] as $idDeclidisp => $idContenu) {
            if(!preg_match('/^[0-9]{1,}$/', $idDeclidisp)) continue;

            if(!empty($idContenu)){
                if(!preg_match('/^[0-9]{1,}$/', $idContenu)) continue;
                $this->query('REPLACE INTO ' . self::TABLE . ' SET
                    declidisp=' . $idDeclidisp . ',
                    contenu=' . $idContenu
                );
            } else {
                $this->query('DELETE FROM ' . self::TABLE . ' WHERE declidisp=' . $idDeclidisp);
            }
        }
        //exit();
    }

    public function listecontenu($niveau, $declidisp, $depart=0, $pdossier=0){
        $niveau++;
        $tdossier = new Dossier();
        $tdossierdesc = new Dossierdesc();
        $this->charger($declidisp);
        //var_dump($this);

        $query = $this->query('SELECT * FROM ' . Dossier::TABLE .' WHERE parent=' . intval($depart));
        for($i=0; $i<$niveau; $i++) $espace .="&nbsp;&nbsp;&nbsp;";
        while($row=mysql_fetch_object($query)) {
            $tdossierdesc->charger($row->id);
            if($pdossier == $tdossierdesc->dossier) $selected="selected"; else $selected="";

            $rec .= "<option value=\"\" style=\"color:red\" $selected>" . $espace . $tdossierdesc->titre . "</option>";

            $query2 = 'SELECT * FROM '. Contenu::TABLE . ' WHERE dossier=' . $tdossierdesc->dossier;
            $resul2 = mysql_query($query2, $this->link);
            while($row2 = mysql_fetch_object($resul2)){
                $contdesc = new Contenudesc();
                $contdesc->charger($row2->id);


                if($this->contenu == $contdesc->contenu) $selected = "selected=\"selected\"";
                else $selected = "";

                $rec .= "<option value=\"" . $contdesc->contenu . "\" style=\"color:blue\" $selected>" . $espace . "&nbsp;&nbsp;&nbsp;" . $contdesc->titre . "</option>";
            }
            $rec .= $this->listecontenu($niveau++, $caracdisp, $row->id, $pdossier);
        }
        return $rec;
    }
}