<?php

/**

* Web based SQLite management

* @package SQLiteManager

* @version $Id: polish.inc.php,v 1.42 2006/04/14 15:16:52 freddy78 Exp $ $Revision: 1.42 $

*/

/*

	(c) Tomasz "Zyx" Jêdrzejewski (zyxwvu@users.sourceforge.net)

*/

$charset = 'iso-8859-2';
$langSuffix = 'en';

$itemTranslated = array(	"Table"		=> "Tabele", 
										"View"		=> "Widoki",
										"Trigger"	=> "Triggery",
										"Function"	=> "Funkcje");
							

$langueTranslated = array(	1=>"French", 2=>"English", 3=>"Polish", 4=>"German", 
							5=>"Japanese", 6=>"Italian", 7=>"Croatian", 
							8=>"Brazilian portuguese", 9=>"Netherlands", 
							10=>"Spanish", 11=>"Danish", 12=>"Chinese traditional", 
              				13=>"Chinese simplified");

$themeTranslated = array("default"=>"Default",  "PMA"=>"PMA", "jall"=>"JALL", "green"=>"Green");

$TEXT[1]	=	"Indeks";
$TEXT[2]	=	"Witaj w <a href=\"http://www.sqlitemanager.org\" target=\"_blank\">SQLiteManager</a> v.";
$TEXT[3]	=	"wersja SQLite";
$TEXT[4]	=	"Dokumentacja SQLite";
$TEXT[5]	=	"Jêzyk SQL";
$TEXT[6]	=	"Modu³ SQLite nie mo¿e zostaæ za³adowany!";
$TEXT[7]	=	"Nie mo¿na otworzyæ konfiguracyjnej bazy danych 'SQLite'.<br>Komunikat:";
$TEXT[8]	=	"Baza konfiguracyjna jest tylko do odczytu!";
$TEXT[9]	=	"B³±d";
$TEXT[10]	=	"funkcja";
$TEXT[11]	=	"Agregacja";
$TEXT[12]	=	"Final function";
$TEXT[13]	=	"Argumentów";
$TEXT[14]	=	"Modyfikuj";
$TEXT[15]	=	"Skasuj";
$TEXT[16]	=	"W³a¶ciwo¶ci nowej funkcji";
$TEXT[17]	=	"W³a¶ciwo¶ci funkcji";
$TEXT[18]	=	"B³±d: Wszystkie pola musz± byæ wype³nione.";
$TEXT[19]	=	"Nazwa";
$TEXT[20]	=	"Typ";
$TEXT[21]	=	"Instrukcje PHP";
$TEXT[22]	=	"Final Properties";
$TEXT[23]	=	"Argumentów";
$TEXT[24]	=	"Dodaj tê funkcjê do wszystkich baz danych.";
$TEXT[25]	=	"W³a¶ciwo¶ci nowej tabeli";
$TEXT[26]	=	"W³a¶ciwo¶ci tabeli";
$TEXT[27]	=	"Pole";
$TEXT[28]	=	"Typ";
$TEXT[29]	=	"D³ugo¦æ";
$TEXT[30]	=	"Puste";
$TEXT[31]	=	"Domy¶lny";
$TEXT[32]	=	"Klucz G³ówny";
$TEXT[33]	=	"Akcja";
$TEXT[34]	=	"Zaznacz wszystko";
$TEXT[35]	=	"Odznacz wszystko";
$TEXT[36]	=	"Zaznaczone pola";
$TEXT[37]	=	"Czy jeste¶ pewien, ¿e chcesz skasowaæ te pola?";
$TEXT[38]	=	"Zarz±dzanie indeksami";
$TEXT[39]	=	"Czy jeste¶ pewien, ¿e chcesz skasowaæ te pola?";
$TEXT[40]	=	"Tak";
$TEXT[41]	=	"Nie";
$TEXT[42]	=	"Primary";
$TEXT[43]	=	"Dodaj";
$TEXT[44]	=	"Pole(a)";
$TEXT[45]	=	"Na koñcu tabeli";
$TEXT[46]	=	"Na pocz±tku tabeli";
$TEXT[47]	=	"Po";
$TEXT[48]	=	"Wstawianie nowego rekordu";
$TEXT[49]	=	"Modyfikacja rekordu";
$TEXT[50]	=	"Warto¶æ";
$TEXT[51]	=	"Zapisz";
$TEXT[52]	=	"Wstaw dane z pliku.";
$TEXT[53]	=	"triggera";
$TEXT[54]	=	"W³a¶ciwo¶ci nowego triggera";
$TEXT[55]	=	"W³a¶ciwo¶ci triggera";
$TEXT[56]	=	"Moment";
$TEXT[57]	=	"Zdarzenie";
$TEXT[58]	=	"Tabela";
$TEXT[59]	=	"Warunek";
$TEXT[60]	=	"Instrukcje";
$TEXT[61]	=	"W³a¶ciwo¶ci";
$TEXT[62]	=	"W³a¶ciwo¶ci nowego widoku";
$TEXT[63]	=	"W³a¶ciwo¶ci widoku";
$TEXT[64]	=	"¯adne zapytanie nie zosta³o wykonane!";
$TEXT[65]	=	"W³a¶ciwo¶ci widoku";
$TEXT[66]	=	"Wykonaj jedno, lub wiêcej <b>zapytañ";
$TEXT[67]	=	"<i>lub</i> z pliku SQL";
$TEXT[68]	=	"Format zapytañ : Standardowy (sqlite)";
$TEXT[69]	=	"Wykonaj";
$TEXT[70]	=	" - ilo¶æ wykonanych zapytañ";
$TEXT[71]	=	" - ilo¶æ zmodyfikowanych rekordów";
$TEXT[72]	=	"Struktura";
$TEXT[73]	=	"Wy¶wietl";
$TEXT[74]	=	"SQL";
$TEXT[75]	=	"Wstaw";
$TEXT[76]	=	"Eksportuj";
$TEXT[77]	=	"Wyczy¶æ";
$TEXT[78]	=	"Czy naprawdê chcesz usun±æ tê funkcjê?";
$TEXT[79]	=	"Czy naprawdê chcesz wyczy¶ciæ tê tabelê?";
$TEXT[80]	=	"Czy naprawdê chcesz skasowaæ tê tabelê?";
$TEXT[81]	=	"Dodaj";
$TEXT[82]	=	"Czy naprawdê chcesz skasowaæ ten widok?";
$TEXT[83]	=	"Czy naprawdê chcesz skasowaæ ten trigger?";
$TEXT[84]	=	"Opcje";
$TEXT[85]	=	"Czy jeste¶ pewien, ¿e chcesz usun±æ tê bazê danych? Wci¶niêcie <<<Tak>>> NIE spowoduje usuniêcia pliku bazy, a tylko niemo¿no¶æ dalszej nad nim pracy w SQLite Manager.";
$TEXT[86]	=	"Skasuj";
$TEXT[87]	=	"Dodaj nowy widok";
$TEXT[88]	=	"Dodaj nowy trigger";
$TEXT[89]	=	"Dodaj now± funkcjê";
$TEXT[90]	=	"Zapytanie SQL";
$TEXT[91]	=	"Nazwa klucza";
$TEXT[92]	=	"Czy na pewno chcesz usun±æ indeks";
$TEXT[93]	=	"Dodaj indeks na";
$TEXT[94]	=	"kolumn(ê)";
$TEXT[95]	=	"Ignoruj";
$TEXT[96]	=	"Dodaj do klucza";
$TEXT[97]	=	"Stwórz widok o nazwie ";
$TEXT[98]	=	"na podstawie tego zapytania.";
$TEXT[99]	=	"B³êdy w linii/liniach";
$TEXT[100]	=	"Nie mo¿na zmodyfikowaæ bazy konfiguracyjnej z powodu problemów z uprawnieniami (brak mo¿liwo¦ci zapisu)";
$TEXT[101]	=	"Nie mo¿na stworzyæ/odczytaæ bazy danych";
$TEXT[102]	=	"Wszystkie pola musz± zostaæ wype³nione!";
$TEXT[103]	=	"Stwórz bazê danych";
$TEXT[104]	=	"¦cie¿ka";
$TEXT[105]	=	"Pojedyncza linia mo¿e zawieraæ tylko jedno zapytanie. Podziel twoje zapytania tak, aby ka¿de z nich zawarte by³o w osobnej linii.";
$TEXT[106]	=	"Parametr(y) konstruktora klasy 'Grid' musz± byæ tablic±";
$TEXT[107]	=	"The column alignement array doesn't haven't a good number of element.";
$TEXT[108]	=	"The cells alignement must be : 'left', 'right' ou 'center'";
$TEXT[109]	=	"The paramaters for the columns alignement must be an array";
$TEXT[110]	=	"The paramaters for the columns format must be an array";
$TEXT[111]	=	"Tablica sortowania kolumn zawiera nieprawid³ow± liczbê elementów.";
$TEXT[112]	=	"Parametry sortowania musz± byæ ustawione na 0=brak sortowania, LUB 1=sortuj.";
$TEXT[113]	=	"The paramaters for the columns sort must be an array";
$TEXT[114]	=	"The format string for the calculate column is empty.";
$TEXT[115]	=	"The title is obligatory for a calculate column.";
$TEXT[116]	=	"Parametr(y) konstruktora klasy 'ArrayToGrid' musz± byæ tablic±.";
$TEXT[117]	=	"Nie mo¿na policzyæ ilo¶ci rekordów.";
$TEXT[118]	=	"Il. rekordów";
$TEXT[119]	=	"Wstaw";
$TEXT[120]	=	"Czy na pewno chcesz skasowaæ";
$TEXT[121]	=	"";
$TEXT[122]	=	"";
$TEXT[123]	=	"Jeste¶ pewien, ¿e chcesz wyczy¶ciæ tê tabelê?";
$TEXT[124]	=	"Tylko struktura";
$TEXT[125]	=	"Struktura i dane";
$TEXT[126]	=	"Tylko dane";
$TEXT[127]	=	"Pe³ne zapytania INSERT";
$TEXT[128]	=	"¦ci±gnij na dysk";
$TEXT[129]	=	"Host";
$TEXT[130]	=	"Data utworzenia";
$TEXT[131]	=	"Baza danych";
$TEXT[132]	=	"Struktura tabeli";
$TEXT[133]	=	"Zrzut danych z tabeli";
$TEXT[134]	=	"Struktura widoku";
$TEXT[135]	=	"W³a¶ciwo¶ci funkcji u¿ytkownika";
$TEXT[136]	=	"rekordów";
$TEXT[137]	=	"Plik";
$TEXT[138]	=	"Nadpisz zawarto¶æ";
$TEXT[139]	=	"Separator";
$TEXT[140]	=	"Importuj dane z pliku tekstowego";
$TEXT[141]	=	"Jêzyk";
$TEXT[142]	=	"Styl";
$TEXT[143]	=	"Upload Database";
$TEXT[144]	=	"Katalog uploadu nie jest dostêpny.<br />(Zmodyfikuj sta³± 'DEFAULT_DB_PATH' w pliku 'include/user_defined.inc.php')";
$TEXT[145]	=	"Wyja¶nij SQL";
$TEXT[146]	=	"Management of attached databases";
$TEXT[147]	=	"You are not allowed to acces here.You must enter valid login and password.";
$TEXT[148]	=	"This login is not valid.";
$TEXT[149]	=	"This password don't correspond with thie login.";
$TEXT[150]	=	"wersja PHP";
$TEXT[151] = 	"After save";
$TEXT[152] = 	"Go back to previous page";
$TEXT[153] = 	"Insert another new row";
$TEXT[154] = 	"The configuration database is in read only.<br>Some feature of SQLiteManager can't work correctly.";
$TEXT[155] = 	"This database is in read only.";
$TEXT[156] = 	"Privileges"; 
$TEXT[157] = 	"Change password"; 
$TEXT[158] = 	"Logoff"; 
$TEXT[159] = 	"Add user"; 
$TEXT[160] = 	"Add groupe"; 
$TEXT[161] = 	"User overview"; 
$TEXT[162] = 	"Groupes overview"; 
$TEXT[163] = 	"Name"; 
$TEXT[164] = 	"Login"; 
$TEXT[165] = 	"Groupe"; 
$TEXT[166] = 	"execSQL"; 
$TEXT[167] = 	"data"; 
$TEXT[168] = 	"export"; 
$TEXT[169] = 	"empty"; 
$TEXT[170] = 	"del"; 
$TEXT[171] = 	"Incorrect old password."; 
$TEXT[172] = 	"Password and confirmation are differente."; 
$TEXT[173] = 	"The password have been changed"; 
$TEXT[174] = 	"Clic here for re-logon"; 
$TEXT[175] = 	"Old :"; 
$TEXT[176] = 	"New :"; 
$TEXT[177] = 	"Confirm :"; 
$TEXT[178] = 	"Information"; 
$TEXT[179] = 	"Location :"; 
$TEXT[180] = 	"Size :"; 
$TEXT[181] = 	"Rights :"; 
$TEXT[182] = 	"Last modified :"; 
$TEXT[183] = 	"Integrity Check"; 
$TEXT[184] = 	"Vacuum"; 
$TEXT[185] = 	"Default synchronous :"; 
$TEXT[186] = 	"Default cache_size :"; 
$TEXT[187] = 	"OFF "; 
$TEXT[188] = 	"NORMAL "; 
$TEXT[189] = 	"FULL "; 
$TEXT[190]	= 	"Access control management"; 
$TEXT[191]	= 	"Yes"; 
$TEXT[192]	= 	"No"; 
$TEXT[193]	= 	"Default Temporary Storage"; 
$TEXT[194]	= 	"DEFAULT"; 
$TEXT[195]	= 	"MEMORY"; 
$TEXT[196]	= 	"FILE"; 
$TEXT[197]	= 	"Unique"; 
$TEXT[198]	= 	"Index"; 
$TEXT[199]	= 	"Data";
$TEXT[200]	= 	"Apply";
$TEXT[201]	=	"Selection";
$TEXT[202]	=	"Operator";
$TEXT[203]	=	"additional condition :";
$TEXT[204]	=	"AND";
$TEXT[205]	=	"OR";
$TEXT[206]	=	"Select";
$TEXT[207]	=	"Search";
$TEXT[208]	=	"Rename";
$TEXT[209]	=	"Move";
$TEXT[210]	=	"Copy";
$TEXT[211]	=	"Plugins";
$TEXT[212]	=	"Maintenance";
$TEXT[213]	=	"Query time :";
$TEXT[214]	=	"msec.";
$TEXT[215]	=	"Rename table to :";
$TEXT[216]	=	"Move table to (database.table) :";
$TEXT[217]	=	"Copy table to (database.table) :";
$TEXT[218]	=	"Add DROP TABLE";
$TEXT[219]	=	"Save as New Row";
$TEXT[220]	=	"Save";
$TEXT[221]	=	"Save Type";
$TEXT[222]  =   "Operation";
$TEXT[223]  =   "Update";
$TEXT[224]  =   "Tip : You can use internal PHP functions in your queries !";
$TEXT[225]  =   "Truncated text";
$TEXT[226]  =   "Full text";
$TEXT[227]  =   "-- select --";
$TEXT[228]  =   "(s)";
$TEXT[229]  =   "Version";
$TEXT[230]  =   "(new database)";
$TEXT[231]  =   "Official SQliteManager Homepage";
$TEXT[232]  =   "The database can't be attain";
$TEXT[233]  =   "Trigger structure";
?>