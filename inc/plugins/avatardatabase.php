<?php

// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
    die("Direct initialization of this file is not allowed.");
}


function avatardatabase_info()
{
    return array(
        "name"			=> "Avatardatenbank",
        "description"	=> "In diesen Plugin kannst du Vorschläge für Avatare einspeichern, sowie ihren Jahrgang, ihre Herkunft und welches Geschlecht sie haben. 
        Du hast du die Möglichkeit sowohl für weiblich, männlich und divers oder nur weiblich und männlich anzeigen zu lassen. 
        Zudem hat der User die Möglichkeit nach bestimmten Punkten zu filtern.",
        "website"		=> "https://github.com/Ales12",
        "author"		=> "Ales",
        "authorsite"	=> "https://github.com/Ales12",
        "version"		=> "1.0",
        "guid" 			=> "",
        "codename"		=> "",
        "compatibility" => "*"
    );
}

function avatardatabase_install()
{

    global  $db, $cache;

    //Datenbank erstellen
    if($db->engine=='mysql'||$db->engine=='mysqli')
    {
        $db->query("CREATE TABLE `".TABLE_PREFIX."avatardatabase` (
          `adb_id` int(10) NOT NULL auto_increment,
          `claimname` varchar(500) CHARACTER SET utf8 NOT NULL,
          `gender` int(10)NOT NULL,
           `year` int(100) NOT NULL,
           `origin` varchar(500) CHARACTER SET utf8  NOT NULL,
	    `hair` varchar(500) CHARACTER SET utf8  NOT NULL,
           `link` varchar(500) CHARACTER SET utf8  NOT NULL,
          PRIMARY KEY (`adb_id`)
        ) ENGINE=MyISAM".$db->build_create_table_collation());

    }

    $db->add_column("usergroups", "canaddavatar", "tinyint NOT NULL default '1'");
    $cache->update_usergroups();

    /*
    * nun kommen die Einstellungen
    */

    $setting_group = array(
        'name' => 'avatardatabase',
        'title' => 'Avatardatenbank',
        'description' => 'Hier kannst du Einstellen, ob du mit oder ohne Divers angezeigt werden möchtest, deine Avatars mit Nachname, Vorname im Profilfeld eingespeichert wird und in welches Profilfeld sie gespeichert werden.',
        'disporder' => 2,
        'isdefault' => 0
    );

    $gid = $db->insert_query("settinggroups", $setting_group);

    $setting_array = array(
        // A text setting
        'avatardatabase_avatarfid' => array(
            'title' => 'Avatar-FID',
            'description' => 'Welche FID hat das Profilfeld für das Avatar:',
            'optionscode' => 'text',
            'value' => 'fid2', // Default
            'disporder' => 1
        ),
        // A select box
        'avatardatabase_claimtype' => array(
            'title' => 'Wie wird die Avatarperson bei euch gespeichert?',
            'description' => 'Wähle aus, wie bei euch die Avatarpersonen im Profilfeld eingetragen werden (Notwendig für den Abgleich):',
            'optionscode' => "select\n0=Vorname Nachname\n1=Nachname, Vorname",
            'value' => 2,
            'disporder' => 2
        ),
        // A yes/no boolean box
        'avatardatabase_gender' => array(
            'title' => 'Tabelle für Divers?',
            'description' => 'Soll auch die Tabelle für Divers angezeigt werden?:',
            'optionscode' => 'yesno',
            'value' => 1,
            'disporder' => 3
        ),
    );

    foreach($setting_array as $name => $setting)
    {
        $setting['name'] = $name;
        $setting['gid'] = $gid;

        $db->insert_query('settings', $setting);
    }

// Don't forget this!
    rebuild_settings();


    // Templates
    $insert_array = array(
        'title'        => 'avatardatabase',
        'template'    => $db->escape_string('	<html>
<head>
<title>{$mybb->settings[\'bbname\']} - {$lang->adb_main}</title>
{$headerinclude}
</head>
<body>
{$header}
<table border="0" cellspacing="{$theme[\'borderwidth\']}" cellpadding="{$theme[\'tablespace\']}" class="tborder">
<tr>
	<td class="thead"><strong>{$lang->adb_main}</strong></td>
</tr>
	<tr>
		<td class="trow"><blockquote>{$lang->adb_infotext}</blockquote>
			{$formular}
		</td>
	</tr>
<tr>
	<td valign="top" align="center">
		<form id="filter_db" method="get" action="misc.php?action=avatardatabase">
			<input type="hidden" name="action" id="action" value="avatardatabase" class="textbox"  /> 
		<table>
			<tr><td class="thead" colspan="4"><strong>{$lang->adb_filter}</strong></td>
			</tr>
			<tr>
				<td class="tcat"><strong>{$lang->adb_initial_filter}</strong></td>
				<td class="tcat"><strong>{$lang->adb_year_filter}</strong></td>
				<td class="tcat"><strong>{$lang->adb_origin_filter}</strong></td>
					<td class="tcat"><strong>{$lang->adb_hair_filter}</strong></td>
			</tr>
			<tr>
				<td class="trow1">
					<select name="letter">
						<option value="%">{$lang->adb_initial_all}</option>
						{$letter_bit}
					</select>
				</td>
							<td class="trow2">
					<select name="year">
							<option value="%">{$lang->adb_initial_all}</option>
						{$year_bit}
					</select>
				</td>
					<td class="trow1">
					<select name="origin">
							<option value="%">{$lang->adb_initial_all}</option>
					{$origin_bit}
					</select>
				</td>
								<td class="trow2">
					<select name="hair">
							<option value="%">{$lang->adb_initial_all}</option>
						<option value="rothaarig">Rothaarig</option>
						<option value="blond">Blond</option>
						<option value="brünett">Brünett</option>
						<option value="bunt">Bunt</option>
						<optione value="Glatze">Glatze</option>
						<optione value="weiß">Weiß</option>
							<optione value="schwarz">Schwarz</option>
					</select>
				</td>
			</tr>
			<tr>
				<td class="trow1" colspan="3" align="center">
					<input type="submit" name="filter_db" value="Filtern" id="submit" class="button">
				</td>
			</tr>
	</table>
		</form><br /> <br />
		<h1>Aktuell sind {$count_female} weibliche, {$count_male} männliche und {$count_divers} diverse Avatarpersonen eingetragen</h1>
		<br />
		{$adb_table}
	</td>
	</tr>	
</table>
{$footer}
</body>
</html>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'avatardatabase_edit',
        'template'    => $db->escape_string('<form id="edit_avasuggestion" method="post" action="misc.php?action=avatardatabase">
	<input type="hidden" name="adb_id" id="adb_id" value="{$row[\'adb_id\']}" class="textbox"  /> 
		<table width="70%" style="margin: auto;"><tr><td class="thead" colspan="2"><strong>{$lang->adb_edit}</strong></td></tr>
			<tr><td class="tcat"><strong>{$lang->adb_claim}</strong></td><td class="tcat"><strong>{$lang->adb_gender}</strong></td></tr>
			<tr>
				<td class="trow1"><input type="text" name="claim" id="claim" value="{$row[\'claimname\']}" class="textbox" required /> 
				</td>
					
				<td class="trow2"><select name="gender">
					{$gender_bit}
					</select>
				</td>
			</tr>
						<tr><td class="tcat"><strong>{$lang->adb_year}</strong></td><td class="tcat"><strong>{$lang->adb_origin}</strong></td></tr>
			<tr>
				<td class="trow1"><input type="number" name="year" id="year" value="{$row[\'year\']}" class="textbox" required /> 
				</td>
			
				<td class="trow2">
			<input type="text" name="origin" id="origin" value="{$row[\'origin\']}" class="textbox" required/> 
				</td>
			</tr>
									<tr>		<td class="tcat"><strong>{$lang->adb_hair}</strong></td>
									<td class="tcat"><strong>{$lang->adb_link}</strong></td></tr>
			<tr>

							<tr><td class="trow1">
			<input type="text" name="hair" id="hair" value="{$row[\'hair\']}" class="textbox" required/> 
				</td>
		<td class="trow2"><input type="text" name="link" id="link" value="{$row[\'link\']}" class="textbox" /> 
				</td>
			</tr>
			<tr><td class="trow1" colspan="2" align="center"><input type="submit" name="edit_avasuggestion" value="{$lang->adb_edit}" id="submit" class="button"></td></tr>
		</table>
</form>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'avatardatabase_formular',
        'template'    => $db->escape_string('<br /><form id="add_avasuggestion" method="post" action="misc.php?action=avatardatabase">
		<table width="70%" style="margin: auto;"><tr><td class="thead" colspan="2"><strong>{$lang->adb_add}</strong></td></tr>
			<tr><td class="tcat"><strong>{$lang->adb_claim}</strong></td><td class="tcat"><strong>{$lang->adb_gender}</strong></td></tr>
			<tr>
				<td class="trow1"><input type="text" name="claim" id="claim" placeholder="Vorname Nachname" class="textbox" required /> 
				</td>
					
				<td class="trow2"><select name="gender">
					<option value="1">{$lang->adb_female}</option>
					<option value="2">{$lang->adb_male}</option>
					{$divers}
					</select>
				</td>
			</tr>
						<tr><td class="tcat"><strong>{$lang->adb_year}</strong></td><td class="tcat"><strong>{$lang->adb_origin}</strong></td></tr>
			<tr>
				<td class="trow1"><input type="number" name="year" id="year" placeholder="1990" class="textbox" required /> 
				</td>
			
				<td class="trow2">
			<input type="text" name="origin" id="origin" placeholder="britisch" class="textbox" required/> 
				</td>
			</tr>
									<tr>
										<td class="tcat"><strong>{$lang->adb_hair}</strong></td>
									<td class="tcat"><strong>{$lang->adb_link}</strong></td>
									</tr>
			<tr>

							<tr>		<td class="trow1">
			<input type="text" name="hair" id="haar" placeholder="brünett" class="textbox" required/> 
				</td>
								<td class="trow2" colspan="2"><input type="text" name="link" id="link" placeholder="https://" class="textbox" /> 
				</td>
			</tr>
			<tr><td class="trow1" colspan="2" align="center"><input type="submit" name="add_avasuggestion" value="{$lang->adb_add}" id="submit" class="button"></td></tr>
		</table>
</form><br />'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'avatardatabase_suggestions',
        'template'    => $db->escape_string('<b>{$claimname}</b> {$link} <div class="float_right">{$options}</div>
<div style="font-size: 10px; text-style: italic;"><i class="fas fa-calendar-alt"></i> {$year} <i class="fas fa-map-marked"></i> {$origin}</div>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'avatardatabase_table',
        'template'    => $db->escape_string('<table border="0"  border="0" width="100%">
	<tr>
		<td class="thead" width="50%"><strong>{$lang->adb_female}</strong></td>
		<td class="thead" width="50%"><strong>{$lang->adb_male}</strong></td>
	</tr>
	<tr>
		<td class="trow1" valign="top">
			{$female}
		</td>
		<td class="trow2" valign="top">
			{$male}
			<td>
	</tr>
</table>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);

    $insert_array = array(
        'title'        => 'avatardatabase_table_divers',
        'template'    => $db->escape_string('<table border="0" width="100%">
	<tr>
		<td class="thead" width="33%"><strong>{$lang->adb_female}</strong></td>
		<td class="thead" width="33%"><strong>{$lang->adb_male}</strong></td>
		<td class="thead" width="33%"><strong>{$lang->adb_diverse}</strong></td>
	</tr>
	<tr>
		<td class="trow1" valign="top">
			{$female}
		</td>
		<td class="trow2" valign="top">
			{$male}
			</td>
				<td class="trow1" valign="top">
					{$diverse}
	</td>
	</tr>
</table>'),
        'sid'        => '-1',
        'version'    => '',
        'dateline'    => TIME_NOW
    );
    $db->insert_query("templates", $insert_array);
}

function avatardatabase_is_installed()
{
    global $db;
    if($db->table_exists("avatardatabase"))
    {
        return true;
    }
    return false;
}

function avatardatabase_uninstall()
{

    //Datenbanktabellen wieder löschen und Cache erneuern
    global $db, $cache;
    if ($db->table_exists("avatardatabase")) {
        $db->drop_table("avatardatabase");
    }

    if ($db->field_exists("canaddavatar", "usergroups")) {
        $db->drop_column("usergroups", "canaddavatar");
    }

    $cache->update_usergroups();

    // Einstellungen löschen
    $db->query("DELETE FROM " . TABLE_PREFIX . "settinggroups WHERE name='avatardatabase'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='avatardatabase_avatarfid'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='avatardatabase_claimtype'");
    $db->query("DELETE FROM " . TABLE_PREFIX . "settings WHERE name='avatardatabase_gender'");

    // Templates löschen
    $db->delete_query("templates", "title LIKE '%avatardatabase%'");
}

function avatardatabase_activate()
{
    require MYBB_ROOT."/inc/adminfunctions_templates.php";
}

function avatardatabase_deactivate()
{
    require MYBB_ROOT . "/inc/adminfunctions_templates.php";
}

$plugins->add_hook("admin_formcontainer_end", "avatardatabase_usergroup_permission");
$plugins->add_hook("admin_user_groups_edit_commit", "avatardatabase_usergroup_permission_commit");
// Usergruppen-Berechtigungen
function avatardatabase_usergroup_permission()
{
    global $mybb, $lang, $form, $form_container, $run_module;

    if($run_module == 'user' && !empty($form_container->_title) & !empty($lang->misc) & $form_container->_title == $lang->misc)
    {
        $avatardatabase_options = array(
            $form->generate_check_box('canaddavatar', 1, "Kann Avatarvorschlag hinzufügen?", array("checked" => $mybb->input['canaddavatar'])),
        );
        $form_container->output_row("Avatardatenbank", "", "<div class=\"group_settings_bit\">".implode("</div><div class=\"group_settings_bit\">", $avatardatabase_options)."</div>");
    }
}

function avatardatabase_usergroup_permission_commit()
{
    global $db, $mybb, $updated_group;
    $updated_group['canaddavatar'] = $mybb->get_input('canaddavatar', MyBB::INPUT_INT);
}


$plugins->add_hook('misc_start', 'avatardatabase_misc');

// In the body of your plugin
function avatardatabase_misc()
{
    global $mybb, $db, $templates, $lang, $header, $headerinclude, $footer, $divers, $formular, $female, $male, $diverse, $claimname, $year, $origin, $link, $edit, $hair;
    $lang->load('avatardatabase');

    if($mybb->get_input('action') == 'avatardatabase')
    {
        // Do something, for example I'll create a page using the hello_world_template

        // Add a breadcrumb
        add_breadcrumb($lang->adb_main, "misc.php?action=avatardatabase");

        // Optionen
        $avatar_exist  = $mybb->settings['avatardatabase_avatarfid'];
        $avatartype = $mybb->settings['avatardatabase_claimtype'];
        $gender_setting = $mybb->settings['avatardatabase_gender'];

        // Das Formular
        if($gender_setting == 1){
            $divers = "<option value=\"3\">{$lang->adb_diverse}</option>";
        }
        if($mybb->usergroup['canaddavatar'] == 1) {
            eval('$formular  = "' . $templates->get('avatardatabase_formular') . '";');
        }

        
        //Avatar hinzufügen
        if(isset($_POST['add_avasuggestion'])) {
            $claim  = $_POST['claim'];
            $gender = $_POST['gender'];
            $origin = $_POST['origin'];
            $year = $_POST['year'];
            $hair = $_POST['hair'];
            $link = $_POST['link'];

            $new_record = array(
                "claimname" => $db->escape_string($claim),
                "gender" => (int)$gender,
                "origin" => $db->escape_string($origin),
                "year" => (int)$year,
                "hair" => $db->escape_string($hair),
                "link" => $db->escape_string($link),
            );

            $db->insert_query("avatardatabase", $new_record);
            redirect("misc.php?action=avatardatabase");
        }

        // auf gehtes zum Filtern





            $letter = $mybb->input['letter'];
            if(empty($letter)){
                $letter = "%";
            }
            $year_filter = $mybb->input['year'];
            if(empty($year_filter)){
                $year_filter = "%";
            }
            $origin_filter = $mybb->input['origin'];
            if(empty($origin_filter)){
                $origin_filter = "%";
            }

        $hair_filter = $mybb->input['hair'];
        if(empty($hair_filter)){
            $hair_filter = "%";
        }


        $alphabet = range('A', 'Z');
        foreach($alphabet as $alphabetletter) {
            $selected = "";
            if($alphabetletter == $letter) {
                $selected = "selected";
            }
            $letter_bit .= "<option value=\"{$alphabetletter}\" {$selected}>{$alphabetletter}</option>";
        }

        // lese alle Jahre aus
        $year_select = $db->query("SELECT DISTINCT year
        FROM ".TABLE_PREFIX."avatardatabase
        order by year ASC
        ");

        $all_years = array();

      while($year_row = $db->fetch_array($year_select)){
          $year = $year_row['year'];
          array_push($all_years, $year);
      }

        foreach($all_years as $years) {
            $selected = "";
            if($years == $year_filter) {
                $selected = "selected";
            }
            $year_bit.= "<option value=\"{$years}\" {$selected}>{$years}</option>";
        }


        // lese alle Herkünfte aus
        $origin_select = $db->query("SELECT DISTINCT origin
        FROM ".TABLE_PREFIX."avatardatabase
        order by origin ASC
        ");

        $all_origins = array();

        while($origin_row = $db->fetch_array($origin_select)){
            $origin = $origin_row['origin'];
            array_push($all_origins, $origin);
        }

        foreach($all_origins as $origins) {
            $selected = "";
            if($origins == $origin_filter) {
                $selected = "selected";
            }
            $origin_bit.= "<option value=\"{$origins}\" {$selected}>{$origins}</option>";
        }

        // variabeln

        $count_female = 0;
        $count_male = 0;
        $count_divers = 0;

        // Geben wir die Datenbank mal aus

        $select = $db->query("SELECT *
        FROM ".TABLE_PREFIX."avatardatabase
        WHERE claimname like '".$letter."%'
        and year like '".$year_filter."'
        and origin like '".$origin_filter."'
        and hair like '%$hair_filter%'
        ORDER BY claimname ASC
        ");


        while($row = $db->fetch_array($select)){

            // variabeln leeren
            $link = "";
            $claimname = "";
            $origin = "";
            $year = 0;
            $hair = "";
            $options = "";
            $gender = 0;
            $gender_bit = "";

            // ID des Eintrags
            $adb_id = $row['adb_id'];

            // einmal bitte mit Inhalt füllen
            $claimname = $row['claimname'];
            $origin = $row['origin'];
            $year = $row['year'];
            $gender = $row['gender'];
            $hair = $row['hair'];

            if(!empty($row['link'])){
                $link = "<a href='{$row['link']}' target='_blank' title='Link zur Galerie'><i class=\"fas fa-location-arrow\"></i></a>";
            }

            // Kontrolliere, ob das Avatar schon vergeben ist. Wenn ja, streich es durch!
            $avaselect = $db->query("SELECT $avatar_exist
            FROM ".TABLE_PREFIX."userfields
            ");

            while ($claims = $db->fetch_array($avaselect)){

                $claim = "";
                $claim = $claims[$avatar_exist];

                if($avatartype == 1){
                    $claim = explode(", ", $claim);
                    $claim = $claim[1]." ".$claim[0];
                }

                if($claim == $claimname){
                    $claimname = "<s>{$claimname}</s>";
                } else{
                    $claimname = $claimname;
                }

            }


            // optionen (nur Team kann bearbeiten und Löschen)
            if($gender_setting == 1){
                $genders = array("1" => "{$lang->adb_female}", "2" => "{$lang->adb_male}", "3" => "{$lang->adb_diverse}");
            } else{
                $genders = array("1" => "{$lang->adb_female}", "2" => "{$lang->adb_male}");
            }


            foreach($genders as $key => $value) {
                $checked = "";
                if($gender == $key) {
                    $checked = "selected";
                }
                $gender_bit .= "<option value=\"{$key}\" {$checked}>{$value}</option>";
            }


  if($mybb->usergroup['canmodcp'] == 1) {
            $edit = "<a onclick=\"$('#edit_{$adb_id}').modal({ fadeDuration: 250, keepelement: true, zIndex: (typeof modal_zindex !== 'undefined' ? modal_zindex : 9999) }); return false;\" style=\"cursor: pointer;\"><i class=\"fa fa-edit\" aria-hidden=\"true\" title='Avatar editieren'></i></a>";

            eval("\$edit_adb = \"" . $templates->get ("avatardatabase_edit") . "\";");

            $delete = "<a href='misc.php?action=avatardatabase&avatar_delete={$adb_id}'><i class=\"fas fa-trash-alt\" title='Avatar löschen'></i></a>";


          
                $options = "{$edit}<div class=\"modal\" id=\"edit_{$adb_id}\" style=\"display: none;\">{$edit_adb}</div> {$delete}";
            }



            if($row['gender'] == 1){
                $count_female++;
                eval("\$female .= \"" . $templates->get("avatardatabase_suggestions") . "\";");
            } elseif($row['gender'] == 2){
                $count_male++;
                eval("\$male .= \"" . $templates->get("avatardatabase_suggestions") . "\";");
            }elseif($row['gender'] == 3){
                $count_divers++;
                eval("\$diverse .= \"" . $templates->get("avatardatabase_suggestions") . "\";");
            }

        }

        if($mybb->settings['avatardatabase_gender'] == 1) {
            eval('$adb_table  = "' . $templates->get('avatardatabase_table_divers') . '";');
        } else{
            eval('$adb_table  = "' . $templates->get('avatardatabase_table') . '";');
        }

        //Avatar editieren
        if(isset($_POST['edit_avasuggestion'])) {
            $adb_id = $_POST['adb_id'];
            $claim  = $_POST['claim'];
            $gender = $_POST['gender'];
            $hair = $_POST['hair'];
            $origin = $_POST['origin'];
            $year = $_POST['year'];
            $link = $_POST['link'];


            $db->query("UPDATE ".TABLE_PREFIX."avatardatabase SET claimname ='".$claim."', gender = '".$gender."', origin = '".$origin."', year = '".$year."',  link = '".$link."', hair = '".$hair."' WHERE adb_id = '".$adb_id."'");
            redirect("misc.php?action=avatardatabase");
        }


        // Avatar löschen
        $avatar_delete = $mybb->input['avatar_delete'];
        if($avatar_delete){

            $db->delete_query("avatardatabase", "adb_id = '$avatar_delete'");
            redirect("misc.php?action=avatardatabase");

        }

        // Using the misc_help template for the page wrapper
        eval("\$page = \"".$templates->get("avatardatabase")."\";");
        output_page($page);

}
}
