<?php
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/authplugins.php");
autorisation("declidispinfo");
if(!preg_match('/^[0-9]{1,}$/', $_GET['id'])) exit('Mauvais identifiant');
?>
<script type="text/javascript">
/*$('.declidispinfo').change(function() {


});

function modif_caracdispinfo(caracdisp, contenu){
	$.ajax({
	   type:'GET',
       url:'../client/plugins/declidispinfo/ajax.php',
       data:'action=modifier&contenu=' + contenu + '&declidisp='+ declidisp
    });
}*/
</script>

<?php
include_once(realpath(dirname(__FILE__)) . "/Declidispinfo.class.php");
include_once(realpath(dirname(__FILE__)) . "/../../../fonctions/divers.php");
?>
<div class="flottant">
    <div class="entete_liste_config">
        <div class="titre">ASSOCIER DES CONTENUS AUX VALEURS</div>
    </div>

    <div class="blocs_pliants_prod">
        <ul id="contenuassoc_liste">
        <?php
        $declidisp = new Declidisp();

        $resul = CacheBase::getCache()->mysql_query('SELECT id FROM '. Declidisp::TABLE . ' WHERE declinaison=' . $_GET['id'], $declidisp->link);
        $i=0;
        foreach((array) $resul as $row) {
        	$declidispdesc = new Declidispdesc();
        	$declidispdesc->charger_declidisp($row->id);

        	if(!($i%2)) $fond="claire";
        	else $fond="fonce";
        	$i++;
            ?>
            <li class="<?php echo $fond; ?>">
				<div class="cellule" style="width:250px;">
				    <?php echo $declidispdesc->titre; ?>
                </div>
				<div class="cellule" style="width:320px;">
					<select class="form declidispinfo" name="declidispinfo[<?php echo $declidispdesc->declidisp; ?>]" style="width: 320px;">
	      			<option value="">&nbsp;</option>
				    <?php
				    $cdispinfo = new Declidispinfo();
				    echo $cdispinfo->listecontenu(1, $declidispdesc->declidisp);
					?>
		 			</select>
		 		</div>
	       </li>
            <?php
	   } ?>
       </ul>
    </div>
</div>