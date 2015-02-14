<?php
// czyli wprowadza pan jakies zmiany w plikach itd itp
// jeszcze jeden comit
/*
PikoCMS v0.9.7
Autor: Mendax - http://www.fabrykaspamu.pl
Licencja: CC-BY-SA http://creativecommons.org/licenses/by-sa/3.0/
*/
//SEKCJA ZMIENNYCH
$admpass='pswf';		//haslo admina - KONIECZNIE ZMIEN!
$tplfile='template.tpl';//nazwa pliku z szablonem
$phpintpl=false;		//okresla czy w pliku template jest kod php
$fileext='.html';		//rozszerzenie podstron
$rssfile='rss.xml';		//zostaw puste jesli nie chcesz generowac kanalu RSS
$upfolde='images';		//katalog do uploadu plikow

//SEKCJA INSTALACYJNA
$locpath=dirname(__FILE__).'/';//okresla lokalna sciezka
$srvname=$_SERVER['SERVER_NAME'];//okresla nazwe serwera
$dirpath=substr($_SERVER['SCRIPT_NAME'],0,-9);//okresla sciezke na serwerze
if(!file_exists($locpath.'.htaccess')||filesize($locpath.'.htaccess')==0)//sprawdza plik .htaccess
	crht();//jesli nie istnieje to proba tworzenia
if(!file_exists($locpath.'data.php')||filesize($locpath.'data.php')==0){//sprawdza plik data.php
	crdp();//jesli nie istnieje to proba tworzenia
	header('Location: http://'.$srvname.$dirpath.'?adm');//i przejscie na sekcje admin
	die;
}
//SEKCJA GLOWNA
if(!isset($_GET['adm'])){//nie ma wejscia do sekcji admin
	$basenametemp=basename($_SERVER['REQUEST_URI']);
	switch($basenametemp){//sprawdza czy wywolany jeden ze specjalnych plikow
		case $rssfile:
			if($rssfile!=''){showrss();die;}//czy tworzony rss
			break;
		case 'robots.txt':
			srbts();die;
		case 'sitemap.xml':
			ssmap();die;
	}
	if($dirpath!=$_SERVER['REQUEST_URI']){//czy wywolana jest podstrona(sciezka glowna+plik)
		$requri=basename(substr($_SERVER['REQUEST_URI'],0,strpos($_SERVER['REQUEST_URI'],$fileext)));//okresla nazwe podstrony z koncowka
		if($requri==''||$requri=='index'){//jesli nie znaleziono koncowki lub podstrona to index
			header('HTTP/1.1 301 Moved Permanently');//i przejscie na strone glowna
			header('Location: http://'.$srvname.$dirpath);
			die;
		}
	}else{//jesli strona glowna
		$requri='index';
	}
	include_once($locpath.'data.php');
	if(!isset($metatitle[$requri])){//brak danych o wywolywanej podstronie z koncowka fileext
		header('HTTP/1.1 301 Moved Permanently');//zatem przejscie na glowna
		header('Location: http://'.$srvname.$dirpath);
		die;
	}
	if(!file_exists($locpath.$tplfile))//sprawdza czy istnieje plik szablonu
		die('Brak szablonu: '.$tplfile);//jesli nie to koniec
	$tplfile=file_get_contents($locpath.$tplfile);//pobiera plik szablonu
	$tplfile=str_replace('{SIDEBAR}',$sidebar,$tplfile);//zmiana tagow na dane wlasciwe
	$tplfile=str_replace('{FOOTER}',$footer,$tplfile);
	$tplfile=str_replace('{TITLE}',$metatitle[$requri],$tplfile);
	$tplfile=str_replace('{DESC}',$metadesc[$requri],$tplfile);
	$tplfile=str_replace('{H1}',$conth1[$requri],$tplfile);
	$tplfile=str_replace('{H2}',$conth2[$requri],$tplfile);
	$tplfile=str_replace('{CONTENT}',$contmain[$requri],$tplfile);
	$tpllinks='';
	foreach($metatitle as $t1 => $t2){//tworzy linki do poszczegolnych podstron
		if($t1!='index'){
			$tpllinks.="<li><a href=\"$t1$fileext\">$t2</a></li>\r\n";
		}else{//jesli glowna to link niech prowadzi do domeny zamiast do index
			$tpllinks.="<li><a href=\"http://$srvname$dirpath\">$t2</a></li>\r\n";
		}
	}
	$tplfile=str_replace('{LINKS}',$tpllinks,$tplfile);//zmiana tagu {LINKS} na liste podstron
	if($rssfile!='')//jesli tworzy kanal RSS: odpowiedni wpis w sekcji head
		$tplfile=str_replace('</head>',"<link href=\"$rssfile\" type=\"application/rss+xml\" rel=\"alternate\" title=\"RSS\" />\r\n</head>",$tplfile);
	if($phpintpl)eval('?>'.$tplfile); else echo $tplfile;//druk gotowej strony
//SEKCJA ADMINISTRACYJNA
}else{//proba wejscia do sekcji admin
	session_start();
	echo'<html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="robots" content="noindex,nofollow"></head><body><form method="POST" action="?adm">';
	if(isset($_GET['logout'])&&$_SESSION['admin']=='ok'){//jesli wylogowanie
		unset($_SESSION['admin']);
		echo'Wylogowano!<br>';
	}
	if($_SESSION['admin']!='ok'){//jesli w sesji nie zapisano info o zalogowaniu do admin
		if($_POST['pswd']!=$admpass){//sprawdza czy przesylane haslo
			//nie jest wiec wyswietla formularz log. i koniec
			echo'<input type="password" name="pswd"><input type="submit" value="Log"></form></body></html>';
			die;
		}else{//haslo sie zgadza wiec zapis w sesji info o tym i przejscie dalej
			$_SESSION['admin']='ok';
		}
	}
	if(isset($_POST['a'])){//jesli wysylane info o edycji artow
		$p_text="<?php\r\n\$sidebar='".fixslash($_POST['p_sidebar'])."';\r\n\$footer='".fixslash($_POST['p_footer'])."';\r\n";
		for($i=1;$i<21;++$i){//petla zbiera dane o poszczegolnych artach
			if($_POST['p_title'.$i]!=''){
				if($i==1)//jesli strona glowna to URL=index
					$fixedurl='index';
				elseif($_POST['p_url'.$i]=='')//jesli brak przeslanego URL-a przez usera: tworzy z tytulu
					$fixedurl=createurl($_POST['p_title'.$i]);
				else//user podal URL wiec zapis
					$fixedurl=$_POST['p_url'.$i];
				$p_text.="\$metatitle['$fixedurl']='".fixslash($_POST['p_title'.$i])."';\r\n\$metadesc['$fixedurl']='".fixslash($_POST['p_desc'.$i])."';\r\n\$conth1['$fixedurl']='".fixslash($_POST['p_conth1'.$i])."';\r\n\$conth2['$fixedurl']='".fixslash($_POST['p_conth2'.$i])."';\r\n\$contmain['$fixedurl']='".fixslash($_POST['p_contmain'.$i])."';\r\n";//zbiera dane o arcie do zapisu pliku data.php
			}
		}
		$p_text.='?>';
		$fp=fopen($locpath.'data.php','w');//zapis do pliku
		fwrite($fp,$p_text);
		fclose($fp);
		echo'OK - <a href="http://pingomatic.com/ping/?title='.urlencode($_POST['p_title1']).'&blogurl='.urlencode('http://'.$srvname.$dirpath).((isset($rssfile))?'&rssurl='.urlencode('http://'.$srvname.$dirpath.$rssfile):'').'&chk_technorati=on&chk_feedburner=on&chk_google=on" target="_blank">PING?</a><br>';
	}
	if(isset($_POST['u'])){//jesli wysylanie pliku
		$pl_tmp=$_FILES['plk']['tmp_name'];
		$pl_name=$_FILES['plk']['name'];
		if(is_uploaded_file($pl_tmp)){
			move_uploaded_file($pl_tmp,"$upfolde/$pl_name");
			echo"OK - plik: http://$srvname$dirpath$upfolde/$pl_name<br>";
		}
	}
	include_once($locpath.'data.php');//wyswietla arty
	echo'<a href="http://'.$srvname.$dirpath.'" target="_blank">Glowna</a> | <a href="?adm&logout">Wylogowanie</a><br><table><tr><th>Sidebar *:</th><td><textarea cols="60" rows="5" name="p_sidebar">'.$sidebar.'</textarea></td></tr><tr><th>Footer *:</th><td><textarea cols="60" rows="3" name="p_footer">'.$footer.'</textarea></td></tr>';
	$i=1;
	foreach($metatitle as $t1 => $t2){//wyswietla po kolei istniejace arty
		echo"<tr><th>MetaTitle $i:</th><td><input name=\"p_title$i\" value=\"".htmlspecialchars($metatitle[$t1])."\" style=\"width:502px\"></td></tr>";
		if($i!=1)echo"<tr><th>URL $i *:</th><td><input name=\"p_url$i\" value=\"".htmlspecialchars($t1)."\" style=\"width:502px\"></td></tr>";
		echo"<tr><th>MetaDescription $i *:</th><td><input name=\"p_desc$i\" value=\"".htmlspecialchars($metadesc[$t1])."\" style=\"width:502px\"></td></tr>";
		echo"<tr><th>ContentH1 $i *:</th><td><input name=\"p_conth1$i\" value=\"".htmlspecialchars($conth1[$t1])."\"  style=\"width:502px\"></td></tr>";
		echo"<tr><th>ContentH2 $i *:</th><td><input name=\"p_conth2$i\" value=\"".htmlspecialchars($conth2[$t1])."\"  style=\"width:502px\"></td></tr>";
		echo"<tr><th>MainContent $i:</th><td><textarea name=\"p_contmain$i\" cols=\"60\" rows=\"12\">{$contmain[$t1]}</textarea></td></tr>";
		echo'<tr><td colspan="2"><hr></td></tr>';
		++$i;
	}
	echo"<tr><th>MetaTitle $i:</th><td><input name=\"p_title$i\" style=\"width:502px\"></td></tr><tr><th>URL $i *:</th><td><input name=\"p_url$i\" style=\"width:502px\"></td></tr><tr><th>MetaDescription $i *:</th><td><input name=\"p_desc$i\" style=\"width:502px\" /></td></tr><tr><th>ContentH1 $i *:</th><td><input name=\"p_conth1$i\" style=\"width:502px\" /></td></tr><tr><th>ContentH2 $i *:</th><td><input name=\"p_conth2$i\" style=\"width:502px\" /></td></tr><tr><th>MainContent $i:</th><td><textarea name=\"p_contmain$i\" cols=\"60\" rows=\"12\"></textarea></td></tr>";//formularz dodawania nowego artu
	echo'<tr><td colspan="2" style="text-align:right">*-opcjonalnie&nbsp;<input type="hidden" name="a" value="o"><input type="submit"></td><tr/></table></form><hr><form enctype="multipart/form-data" method="POST" action="?adm">Plik: <input name="plk" type="file"> <input type="hidden" name="u" value="o"><input type="submit"></form></body></html>';
}
die;
//SEKCJA FUNKCJI
function showrss(){//wyswietla kanal RSS
	global $locpath,$srvname,$dirpath,$fileext;
	include_once($locpath.'data.php');
	$datemodif=date(DATE_RFC822,filemtime($locpath.'data.php'));//ustala date ostatniej modyfikacji pliku data.php
	header('Content-Type: text/xml');
	echo"<?xml version=\"1.0\"  encoding=\"UTF-8\"?>\r\n<rss version=\"2.0\">\r\n  <channel>\r\n";
	echo"    <title>{$metatitle['index']}</title>\r\n";
	echo"    <link>http://$srvname$dirpath</link>\r\n";
	echo"    <description>{$metatitle['index']} - kanał RSS</description>\r\n";
	echo"    <lastBuildDate>$datemodif</lastBuildDate>\r\n";
	foreach($metatitle as $t1 => $t2){//petla po kolejnych artykulach
		echo"    <item>\r\n      <title>$t2</title>\r\n";
		echo"      <link>http://$srvname$dirpath".(($t1!='index') ? $t1.$fileext: '')."</link>\r\n";
		echo"      <description>$t2 - {$metadesc[$t1]}</description>\r\n";
		echo"    </item>\r\n";
	}
	echo"  </channel>\r\n</rss>";
}
function srbts(){//wyswietla robots.txt
	global $srvname,$dirpath;
	header('Content-Type: text/plain');
	echo"Sitemap: http://$srvname$dirpath"."sitemap.xml
User-agent: *
Disallow: ";
}
function ssmap(){//wyswietla plik sitemap
	global $locpath,$srvname,$dirpath,$fileext;
	include_once($locpath.'data.php');
	$datemodif=date('Y-m-d',filemtime($locpath.'data.php'));//ustala date ostatniej modyfikacji pliku data.php
	header('Content-Type: text/xml');
	echo"<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\r\n";
	foreach($metatitle as $t1 => $t2){//petla po kolejnych artykulach
		echo"	<url>\r\n";
		echo"		<loc>http://$srvname$dirpath".(($t1!='index') ? $t1.$fileext: '')."</loc>\r\n";
		echo"		<lastmod>$datemodif</lastmod>\r\n";
		echo"	</url>\r\n";
	}
	echo'</urlset>';
}
function crht(){//tworzy .htaccess
	global $locpath,$srvname,$dirpath;
	$htdata="RewriteEngine On
RewriteBase $dirpath
RewriteCond %{HTTP_HOST} ^".(substr($srvname,0,4)=='www.' ? str_replace('www.','',$srvname):"www.$srvname")." [NC]
RewriteRule ^(.*)$ http://$srvname%{REQUEST_URI} [R=301,L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . $dirpath"."index.php [L]
";
	$fp=fopen($locpath.'.htaccess','w');
	fwrite($fp,$htdata);
	fclose($fp);
}
function crdp(){//tworzy pierwszy plik z danymymi
	global $locpath,$srvname,$dirpath;
	$dataphp="<?php
\$sidebar='Sidebar';
\$footer='&copy; 2009';
\$metatitle['index']='Title';
\$metadesc['index']='Description';
\$conth1['index']='H1 title';
\$conth2['index']='H2 title';
\$contmain['index']='Content';
?>";
	$fp=fopen($locpath.'data.php','w');
	fwrite($fp,$dataphp);
	fclose($fp);
}
function fixslash($str){//jak stripslashes, ale bez zmiany '
	$str=str_replace('\\"','"',$str);
	$str=str_replace('\\\\','\\',$str);
	return $str;
}
function hex2asc($str){//zrodlo: http://www.php.net/hexdec#54002
    $p='';
    for($i=0;$i<strlen($str);$i=$i+2)
        $p.=chr(hexdec(substr($str, $i, 2)));
    return $p;
}
function createurl($title){//tworzy URLe bez smieci
	$url=str_replace(' ','-',$title);
	$utfchars=array(hex2asc("C484"),hex2asc("C485"),hex2asc("C486"),hex2asc("C487"),hex2asc("C498"),hex2asc("C499"),hex2asc("C581"),hex2asc("C582"),hex2asc("C583"),hex2asc("C584"),hex2asc("C393"),hex2asc("C3B3"),hex2asc("C59A"),hex2asc("C59B"),hex2asc("C5BB"),hex2asc("C5BC"),hex2asc("C5B9"),hex2asc("C5BA"));
	$normchars=array('a','a','c','c','e','e','l','l','n','n','o','o','s','s','z','z','z','z');
	$url=str_replace($utfchars,$normchars,$url);//usuwa ogonki
	$url=strtolower(preg_replace('|[^a-z0-9-_.;,]|i','',$url));//usuwa niestandardowe znaki i zmienia na male litery
	return $url;
} ?>