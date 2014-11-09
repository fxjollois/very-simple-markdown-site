<?php 

// ***************************************************************************
// quelques outils pratiques
// ***************************************************************************

// http://parsedown.org/ : pour parser du Markdown en HTML 
include 'lib/Parsedown.php'; 

// http://simplehtmldom.sourceforge.net/ pour parser du HTML en DOM 
include 'lib/simple_html_dom.php';

// ***************************************************************************
// récupération de la page demandée
// ***************************************************************************

// Definition de constantes
define('ROOT_DIR', realpath(dirname(__FILE__)) .'/');
define('CONTENT_DIR', ROOT_DIR .'src/');
define('CONTENT_EXT', '.md');

// Récupération de l'url demandée et du script actuel
$url = '';
$request_url = (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '';
$script_url  = (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : '';
$base_url = str_replace('index.php', '', $script_url);

// Si différent (i.e. on n'a pas demandé explicitement la racine ou index.php de la racine)
// on enlève le ROOT_DIR (si les choses sont bien faites)
if($request_url != $script_url) 
	$url = trim(preg_replace('/'. str_replace('/', '\/', $base_url) .'/', '', $request_url, 1), '/');

// Création du chemin de la page demandée
if($url) $file = CONTENT_DIR . $url;
else $file = CONTENT_DIR .'index';

// On regarde si c'est un répertoire et que ca termine bien par un "/"
if (is_dir($file) && substr($request_url, -1, 1) != '/') {
	// si oui, redirection avec un / à la fin (pour éviter les soucis d'adressage par la suite)
	echo header('Location: '.$request_url.'/');
	exit();
}

// Chargement du contenu de la page demandée
if(is_dir($file)) 
	$file = CONTENT_DIR . $url .'/index'. CONTENT_EXT;
else 
	$file .= CONTENT_EXT;

if(file_exists($file))
	$content = file_get_contents($file);
else
	$content = file_get_contents(CONTENT_DIR . "/404.md");

// parsage du contenu Markdown en HTML
$Parsedown = new Parsedown();
$parsed = $Parsedown->text($content);
	
// parsage du contenu HTML en DOM
$html = str_get_html($parsed); 

// ***************************************************************************
// ***************************************************************************
// création de la page finale
// ***************************************************************************
// ***************************************************************************

$out2 = file_get_html(ROOT_DIR . '/theme/struct.html');

// ***************************************************************************
// Partie en-tête
// ***************************************************************************

// titre de la page = le premier h1 qu'on trouve dans la page demandée
$out2->find('title', 0)->innertext = $html->find('h1', 0)->innertext;

// on indique le bon lien pour le CSS
$out2->find('link', 0)->href = $base_url.$out2->find('link', 0)->href;

// ***************************************************************************
// Partie corps
// ***************************************************************************

// En-tête de la page
$out2->find('h1', 0)->innertext = "<a href='".$base_url."'>".$out2->find('h1', 0)->innertext."</a>";

// Menu du site
$dir = CONTENT_DIR;
$menu = "";
$chemin = "accueil";

// Fonction de lecture récursive des fichiers dans un répertoire
function LectureRep($repname) {
	global $menu, $base_url, $request_url, $chemin;
	$fichiers = scandir($repname);
	if (in_array("index.md", $fichiers)) { // on est dans un répertoire valide
		$pd = new Parsedown();
		if ($repname == CONTENT_DIR) // on est à la racine -> titre = Accueil
			$menu .= "<ul>";
		else {
			$titre = str_get_html($pd->text(file_get_contents($repname."/index.md")))->find('h1', 0)->innertext;
			$lien = $base_url.str_replace(CONTENT_DIR, '', $repname); 
			$active = "";
			if ($lien == $request_url || strstr($request_url, $lien)) { // on est dans un super-répertoire de la page demandée
				$active = " class='active'";
				$chemin .= "|".$titre;
			}
			$menu .= "<li><a href ='".$lien."'".$active.">".$titre."</a><ul>";
		}
		foreach ($fichiers as $fichier) {
			if (is_dir($repname . "/" . $fichier) && $fichier != "." && $fichier != "..")  {
				LectureRep($repname . $fichier . "/");
			}
			elseif (substr($fichier, -3, 3) == ".md" && $fichier != "index.md" && $fichier != "404.md") { 
				$titre = str_get_html($pd->text(file_get_contents($repname."/".$fichier)))->find('h1', 0)->innertext;
				$lien = $base_url.str_replace(CONTENT_DIR, '', $repname).substr($fichier, 0, -3); 
				$active = "";
				if ($lien == $request_url) {
					$active = " class='active'";
					$chemin .= "|".$titre;
				}
				$menu .= "<li><a href ='".$lien."'".$active.">".$titre."</a></li>";
			}
		}
		$menu .= "</ul>"."</li>";
	}
}
LectureRep($dir);
$out2->find("nav", 0)->innertext = $menu;

// On indique la où on est dans la div #chemin
$out2->find('#chemin', 0)->innertext = $chemin;


// Section contenu
$out2->find("section", 0)->innertext = $parsed;

// Pied de page

// Ajout du script
$out2->find('body', 0)->innertext .= '<script async src = "'.$base_url.'theme/script.js"></script>';
    


// ***************************************************************************
// On renvoie le résultat (minification éventuellement)
// ***************************************************************************
// minification à réfléchir (problème sur les codes par exemple)
//$out2min = str_replace(array("\r", "\n", "\t"), '', $out2);
//$out2min = str_replace("[ ]*", "", $out2min);
echo $out2;
?>