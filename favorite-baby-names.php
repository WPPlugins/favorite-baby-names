<?php
/*
Plugin Name: Favorite Baby Names
Plugin URI: http://wordpress.org/extend/plugins/favorite-baby-names/
Description: Adds a customizeable widget which displays the latest favorite baby names in Germany by http://www.vorname.com/
Version: 1.0
Author: Jens Heinstein
Author URI: http://www.vorname.com/
License: GPL3
*/

function vornamecom()
{
  $options = get_option("widget_vornamecom");
  if (!is_array($options)){
    $options = array(
      'title' => 'Beliebte Vornamen',
      'news' => '10',
      'chars' => '10'
    );
  }

  // RSS Objekt erzeugen 
  $rss = simplexml_load_file( 
  'http://www.vorname.com/feeds/topnamen.php'); 
  
  $girl = array();
  $boy = array();

  foreach($rss->channel->item as $i) { 
    $title = $i->title;
    $link = $i->link;
    $geschlecht = $i->geschlecht;
    $position = $i->position;
    if ($geschlecht == "m") {
      $boy[] = array ('Name' => $title, 'Link' => $link, 'Geschlecht' => $geschlecht, 'Position' => $position);
    } else {
  	  $girl[] = array ('Name' => $title, 'Link' => $link, 'Geschlecht' => $geschlecht, 'Position' => $position);
    }    
  }
  ?> 
  
  <table width="100%">
    <tr>
      <th scope="col">Weibliche Vornamen</th>
      <th scope="col">M&auml;nnliche Vornamen</th>
    </tr>  
  <?php 
  // maximale Anzahl an Namen, wobei 0 (Null) alle anzeigt
  $max_news = $options['news'];
    
  // RSS Elemente durchlaufen 
  $cnt = 0;
  for($x=0;$x<25;$x++) { 
    if($max_news > 0 AND $cnt >= $max_news){
        break;
    }
    ?> 
      <tr>
      <td><a href="<?php echo $girl[$x][Link][0]?>" target="_blank"><?php echo $girl[$x][Name][0]?></a></td>
      <td><a href="<?php echo $girl[$x][Link][0]?>" target="_blank"><?php echo $boy[$x][Name][0]?></a></td>
      </tr>
    <?php 
    $cnt++;
  } 
  ?>     
  </table>
  <?php
  $powered = $options['chars'];
    if ($powered > 0){
      echo "<span style=\"font-size: ".$powered."px;\">powered by <a href=\"http://www.vorname.com/\" target=\"_blank\">vorname.com</a></span>";
    }
  ?>
<?php  
}

function widget_vornamecom($args)
{
  extract($args);
  
  $options = get_option("widget_vornamecom");
  if (!is_array($options)){
    $options = array(
      'title' => 'Beliebte Vornamen',
      'news' => '10',
      'chars' => '10'
    );
  }
  
  echo $before_widget;
  echo $before_title;
  echo $options['title'];
  echo $after_title;
  vornamecom();
  echo $after_widget;
}

function vornamecom_control()
{
  $options = get_option("widget_vornamecom");
  if (!is_array($options)){
    $options = array(
      'title' => 'Beliebte Vornamen',
      'news' => '10',
      'chars' => '10'
    );
  }
  
  if($_POST['vornamecom-Submit'])
  {
    $options['title'] = htmlspecialchars($_POST['vornamecom-WidgetTitle']);
    $options['news'] = htmlspecialchars($_POST['vornamecom-NewsCount']);
    $options['chars'] = htmlspecialchars($_POST['vornamecom-CharCount']);
    update_option("widget_vornamecom", $options);
  }
?> 
  <p>
    <label for="vornamecom-WidgetTitle">Widget Titel: </label>
    <input type="text" id="vornamecom-WidgetTitle" name="vornamecom-WidgetTitle" value="<?php echo $options['title'];?>" />
    <br /><br />
    <label for="vornamecom-NewsCount">Anzahl Namen (max. 25): </label>
    <input type="text" size="3" id="vornamecom-NewsCount" name="vornamecom-NewsCount" value="<?php echo $options['news'];?>" />
    <br /><br />
    <label for="vornamecom-CharCount">Gr&ouml;&szlig;e von "powered by" (0 = aus): </label>
    <input type="text" size="3" id="vornamecom-CharCount" name="vornamecom-CharCount" value="<?php echo $options['chars'];?>" />
    <br /><br />
    <input type="hidden" id="vornamecom-Submit"  name="vornamecom-Submit" value="1" />
  </p>
  
<?php
}

function vornamecom_init()
{
  register_sidebar_widget(__('Favorite Baby Names'), 'widget_vornamecom');    
  register_widget_control('Favorite Baby Names', 'vornamecom_control', 300, 200);
}
add_action("plugins_loaded", "vornamecom_init");
?>
