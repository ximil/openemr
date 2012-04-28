<?php
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.

$fake_register_globals=false;
$sanitize_all_escapes=true;

require_once("../../globals.php");
require_once("../../../custom/code_types.inc.php");
require_once("$srcdir/sql.inc");
require_once("$srcdir/options.inc.php");
require_once("$srcdir/formatting.inc.php");
require_once("$srcdir/formdata.inc.php");

// Translation for form fields.
function ffescape($field) {
  $field = add_escape_custom($field);
  return trim($field);
}

// Format dollars for display.
//
function bucks($amount) {
  if ($amount) {
    $amount = oeFormatMoney($amount);
    return $amount;
  }
  return '';
}

$alertmsg = '';
$pagesize = 100;
$mode = $_POST['mode'];
$code_id = 0;
$related_code = '';
$active = 1;
$reportable = 0;

if (isset($mode)) {
  $code_id    = $_POST['code_id'] + 0;
  $code       = $_POST['code'];
  $code_type  = $_POST['code_type'];
  $code_text  = $_POST['code_text'];
  $modifier   = $_POST['modifier'];
  $superbill  = $_POST['form_superbill'];
  $related_code = $_POST['related_code'];
  $cyp_factor = $_POST['cyp_factor'] + 0;
  $active     = empty($_POST['active']) ? 0 : 1;
  $reportable = empty($_POST['reportable']) ? 0 : 1;

  $taxrates = "";
  if (!empty($_POST['taxrate'])) {
    foreach ($_POST['taxrate'] as $key => $value) {
      $taxrates .= "$key:";
    }
  }

  if ($mode == "delete") {
    sqlStatement("DELETE FROM codes WHERE id = ?", array($code_id) );
    $code_id = 0;
  }
  else if ($mode == "add") { // this covers both adding and modifying
    $crow = sqlQuery("SELECT COUNT(*) AS count FROM codes WHERE " .
      "code_type = '"    . ffescape($code_type)    . "' AND " .
      "code = '"         . ffescape($code)         . "' AND " .
      "modifier = '"     . ffescape($modifier)     . "' AND " .
      "id != '"          . add_escape_custom($code_id) . "'");
    if ($crow['count']) {
      $alertmsg = xl('Cannot add/update this entry because a duplicate already exists!');
    }
    else {
      $sql =
        "code = '"         . ffescape($code)         . "', " .
        "code_type = '"    . ffescape($code_type)    . "', " .
        "code_text = '"    . ffescape($code_text)    . "', " .
        "modifier = '"     . ffescape($modifier)     . "', " .
        "superbill = '"    . ffescape($superbill)    . "', " .
        "related_code = '" . ffescape($related_code) . "', " .
        "cyp_factor = '"   . ffescape($cyp_factor)   . "', " .
        "taxrates = '"     . ffescape($taxrates)     . "', " .
        "active = "        . add_escape_custom($active) . ", " .
        "reportable = "    . add_escape_custom($reportable);
      if ($code_id) {
        $query = "UPDATE codes SET $sql WHERE id = ?";
        sqlStatement($query, array($code_id) );
        sqlStatement("DELETE FROM prices WHERE pr_id = ? AND " .
          "pr_selector = ''", array($code_id) );
      }
      else {
        $code_id = sqlInsert("INSERT INTO codes SET $sql");
      }
      if (!$alertmsg) {
        foreach ($_POST['fee'] as $key => $value) {
          $value = $value + 0;
          if ($value) {
            sqlStatement("INSERT INTO prices ( " .
              "pr_id, pr_selector, pr_level, pr_price ) VALUES ( " .
              "?, '', ?, ?)", array($code_id,$key,$value) );
          }
        }
        $code = $code_type = $code_text = $modifier = $superbill = "";
        $code_id = 0;
        $related_code = '';
        $cyp_factor = 0;
        $taxrates = '';
        $active = 1;
        $reportable = 0;
      }
    }
  }
  else if ($mode == "edit") { // someone clicked [Edit]
    $sql = "SELECT * FROM codes WHERE id = ?";
    $results = sqlStatement($sql, array($code_id) );
    while ($row = sqlFetchArray($results)) {
      $code         = $row['code'];
      $code_text    = $row['code_text'];
      $code_type    = $row['code_type'];
      $modifier     = $row['modifier'];
      // $units        = $row['units'];
      $superbill    = $row['superbill'];
      $related_code = $row['related_code'];
      $cyp_factor   = $row['cyp_factor'];
      $taxrates     = $row['taxrates'];
      $active       = 0 + $row['active'];
      $reportable   = 0 + $row['reportable'];
    }
  }
}

$related_desc = '';
if (!empty($related_code)) {
  $related_desc = $related_code;
}

$fstart = $_REQUEST['fstart'] + 0;
$filter = $_REQUEST['filter'] + 0;
if ($filter) {
 $filter_key = convert_type_id_to_key($filter);
}
$search = $_REQUEST['search'];

if ($filter) {
 $count = code_set_search($filter_key,$search,true,false);
}
else {
 $count = code_set_search("--ALL--",$search,true,false);
}
if ($fstart >= $count) $fstart -= $pagesize;
if ($fstart < 0) $fstart = 0;
$fend = $fstart + $pagesize;
if ($fend > $count) $fend = $count;
?>

<html>
<head>
<?php html_header_show(); ?>
<link rel="stylesheet" href="<?php echo attr($css_header);?>" type="text/css">
<script type="text/javascript" src="../../../library/dialog.js"></script>
<script type="text/javascript" src="../../../library/textformat.js"></script>

<script language="JavaScript">

// This is for callback by the find-code popup.
// Appends to or erases the current list of related codes.
function set_related(codetype, code, selector, codedesc) {
 var f = document.forms[0];
 var s = f.related_code.value;
 if (code) {
  if (s.length > 0) s += ';';
  s += codetype + ':' + code;
 } else {
  s = '';
 }
 f.related_code.value = s;
 f.related_desc.value = s;
}

// This invokes the find-code popup.
function sel_related() {
 var f = document.forms[0];
 var i = f.code_type.selectedIndex;
 var codetype = '';
 if (i >= 0) {
  var myid = f.code_type.options[i].value;
<?php
foreach ($code_types as $key => $value) {
  $codeid = $value['id'];
  $coderel = $value['rel'];
  if (!$coderel) continue;
  echo "  if (myid == $codeid) codetype = '$coderel';";
}
?>
 }
 if (!codetype) {
  alert('<?php echo addslashes( xl('This code type does not accept relations.') ); ?>');
  return;
 }
 dlgopen('find_code_popup.php', '_blank', 500, 400);
}

// Some validation for saving a new code entry.
function validEntry(f) {
 if (!f.code.value) {
  alert('<?php echo addslashes( xl('No code was specified!') ); ?>');
  return false;
 }
<?php if ($GLOBALS['ippf_specific']) { ?>
 if (f.code_type.value == 12 && !f.related_code.value) {
  alert('<?php echo addslashes( xl('A related IPPF code is required!') ); ?>');
  return false;
 }
<?php } ?>
 return true;
}

function submitAdd() {
 var f = document.forms[0];
 if (!validEntry(f)) return;
 f.mode.value = 'add';
 f.code_id.value = '';
 f.submit();
}

function submitUpdate() {
 var f = document.forms[0];
 if (! parseInt(f.code_id.value)) {
  alert('<?php echo addslashes( xl('Cannot update because you are not editing an existing entry!') ); ?>');
  return;
 }
 if (!validEntry(f)) return;
 f.mode.value = 'add';
 f.submit();
}

function submitList(offset) {
 var f = document.forms[0];
 var i = parseInt(f.fstart.value) + offset;
 if (i < 0) i = 0;
 f.fstart.value = i;
 f.submit();
}

function submitEdit(id) {
 var f = document.forms[0];
 f.mode.value = 'edit';
 f.code_id.value = id;
 f.submit();
}

function submitDelete(id) {
 var f = document.forms[0];
 f.mode.value = 'delete';
 f.code_id.value = id;
 f.submit();
}

function getCTMask() {
 var ctid = document.forms[0].code_type.value;
<?php
foreach ($code_types as $key => $value) {
  $ctid   = attr($value['id']);
  $ctmask = attr($value['mask']);
  echo " if (ctid == '$ctid') return '$ctmask';\n";
}
?>
 return '';
}

function codeTypeChanged(code) {
 var f = document.forms[0];
 if (f.code_type.value == 2) 
   f.reportable.disabled = false;
 else {
   f.reportable.checked = false;
   f.reportable.disabled = true;
 }
}

</script>

</head>
<body class="body_top" onLoad="codeTypeChanged();" >

<?php if ($GLOBALS['concurrent_layout']) {
} else { ?>
<a href='patient_encounter.php?codefrom=superbill' target='Main'>
<span class='title'><?php echo xlt('Superbill Codes'); ?></span>
<font class='more'><?php echo text($tback);?></font></a>
<?php } ?>

<form method='post' action='superbill_custom_full.php' name='theform'>

<input type='hidden' name='mode' value=''>

<br>

<center>
<table border='0' cellpadding='0' cellspacing='0'>

 <tr>
  <td colspan="3"> <?php echo xlt('Not all fields are required for all codes or code types.'); ?><br><br></td>
 </tr>

 <tr>
  <td><?php echo xlt('Type'); ?>:</td>
  <td width="5"></td>
  <td>
   <select name="code_type" onChange="codeTypeChanged();">
<?php $external_sets = array(); ?>
<?php foreach ($code_types as $key => $value) { ?>
  <?php if ( !($value['external']) ) { ?>
    <option value="<?php  echo attr($value['id']) ?>"<?php if ($GLOBALS['code_type'] == $value['id']) echo " selected" ?>><?php echo xlt($value['label']) ?></option>
  <?php } else {
    array_push($external_sets,$key);
  } ?>
<?php } ?>
   </select>
   &nbsp;&nbsp;
   <?php echo xlt('Code'); ?>:
   <input type='text' size='6' name='code' value='<?php echo attr($code) ?>'
    onkeyup='maskkeyup(this,getCTMask())'
    onblur='maskblur(this,getCTMask())'
   />
<?php if (modifiers_are_used()) { ?>
   &nbsp;&nbsp;<?php echo xlt('Modifier'); ?>:
   <input type='text' size='3' name='modifier' value='<?php echo attr($modifier) ?>'>
<?php } else { ?>
   <input type='hidden' name='modifier' value=''>
<?php } ?>

   &nbsp;&nbsp;
   <input type='checkbox' name='active' value='1'<?php if (!empty($active)) echo ' checked'; ?> />
   <?php echo xlt('Active'); ?>
  </td>
 </tr>

 <tr>
  <td><?php echo xlt('Description'); ?>:</td>
  <td></td>
  <td>
   <input type='text' size='50' name="code_text" value='<?php echo attr($code_text) ?>'>
  </td>
 </tr>

 <tr>
  <td><?php echo xlt('Category'); ?>:</td>
  <td></td>
  <td>
<?php
generate_form_field(array('data_type'=>1,'field_id'=>'superbill','list_id'=>'superbill'), $superbill);
?>
   &nbsp;&nbsp;
   <input type='checkbox' name='reportable' value='1'<?php if (!empty($reportable)) echo ' checked'; ?> />
   <?php echo xlt('Reportable'); ?>
  </td>
 </tr>

 <tr<?php if (empty($GLOBALS['ippf_specific'])) echo " style='display:none'"; ?>>
  <td><?php echo xlt('CYP Factor'); ?>:</td>
  <td></td>
  <td>
   <input type='text' size='10' maxlength='20' name="cyp_factor" value='<?php echo attr($cyp_factor) ?>'>
  </td>
 </tr>

 <tr<?php if (!related_codes_are_used()) echo " style='display:none'"; ?>>
  <td><?php echo xlt('Relate To'); ?>:</td>
  <td></td>
  <td>
   <input type='text' size='50' name='related_desc'
    value='<?php echo attr($related_desc) ?>' onclick="sel_related()"
    title='<?php echo xla('Click to select related code'); ?>' readonly />
   <input type='hidden' name='related_code' value='<?php echo attr($related_code) ?>' />
  </td>
 </tr>

 <tr>
  <td><?php echo xlt('Fees'); ?>:</td>
  <td></td>
  <td>
<?php
$pres = sqlStatement("SELECT lo.option_id, lo.title, p.pr_price " .
  "FROM list_options AS lo LEFT OUTER JOIN prices AS p ON " .
  "p.pr_id = ? AND p.pr_selector = '' AND p.pr_level = lo.option_id " .
  "WHERE list_id = 'pricelevel' ORDER BY lo.seq", array($code_id) );
for ($i = 0; $prow = sqlFetchArray($pres); ++$i) {
  if ($i) echo "&nbsp;&nbsp;";
  echo text(xl_list_label($prow['title'])) . " ";
  echo "<input type='text' size='6' name='fee[" . attr($prow['option_id']) . "]' " .
    "value='" . attr($prow['pr_price']) . "' >\n";
}
?>
  </td>
 </tr>

<?php
$taxline = '';
$pres = sqlStatement("SELECT option_id, title FROM list_options " .
  "WHERE list_id = 'taxrate' ORDER BY seq");
while ($prow = sqlFetchArray($pres)) {
  if ($taxline) $taxline .= "&nbsp;&nbsp;";
  $taxline .= "<input type='checkbox' name='taxrate[" . attr($prow['option_id']) . "]' value='1'";
  if (strpos(":$taxrates", $prow['option_id']) !== false) $taxline .= " checked";
  $taxline .= " />\n";
  $taxline .=  text(xl_list_label($prow['title'])) . "\n";
}
if ($taxline) {
?>
 <tr>
  <td><?php echo xlt('Taxes'); ?>:</td>
  <td></td>
  <td>
   <?php echo $taxline ?>
  </td>
 </tr>
<?php } ?>

 <tr>
  <td colspan="3" align="center">
   <input type="hidden" name="code_id" value="<?php echo attr($code_id) ?>"><br>
   <a href='javascript:submitUpdate();' class='link'>[<?php echo xlt('Update'); ?>]</a>
   &nbsp;&nbsp;
   <a href='javascript:submitAdd();' class='link'>[<?php echo xlt('Add as New'); ?>]</a>
  </td>
 </tr>

</table>

<table border='0' cellpadding='5' cellspacing='0' width='96%'>
 <tr>

  <td class='text'>
   <select name='filter' onchange='submitList(0)'>
    <option value='0'>
    <?php $all_string = xlt("All");
          if ( !(empty($external_sets) )) {
           // Show the external code sets that will not work with All selection
           $all_string .= " (" . xlt("Except") . " ";
           $first_flag = true;
           foreach ($external_sets as $set) {
            if ($first_flag) { //deal with the comma
             $first_flag = false;
            }
            else {
             $all_string .= ",";
            }
            $all_string .= $code_types[$set]['label'];
           }
           $all_string .= ")";
          }
          echo $all_string;
    ?>
    </option>
<?php
foreach ($code_types as $key => $value) {
  echo "<option value='" . attr($value['id']) . "'";
  if ($value['id'] == $filter) echo " selected";
  echo ">" . xlt($value['label']) . "</option>\n";
}
?>
   </select>
   &nbsp;&nbsp;&nbsp;&nbsp;

   <input type="text" name="search" size="5" value="<?php echo attr($search) ?>">&nbsp;
   <input type="submit" name="go" value='<?php echo xla('Search'); ?>'>
   <input type='hidden' name='fstart' value='<?php echo attr($fstart) ?>'>
  </td>

  <td class='text' align='right'>
<?php if ($fstart) { ?>
   <a href="javascript:submitList(-<?php echo attr($pagesize) ?>)">
    &lt;&lt;
   </a>
   &nbsp;&nbsp;
<?php } ?>
   <?php echo ($fstart + 1) . " - $fend of $count" ?>
   &nbsp;&nbsp;
   <a href="javascript:submitList(<?php echo attr($pagesize) ?>)">
    &gt;&gt;
   </a>
  </td>

 </tr>
</table>

</form>

<table border='0' cellpadding='5' cellspacing='0' width='96%'>
 <tr>
  <td><span class='bold'><?php echo xlt('Code'); ?></span></td>
  <td><span class='bold'><?php echo xlt('Mod'); ?></span></td>
  <td><span class='bold'><?php echo xlt('Act'); ?></span></td>
  <td><span class='bold'><?php echo xlt('Rep'); ?></span></td>
  <td><span class='bold'><?php echo xlt('Type'); ?></span></td>
  <td><span class='bold'><?php echo xlt('Description'); ?></span></td>
<?php if (related_codes_are_used()) { ?>
  <td><span class='bold'><?php echo xlt('Related'); ?></span></td>
<?php } ?>
<?php
$pres = sqlStatement("SELECT title FROM list_options " .
  "WHERE list_id = 'pricelevel' ORDER BY seq");
while ($prow = sqlFetchArray($pres)) {
  echo "  <td class='bold' align='right' nowrap>" . text(xl_list_label($prow['title'])) . "</td>\n";
}
?>
  <td></td>
  <td></td>
 </tr>
<?php
// Flag is this is from an external set
$is_external_set=false;
if (in_array($filter_key,$external_sets)) {
  $is_external_set=true;
}

if ($filter) {
 $res = code_set_search($filter_key,$search,false,false,$fstart,($fend - $fstart));
}
else {
 $res = code_set_search("--ALL--",$search,false,false,$fstart,($fend - $fstart));
}

for ($i = 0; $row = sqlFetchArray($res); $i++) $all[$i] = $row;

if (!empty($all)) {
  $count = 0;
  foreach($all as $iter) {
    $count++;

    $has_fees = false;
    foreach ($code_types as $key => $value) {
      if ($value['id'] == $iter['code_type']) {
        $has_fees = $value['fee'];
        break;
      }
    }

    echo " <tr>\n";
    echo "  <td class='text'>" . text($iter["code"]) . "</td>\n";
    echo "  <td class='text'>" . text($iter["modifier"]) . "</td>\n";
    // For active flag, always yes when shwoing external code sets
    echo "  <td class='text'>" . ( ($iter["active"] || $is_external_set) ? xlt('Yes') : xlt('No')) . "</td>\n";
    echo "  <td class='text'>" . ($iter["reportable"] ? xlt('Yes') : xlt('No')) . "</td>\n";
    echo "  <td class='text'>" . text($key) . "</td>\n";
    echo "  <td class='text'>" . text($iter['code_text']) . "</td>\n";

    if (related_codes_are_used()) {
      // Show related codes.
      echo "  <td class='text'>";
      $arel = explode(';', $iter['related_code']);
      foreach ($arel as $tmp) {
        list($reltype, $relcode) = explode(':', $tmp);
        $code_description = lookup_code_descriptions($reltype.":".$relcode);        
        echo text($relcode) . ' ' . text(trim($code_description)) . '<br />';
      }
      echo "</td>\n";
    }

    $pres = sqlStatement("SELECT p.pr_price " .
      "FROM list_options AS lo LEFT OUTER JOIN prices AS p ON " .
      "p.pr_id = ? AND p.pr_selector = '' AND p.pr_level = lo.option_id " .
      "WHERE list_id = 'pricelevel' ORDER BY lo.seq", array($iter['id']) );
    while ($prow = sqlFetchArray($pres)) {
      echo "<td class='text' align='right'>" . text(bucks($prow['pr_price'])) . "</td>\n";
    }

    if (!($is_external_set)) { //Unable to modify external code sets
      echo "  <td align='right'><a class='link' href='javascript:submitDelete(" . attr($iter['id']) . ")'>[" . xlt('Delete') . "]</a></td>\n";
      echo "  <td align='right'><a class='link' href='javascript:submitEdit("   . attr($iter['id']) . ")'>[" . xlt('Edit') . "]</a></td>\n";
    }

    echo " </tr>\n";

  }
}

?>

</table>

</center>

<script language="JavaScript">
<?php
 if ($alertmsg) {
  echo "alert('" . addslashes($alertmsg) . "');\n";
 }
?>
</script>

</body>
</html>
