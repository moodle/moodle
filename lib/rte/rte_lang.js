////////////////////////////////////////////////////////////////////////////////
//
// HTML Text Editing Component for hosting in Web Pages
// Copyright (C) 2001  Ramesys (Contracting Services) Limited
//
// This library is free software; you can redistribute it and/or
// modify it under the terms of the GNU Lesser General Public
// License as published by the Free Software Foundation; either
// version 2.1 of the License, or (at your option) any later version.
//
// This library is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
// Lesser General Public License for more details.
//
// You should have received a copy of the GNU LesserGeneral Public License
// along with this program; if not a copy can be obtained from
//
//    http://www.gnu.org/copyleft/lesser.html
//
// or by writing to:
//
//    Free Software Foundation, Inc.
//    59 Temple Place - Suite 330,
//    Boston,
//    MA  02111-1307,
//    USA.
//
// Original Developer:
//
//	Austin David France
//	Ramesys (Contracting Services) Limited
//	Mentor House
//	Ainsworth Street
//	Blackburn
//	Lancashire
//	BB1 6AY
//	United Kingdom
//  email: Austin.France@Ramesys.com
//
// Home Page:    http://richtext.sourceforge.net/
// Support:      http://richtext.sourceforge.net/
//
////////////////////////////////////////////////////////////////////////////////
//
// Authors & Contributers:
//
//	OZ			Austin David France		[austin.france@ramesys.com]
//					Primary Developer
//
//	TE			Torbjørn Engedal		[torbjoen@stud.ntnu.no]
//					Doc. Translator
//
//	GE			Herfurth, Gerrit		[gerrit.herfurth@gs-druckfarben.de]
//
//	BC			Bill Chalmers			[bill_paula@btinternet.com]
//
// History:
//
//	OZ		16-02-2002
//			Initial Implementation
//
//	TE		17-02-2002
//			Norwegian Translation
//
//	GE		05-06-2002
//			German Translation
//
//	OZ		01-07-2002
//			Extended EN translation to include table editing.  Other languages
//			to follow.
//
// 	BC		21-07-2002
//			Fixed bug no: 584424, trying to set lang equal to local[lang] caused error
//			if the users local lang was not in the predefined locale array.
//
//	BC		31-07-2002
//			Added french translation courtesy of Arnaud Vatel.
//
//	OZ		27-08-2002
//			Added Russian Translation - submitted by Artem Orlov [art@ural.ru]
//
//	OZ		27-08-2002
//			Added Turkish Translation - submitted by Fatih BOY <fatih_boy@yahoo.com>
//
//	OZ		27-08-2002
//			Fix bug where missing text in non-en/us language was not falling back
//			to en/us text.
//
//	OZ		27-08-2002
//			Added Italiano Translation - submitted by Angelo Del Mazza <delmazza@a99.it>
//
//	BC		04-09-2002
//			Added Dutch Translation - Courtesy of levOOware, Marja Ribbers-de Vroed
//
//	BC		31-10-2002
//			Added Portugese (Brazilian) Translation - Courtesy of Ricardo Colombani de Sousa <colombani@ig.com.br>
//
//	BC		31-10-2002
//			Added Danish Translation - Courtesy of Morten Flyger <flyger@email.dk>
//
//	BC		31-10-2002
//			Added bold, underline and italic icons sources for en-us				
////////////////////////////////////////////////////////////////////////////////

var locale = new Object;

// locale.getLanguage(): Called to work out what language to use.
locale.getLanguage = function()
{
	return locale.language ? locale.language : navigator.userLanguage;
}

// locale.getString(): Called to return the language variant of a @code string.
// this routin will fall back to en-us is no language variant is found.  If no
// english version exists, the code is returned.
locale.getString = function(str, lang)
{
	// If not supplied, pick up the language to use
	if (!lang) lang = locale.getLanguage();

	// Get references to required languages 
	if (!locale[lang])
	{
		enus = lang = locale["en-us"];
	}
	else
	{
		lang = locale[lang];
		enus = locale["en-us"];
	}

	// Find the end of the text code
	var i = str.indexOf('@{');
	while (i != -1)
	{
		// Find the closing } 
		var j = str.indexOf('}', i+1);

		// Extrace the language code
		var code = str.substr(i+2,j-i-2);

		// Return the language version of the text
		if (lang[code]) {
			str = str.substr(0,i) + lang[code] + str.substr(i+j+1);
		} else {
			if (enus[code]) {
				str = str.substr(0,i) + enus[code] + str.substr(i+j+1);
			}
		}
	
		// Find the next code if any
		i = str.indexOf('@{', i+1);
	}

	// Untranslated
	return str;
}

// locale.setLocale(): Called once the editor has loaded to replace all language
// codes in alt, title and innerText with thier language counterparts.
locale.setLocale = function()
{
	// Work out which language to apply
	var lang = locale.getLanguage();

	// Enumerate all elements within the document
	for (var i = 0; i < document.all.length; i++)
	{
		// Get a reference to this element
		var el = document.all(i);

		// Translate the alt attribute (alternate image text) if required
		if (el.alt && el.alt.indexOf('@{') != -1) {
			el.alt = locale.getString(el.alt, lang);
		}

		// Translate the title attribute (tooltip) if required
		if (el.title && el.title.indexOf('@{') != -1) {
			el.title = locale.getString(el.title, lang);
		}

		// Translate the src attribute (image/script source url) if required
		if (el.src && el.src.indexOf('@{') != -1) {
			el.src = locale.getString(el.src, lang);
		}

		// Translate bottom level (leaf nodes) innerText if required.
		if (!el.children.length && el.innerText && el.innerText.indexOf('@{') != -1) {
			el.innerText = locale.getString(el.innerText, lang);
		}
	}
}

// Arrange for translation to occur when the document has loaded
window.attachEvent("onload", locale.setLocale);

////////////////////////////////////////////////////////////////////////////////
//
// English (American & British)
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["en-us"] = locale["en-gb"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Post Topic";
	o["Cut"]				= "Cut";
	o["Copy"]				= "Copy";
	o["Paste"]				= "Paste";
	o["SpellCheck"]			= "Spell Check";
	o["SelectAll"]			= "Select All";
	o["RemoveFormatting"]	= "Remove Formatting";
	o["InsertLink"]			= "Insert Link";
	o["RemoveLink"]			= "Remove Link";
	o["InsertImage"]		= "Insert Image";
	o["InsertTable"]		= "Insert Table";
	o["EditTable"]			= "Edit Table";
	o["InsertLine"]			= "Insert Horizontal Line";
	o["InsertSmily"]		= "Insert Smily 8-)";
	o["InsertCharacter"]	= "Insert special character";
	o["About"]				= "About Richtext Editor";
	o["Bold"]				= "Bold";
	o["Italic"]				= "Italic";
	o["Underline"]			= "Underline";
	o["Strikethrough"]		= "Strikethrough";
	o["AlignLeft"]			= "Align Left";
	o["Center"]				= "Center";
	o["AlignRight"]			= "Align Right";
	o["AlignBlock"]			= "Align Block";
	o["NumberedList"]		= "Numbered List";
	o["BulettedList"]		= "Buletted List";
	o["DecreaseIndent"]		= "Decrease Indent";
	o["IncreaseIndent"]		= "Increase Indent";
	o["HistoryBack"]		= "History back";
	o["HistoryForward"]		= "History forward";
	o["TextColor"]			= "Text Color";
	o["BackgroundColor"]	= "Background Color";

	o["RemoveColspan"]		= "Remove Colspan";
	o["RemoveRowspan"]		= "Remove Rowspan";
	o["IncreaseColspan"]	= "Increase Colspan";
	o["IncreaseRowspan"]	= "Increase Rowspan";
	o["AddColumn"]			= "Add Column";
	o["AddRow"]				= "Add Row";
	o["RemoveColumn"]		= "Remove Column";
	o["RemoveRow"]			= "Remove Row";

	// Label Text
	o["Style"]				= "Style";
	o["Font"]				= "Font";
	o["Size"]				= "Size";
	o["Source"]				= "Source";

	// Titles
	o["SourceTitle"]		= "Click here to toggle between WYSIWYG and Source mode.";

	// Image Sources
	o["icon_post"]			= "images/icon_post.gif";
	o["hdr_tables"]			= "images/hdr_tables.gif";
	o["icon_bold"]			= "images/icon_bold.gif";
	o["icon_underline"]		= "images/icon_underline.gif";
	o["icon_italic"]		= "images/icon_italic.gif";

////////////////////////////////////////////////////////////////////////////////
//
// Norwegian Bokmål
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["no"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Send";
	o["Cut"]				= "Klipp";
	o["Copy"]				= "Kopier";
	o["Paste"]				= "Lim";
	o["SpellCheck"]			= "Stavekontroll";
	o["SelectAll"]			= "Marker alt";
	o["RemoveFormatting"]	= "Fjern formatering";
	o["InsertLink"]			= "Sett inn link";
	o["RemoveLink"]			= "Fjern link";
	o["InsertImage"]		= "Sett inn bilde";
	o["InsertTable"]		= "Sett inn tabell";
	o["EditTable"]			= "Endre tabell";
	o["InsertLine"]			= "Sett inn horisontal linje";
	o["InsertSmily"]		= "Sett inn smily 8-)";
	o["InsertCharacter"]	= "Sett inn spesialtegn";
	o["About"]				= "Om Richtext Editor";
	o["Bold"]				= "Fet";
	o["Italic"]				= "Kursiv";
	o["Underline"]			= "Understrekning";
	o["Strikethrough"]		= "Gjennomstrekning";
	o["AlignLeft"]			= "Venstrejustering";
	o["Center"]				= "Sentrering";
	o["AlignRight"]			= "Høyrejustering";
	o["AlignBlock"]			= "Blokkjustering";
	o["NumberedList"]		= "Nummerert liste";
	o["BulettedList"]		= "Punktliste";
	o["DecreaseIndent"]		= "Mink innrykksverdi";
	o["IncreaseIndent"]		= "Øk innrykksverdi";
	o["HistoryBack"]		= "Historie bakover";
	o["HistoryForward"]		= "Historie forover";
	o["TextColor"]			= "Tekstfarge";
	o["BackgroundColor"]	= "Bakgrunnsfarge";

	// Label Text
	o["Style"]				= "Stil";
	o["Font"]				= "Type";
	o["Size"]				= "Størrelse";
	o["Source"]				= "Kilde";

	// Titles
	o["SourceTitle"]		= "Klikk her for å bytte mellom WYSIWYG og kilde modus.";

	// Image Sources
	o["icon_post"]			= "images/lang/no.icon_post.gif";

////////////////////////////////////////////////////////////////////////////////
//
// German
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["de"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]                  = "Speichern";
	o["Cut"]                        = "Ausschneiden";
	o["Copy"]                       = "Kopieren";
	o["Paste"]                      = "Einfügen";
	o["SpellCheck"]                 = "Rechschreibprüfung";
	o["SelectAll"]                  = "Alles markieren";
	o["RemoveFormatting"]           = "Formatierung entfernen";
	o["InsertLink"]                 = "Link einfügen";
	o["RemoveLink"]                 = "Link entfernen";
	o["InsertImage"]                = "Bild einfügen";
	o["InsertTable"]                = "Tabelle einfügen";
	o["EditTable"]                  = "Tabelle bearbeiten";
	o["InsertLine"]                 = "Horizontale Linie einfügen";
	o["InsertSmily"]                = "Smily 8-) einfügen";
	o["InsertCharacter"]            = "Sonderzeichen einfügen";
	o["About"]                      = "Über Richtext Editor";
	o["Bold"]                       = "Fett";
	o["Italic"]                     = "Kursiv";
	o["Underline"]                  = "Unterstrichen";
	o["Strikethrough"]              = "Durchgestrichen";
	o["AlignLeft"]                  = "Linksbündig";
	o["Center"]                     = "Zentriert";
	o["AlignRight"]                 = "Rechtsbündig";
	o["AlignBlock"]                 = "Blocksatz";
	o["NumberedList"]               = "Nummerierung";
	o["BulettedList"]               = "Aufzählungszeichen";
	o["DecreaseIndent"]             = "Einzug verkleinern";
	o["IncreaseIndent"]             = "Einzug vergrößern";
	o["HistoryBack"]                = "Rückgängig";
	o["HistoryForward"]             = "Wiederherstellen";
	o["TextColor"]                  = "Zeichenfarbe";
	o["BackgroundColor"]            = "Hintergrundfarbe";

	// Label Text
	o["Style"]                      = "Absatzformat";
	o["Font"]                       = "Schriftart";
	o["Size"]                       = "Größe";
	o["Source"]                     = "Quelltext";

	// Titles
	o["SourceTitle"]                = "Hier klicken, um zwischen WYSIWYG- und Quelltext-Modus umzuschalten.";

	// Image Sources
	o["icon_post"]                  = "images/lang/de.icon_post.gif";

////////////////////////////////////////////////////////////////////////////////
//
// Français
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["fr"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Poster le sujet";
	o["Cut"]				= "Couper";
	o["Copy"]				= "Copier";
	o["Paste"]				= "Coller";
	o["Find Text"]			= "Rechercher";
	o["SpellCheck"]			= "Vérifier l'orthographe";
	o["SelectAll"]			= "Sélectionner tout";
	o["RemoveFormatting"]	= "Supprimer le formattage";
	o["InsertLink"]			= "Insérer un lien";
	o["RemoveLink"]			= "Supprimer un lien";
	o["InsertImage"]		= "Insérer une image";
	o["InsertTable"]		= "Insérer un tableau";
	o["EditTable"]			= "Editer le tableau";
	o["InsertLine"]			= "Insérer une ligne horizontale";
	o["InsertSmily"]		= "Insérer un Smiley 8-)";
	o["InsertCharacter"]	= "Insérer des caractères spéciaux";
	o["About"]				= "A propos de Richtext Editor";
	o["Bold"]				= "Gras";
	o["Italic"]				= "Italique";
	o["Underline"]			= "Souligné";
	o["Strikethrough"]		= "Barré";
	o["AlignLeft"]			= "Aligné à gauche";
	o["Center"]				= "Centré";
	o["AlignRight"]			= "Aligné à droite";
	o["AlignBlock"]			= "Justifié";
	o["NumberedList"]		= "Liste numérotée";
	o["BulettedList"]		= "Liste à puces";
	o["DecreaseIndent"]		= "Diminuer le retrait";
	o["IncreaseIndent"]		= "Augmenter le retrait";
	o["HistoryBack"]		= "Annuler";
	o["HistoryForward"]		= "Rétablir";
	o["TextColor"]			= "Couleur du texte";
	o["BackgroundColor"]	= "Couleur de l'arrière plan";

	o["RemoveColspan"]		= "Fractionner la cellule";
	o["RemoveRowspan"]		= "Fusionner la cellule";
	o["IncreaseColspan"]	= "Augmenter l'étendue de la colonne";
	o["IncreaseRowspan"]	= "Augmenter l'étendue de la ligne";
	o["AddColumn"]			= "Ajouter une colonne";
	o["AddRow"]				= "Ajouter une ligne";
	o["RemoveColumn"]		= "Supprimer une colonne";
	o["RemoveRow"]			= "Supprimer une ligne";

	// Label Text
	o["Style"]				= "Style";
	o["Font"]				= "Police";
	o["Size"]				= "Taille";
	o["Source"]				= "Code source";

	// Titles
	o["SourceTitle"]		= "Cliquez ici pour basculer entre Aperçu et mode Source.";

	// Image Sources
	o["icon_post"]			= "images/icon_post.gif";
	
////////////////////////////////////////////////////////////////////////////////
//
// Russian (Windows 1251)
// by Artem Orlov [art@ural.ru]
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["ru"] = locale["ru"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Ñîõðàíèòü";
	o["Cut"]				= "Âûðåçàòü";
	o["Copy"]				= "Êîïèðîâàòü";
	o["Paste"]				= "Âñòàâèòü";
	o["SpellCheck"]			= "Ïðîâåðèòü îðôîãðàôèþ";
	o["SelectAll"]			= "Âûäåëèòü âñå";
	o["RemoveFormatting"]	= "Óäàëèòü ôîðìàòèðîâàíèå";
	o["InsertLink"]			= "Âñòàâèòü ññûëêó";
	o["RemoveLink"]			= "Óáðàòü ññûëêó";
	o["InsertImage"]		= "Âñòàâèòü êàðòèíêó";
	o["InsertTable"]		= "Âñòàâèòü òàáëèöó";
	o["EditTable"]			= "Èçìåíèòü òàáëèöó";
	o["InsertLine"]			= "Âñòàâèòü ãîðèçîíòàëüíóþ ëèíèþ";
	o["InsertSmily"]		= "Âñòàâèòü ñìàéëèê 8-)";
	o["InsertCharacter"]	= "Âñòàâèòü ñïåöñèìâîë";
	o["About"]				= "Î ðåäàêòîðå";
	o["Bold"]				= "Æèðíûé";
	o["Italic"]				= "Êóðñèâ";
	o["Underline"]			= "Ïîä÷åðêíóòûé";
	o["Strikethrough"]		= "Çà÷åðêíóòûé";
	o["AlignLeft"]			= "Ïî ëåâîìó êðàþ";
	o["Center"]				= "Ïî öåíòðó";
	o["AlignRight"]			= "Ïî ïðàâîìó êðàþ";
	o["AlignBlock"]			= "Ïî øèðèíå";
	o["NumberedList"]		= "Íóìåðîâàííûé ñïèñîê";
	o["BulettedList"]		= "Ìàðêèðîâàííûé ñèñîê";
	o["DecreaseIndent"]		= "Óìåíüøèòü îòñòóï";
	o["IncreaseIndent"]		= "Óâåëè÷èòü îòñòóï";
	o["HistoryBack"]		= "Îòìåíèòü";
	o["HistoryForward"]		= "Ïîâòîðèòü";
	o["TextColor"]			= "Öâåò òåêñòà";
	o["BackgroundColor"]	= "Öâåò ôîíà";

	o["RemoveColspan"]		= "Óáðàòü îáúåäèíåíèå ñòîëáöîâ";
	o["RemoveRowspan"]		= "Óáðàòü îáúåäèíåíèå ñòðîê";
	o["IncreaseColspan"]	= "Îáúåäèíèòü ñòîëáöû";
	o["IncreaseRowspan"]	= "Îáúåäèíèòü ñòðîêè";
	o["AddColumn"]			= "Äîáàâèòü Ñòîëáåö";
	o["AddRow"]				= "Äîáàâèòü Ñòðîêó";
	o["RemoveColumn"]		= "Óäàëèòü Ñòîëáåö";
	o["RemoveRow"]			= "Óäàëèòü Ñòðîêó";

	// Label Text
	o["Style"]				= "Ñòèëü";
	o["Font"]				= "Øðèôò";
	o["Size"]				= "Ðàçìåð";
	o["Source"]				= "Â âèäå HTML";

	// Titles
	o["SourceTitle"]		= "Ùåëêíèòå çäåñü äëÿ ïåðåêþ÷åíèÿ ìåæäó WYSIWYG and ïðîñìîòðîì â âèäå HTML.";

////////////////////////////////////////////////////////////////////////////////
//
// Türkçe
// Fatih BOY <fatih_boy@yahoo.com> tarafindan Türkçeye çevirilmistir.
//
// Turkish
// Translated into Turkish by Fatih BOY <fatih_boy@yahoo.com>
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["tr"]  = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Konuyu Gönder";
	o["Cut"]				= "Kes";
	o["Copy"]				= "Kopyala";
	o["Paste"]				= "Yapýþtýr";
	o["SpellCheck"]			= "Dil Kontrolü";
	o["SelectAll"]			= "Hepsini Seç";
	o["RemoveFormatting"]	= "Biçimlemeyi Kaldýr";
	o["InsertLink"]			= "Köprü Ekle";
	//o["InsertLink"]			= "Link Ekle";
	o["RemoveLink"]			= "Köprüyü Kaldýr";
	//o["RemoveLink"]			= "Link'i Kaldýr";	
	o["InsertImage"]		= "Resim Ekle";
	o["InsertTable"]		= "Tablo Ekle";
	o["EditTable"]			= "Tabloyu Düzenle";
	o["InsertLine"]			= "Yatay Çizgi Ekle";
	o["InsertSmily"]		= "Gülümseme Ekle 8-)";
	o["InsertCharacter"]	= "Özel Karakter Ekle";
	o["About"]				= "Richtext Editörü Hakkýnda";
	o["Bold"]				= "Kalýn";
	o["Italic"]				= "Yatay";
	o["Underline"]			= "Altý çizili";
	o["Strikethrough"]		= "Çizili";
	o["AlignLeft"]			= "Sola Daya";
	o["Center"]				= "Ortala";
	o["AlignRight"]			= "Saða Daya";
	o["AlignBlock"]			= "Blokla";
	o["NumberedList"]		= "Numaralý Liste";
	o["BulettedList"]		= "Buletted Liste";
	o["DecreaseIndent"]		= "Satýr aralýðýný Azalt";
	o["IncreaseIndent"]		= "Satýr Aralýðýný Arttýr";
	o["HistoryBack"]		= "Geçmiþ - Geri";
	o["HistoryForward"]		= "Geçmiþ - Ýleri";
	o["TextColor"]			= "Yazý Rengi";
	o["BackgroundColor"]	= "Artalan Rengi";

	o["RemoveColspan"]		= "Colspan'ý Kaldýr";
	o["RemoveRowspan"]		= "Rowspan'ý Kaldýr";
	o["IncreaseColspan"]	= "Colspan'ý Arttýr";
	o["IncreaseRowspan"]	= "Rowspan'ý Arttýr";
	o["AddColumn"]			= "Sütunu Kaldýr";
	o["AddRow"]				= "Satýr Ekle";
	o["RemoveColumn"]		= "Sütun Ekle";
	o["RemoveRow"]			= "Satýrý Kaldýr";

	// Label Text
	o["Style"]				= "Stil";
	o["Font"]				= "Font";
	o["Size"]				= "Boyut";
	o["Source"]				= "Kaynak";

	// Titles
	o["SourceTitle"]		= "Editör ile kaynak modlarý arasýnda geçiþ için týklayýnýz.";

	// Image Sources
	o["icon_post"]			= "images/lang/tr.icon_post.gif";
	o["hdr_tables"]			= "images/lang/tr.hdr_tables.gif";
	
////////////////////////////////////////////////////////////////////////////////
//
// Italiano: delmazza@a99.it - Angelo Del Mazza - Area99 http://www.a99.it 
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["it"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Invia Articolo";
	o["Cut"]				= "Taglia";
	o["Copy"]				= "Copia";
	o["Paste"]				= "Incolla";
	o["SpellCheck"]			= "Controlla Ortografia";
	o["SelectAll"]			= "Seleziona Tutto";
	o["RemoveFormatting"]	= "Rimuovi Formattazione";
	o["InsertLink"]			= "Insrisci Link";
	o["RemoveLink"]			= "Rimuovi Link";
	o["InsertImage"]		= "Inserisci Immagine";
	o["InsertTable"]		= "Inserisci Tabella";
	o["EditTable"]			= "Modifica Tabella";
	o["InsertLine"]			= "Inserisci Linea Orizzontale";
	o["InsertSmily"]		= "Inserisci Smily 8-)";
	o["InsertCharacter"]	= "Inserisci Carattere Speciale";
	o["About"]				= "Info su Richtext Editor";
	o["Bold"]				= "Grassetto";
	o["Italic"]				= "Italico";
	o["Underline"]			= "Sottolineato";
	o["Strikethrough"]		= "Barrato";
	o["AlignLeft"]			= "Allinea a Sinistra";
	o["Center"]				= "Centrato";
	o["AlignRight"]			= "Alline a Destra";
	o["AlignBlock"]			= "Allinea Blocco";
	o["NumberedList"]		= "Elenco Numerato";
	o["BulettedList"]		= "Elenco Puntato";
	o["DecreaseIndent"]		= "Diminuisci Rientro";
	o["IncreaseIndent"]		= "Incrementa Rientro";
	o["HistoryBack"]		= "Indietro";
	o["HistoryForward"]		= "Avanti";
	o["TextColor"]			= "Colore Testo";
	o["BackgroundColor"]	= "Colore Sfondo";

	o["RemoveColspan"]		= "Rimuovi Colspan";
	o["RemoveRowspan"]		= "Rimuovi Rowspan";
	o["IncreaseColspan"]	= "Incrementa Colspan";
	o["IncreaseRowspan"]	= "Incrementa Rowspan";
	o["AddColumn"]			= "Aggiungi Colonna";
	o["AddRow"]				= "Aggiungi Riga";
	o["RemoveColumn"]		= "Rimuovi Colonna";
	o["RemoveRow"]			= "Rimuovi Riga";

	// Label Text
	o["Style"]				= "Stile";
	o["Font"]				= "Carattere";
	o["Size"]				= "Dimensione";
	o["Source"]				= "Sorgente";

	// Titles
	o["SourceTitle"]		= "Clicca per passare in modalità WYSIWYG o Sorgente";

	// Image Sources
	o["icon_post"]			= "images/lang/it.icon_post.gif";
	o["hdr_tables"]			= "images/hdr_tables.gif";

////////////////////////////////////////////////////////////////////////////////
// 
// Dutch Translation - Courtesy of FlevOOware, Marja Ribbers-de Vroed
// <marja@flevooware.nl>
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["nl"] = new Object;

 // Icon Titles (alt="")
 o["PostTopic"]                  = "Opslaan";
 o["Cut"]                        = "Knippen";
 o["Copy"]                       = "Kopieren";
 o["Paste"]                      = "Plakken";
 o["SpellCheck"]                 = "Controleer spelling";
 o["SelectAll"]                  = "Selecteer alles";
 o["RemoveFormatting"]           = "Opmaak verwijderen";
 o["InsertLink"]                 = "Link invoegen";
 o["RemoveLink"]                 = "Link verwijderen";
 o["InsertImage"]                = "Afbeelding invoegen";
 o["InsertTable"]                = "Tabel invoegen";
 o["EditTable"]                  = "Tabel wijzigen";
 o["InsertLine"]                 = "Horizontale lijn invoegen";
 o["InsertSmily"]                = "Smiley 8-) invoegen";
 o["InsertCharacter"]            = "Karakter invoegen";
 o["About"]                      = "Over Richtext Editor";
 o["Bold"]                       = "Vet";
 o["Italic"]                     = "Cursief";
 o["Underline"]                  = "Onderstreept";
 o["Strikethrough"]              = "Doorgehaald";
 o["AlignLeft"]                  = "Links uitlijnen";
 o["Center"]                     = "Centreren";
 o["AlignRight"]                 = "Rechts uitlijnen";
 o["AlignBlock"]                 = "Uitlijnen als blok";
 o["NumberedList"]               = "Lijst met nummering";
 o["BulettedList"]               = "Lijst met opsommingstekens";
 o["DecreaseIndent"]             = "Inspringing verkleinen";
 o["IncreaseIndent"]             = "Inspringing vergroten";
 o["HistoryBack"]                = "Herstel";
 o["HistoryForward"]             = "Opnieuw";
 o["TextColor"]                  = "Tekstkleur";
 o["BackgroundColor"]            = "Achtergrondkleur";

 o["RemoveColspan"]       = "Verwijder Colspan";
 o["RemoveRowspan"]       = "Verwijder Rowspan";
 o["IncreaseColspan"]    = "Verhoog Colspan";
 o["IncreaseRowspan"]    = "Verlaag Rowspan";
 o["AddColumn"]    = "Voeg kolom toe";
 o["AddRow"]     = "Voeg rij toe";
 o["RemoveColumn"]    = "Verwijder kolom";
 o["RemoveRow"]    = "Verwijder rij";

 // Label Text
 o["Style"]                      = "Stijl";
 o["Font"]                       = "Lettertype";
 o["Size"]                       = "Grootte";
 o["Source"]                     = "HTML-brontekst";

 // Titles
 o["SourceTitle"]                = "Klik hier om te wisselen tussen WYSIWYG-en HTML-brontekst-modus.";

 // Image Sources
 o["icon_post"]   = "images/icon_post.gif";
 o["hdr_tables"]   = "images/hdr_tables.gif";

////////////////////////////////////////////////////////////////////////////////
//
// Portuguese (Brazilian) 
// Courtesy of Ricardo Colombani de Sousa <colombani@ig.com.br>
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["pt-br"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Enviar";
	o["Cut"]				= "Recortar";
	o["Copy"]				= "Copiar";
	o["Paste"]				= "Colar";
	o["SpellCheck"]			= "Corretor Ortográfico";
	o["SelectAll"]			= "Selecionar Tudo";
	o["RemoveFormatting"]	= "Remover Formatação";
	o["InsertLink"]			= "Inserir Link";
	o["RemoveLink"]			= "Remover Link";
	o["InsertImage"]		= "Inserir Imagem";
	o["InsertTable"]		= "Inserir Tabela";
	o["EditTable"]			= "Editar Tabela";
	o["InsertLine"]			= "Inserir Linha Horizontal";
	o["InsertSmily"]		= "Inserir Emoticon 8-)";
	o["InsertCharacter"]	= "Inserir caráter Especial";
	o["About"]				= "Sobre o Richtext Editor";
	o["Bold"]				= "Negrito";
	o["Italic"]				= "Itálico";
	o["Underline"]			= "Sublinhado";
	o["Strikethrough"]		= "Riscado";
	o["AlignLeft"]			= "Alinhar à Esquerda";
	o["Center"] 			= "Centralizar";
	o["AlignRight"]			= "Alinhar à Direita";
	o["AlignBlock"]			= "Justificar";
	o["NumberedList"]		= "Lista Numerada";
	o["BulettedList"]		= "Lista com Marcadores";
	o["DecreaseIndent"]		= "Diminuir Identação";
	o["IncreaseIndent"]		= "Aumentar Identação";
	o["HistoryBack"]		= "Desfazer";
	o["HistoryForward"]		= "Refazer";
	o["TextColor"]			= "Cor de Texto";
	o["BackgroundColor"]	= "Cor de Fundo";

	o["RemoveColspan"]		= "Remover Colspan";
	o["RemoveRowspan"]		= "Remover Rowspan";
	o["IncreaseColspan"]	= "Aumentar Colspan";
	o["IncreaseRowspan"]	= "Aumentar Rowspan";
	o["AddColumn"]			= "Adicionar Coluna";
	o["AddRow"]				= "Adicionar Linha";
	o["RemoveColumn"]		= "Remover Coluna";
	o["RemoveRow"]			= "Remover Linha";

	// Label Text
	o["Style"]				= "Estilo";
	o["Font"]				= "Fonte";
	o["Size"]				= "Tamanho";
	o["Source"]				= "Código fonte";

	// Titles
	o["SourceTitle"]		= "Clique aqui para alternar entre modo de edição e modo código fonte.";

	// Image Sources
	o["icon_post"]			= "images/lang/br.icon_post.gif";
	o["hdr_tables"]			= "images/lang/br.hdr_tables.gif";
	o["icon_bold"]			= "images/lang/br.icon_bold.gif";
	o["icon_italic"]		= "images/icon_italic.gif";
	o["icon_underline"]		= "images/lang/br.icon_underline.gif";
	
////////////////////////////////////////////////////////////////////////////////
//
// Danish - translated by Morten Flyger (flyger@email.dk)
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["da"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Gem";
	o["Cut"]			= "Klip";
	o["Copy"]			= "Kopiere";
	o["Paste"]			= "Indsæt";
	o["SpellCheck"]			= "Stavekontrol";
	o["SelectAll"]			= "Markere alt";
	o["RemoveFormatting"]		= "Fjern formatering";
	o["InsertLink"]			= "Indsæt link";
	o["RemoveLink"]			= "Fjern link";
	o["InsertImage"]		= "Indsæt billede";
	o["InsertTable"]		= "Indsæt tabel";
	o["EditTable"]			= "Ændre tabel";
	o["InsertLine"]			= "Indsæt horisontal linje";
	o["InsertSmily"]		= "Indsæt smily 8-)";
	o["InsertCharacter"]		= "Indsæt specialtegn";
	o["About"]			= "Om Richtext Editor";
	o["Bold"]			= "Fed";
	o["Italic"]			= "Kursiv";
	o["Underline"]			= "Understregning";
	o["Strikethrough"]		= "Gennemstregning";
	o["AlignLeft"]			= "Venstrejustering";
	o["Center"]			= "Centrering";
	o["AlignRight"]			= "Højrejustering";
	o["AlignBlock"]			= "Blokjustering";
	o["NumberedList"]		= "Nummereret liste";
	o["BulettedList"]		= "Punktopstilling";
	o["DecreaseIndent"]		= "Mindske indrykningsværdi";
	o["IncreaseIndent"]		= "Øge indrykningsværdi";
	o["HistoryBack"]		= "Historie tilbage";
	o["HistoryForward"]		= "Historie frem";
	o["TextColor"]			= "Tekstfarve";
	o["BackgroundColor"]		= "Baggrundsfarve";

	// Label Text
	o["Style"]			= "Stil";
	o["Font"]			= "Type";
	o["Size"]			= "Størrelse";
	o["Source"]			= "Kilde";

	// Titles
	o["SourceTitle"]		= "Klik her for at skifte imellem WYSIWYG og kilde fremtrædelsesform.";

	// Image Sources
	o["icon_post"]			= "images/lang/no.icon_post.gif";
	o["icon_bold"]			= "images/lang/da_icon_bold.gif";
	o["icon_italic"]		= "images/lang/da_icon_italic.gif";
	o["icon_underline"]		= "images/lang/da_icon_underline.gif";


////////////////////////////////////////////////////////////////////////////////
//
// Español (es-mx)
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["es-mx"] = new Object;


	// Icon Titles (alt="")
	o["PostTopic"]			= "Colcar";
	o["Cut"]				= "Cortar";
	o["Copy"]				= "Copiar";
	o["Paste"]				= "Pegar";
	o["SpellCheck"]			= "Checar orografía";
	o["SelectAll"]			= "Seleccionar todo";
	o["RemoveFormatting"]	= "Quitar formato";
	o["InsertLink"]			= "Insertar liga";
	o["RemoveLink"]			= "Quitar liga";
	o["InsertImage"]		= "Insertar imagen";
	o["InsertTable"]		= "Insertar tabla";
	o["EditTable"]			= "Editar tabla";
	o["InsertLine"]			= "Insertar línea horizontal";
	o["InsertSmily"]		= "Insertar carita 8-)";
	o["InsertCharacter"]	= "Insertar caracter especial";
	o["About"]				= "Sobre el editor";
	o["Bold"]				= "Negrita";
	o["Italic"]				= "Cursiva";
	o["Underline"]			= "Subrayado";
	o["Strikethrough"]		= "Tachado";
	o["AlignLeft"]			= "Alinear a la izquierda";
	o["Center"]				= "Centrar";
	o["AlignRight"]			= "Alinear a la derecha";
	o["AlignBlock"]			= "Alinear justificado";
	o["NumberedList"]		= "Lista numerada";
	o["BulettedList"]		= "Lista no numerada";
	o["DecreaseIndent"]		= "Sangría decreciente";
	o["IncreaseIndent"]		= "Sangría creciente";
	o["HistoryBack"]		= "Deshacer";
	o["HistoryForward"]		= "Rehacer";
	o["TextColor"]			= "Color de texto";
	o["BackgroundColor"]	= "Color de fondo";

	o["RemoveColspan"]		= "Separar columnas";
	o["RemoveRowspan"]		= "Separar filas";
	o["IncreaseColspan"]	= "Juntar columnas";
	o["IncreaseRowspan"]	= "Juntar filas";
	o["AddColumn"]			= "Agregar columnas";
	o["AddRow"]				= "Agregar fila";
	o["RemoveColumn"]		= "Quitar columna";
	o["RemoveRow"]			= "Quitar fila";

	// Label Text
	o["Style"]				= "Est.";
	o["Font"]				= "Fte.";
	o["Size"]				= "Tam.";
	o["Source"]				= "Cód.";

	// Titles
	o["SourceTitle"]		= "Cambiar entre editor visual y de código fuente.";

	// Image Sources
	o["icon_post"]			= "images/icon_post.gif";
	o["hdr_tables"]			= "images/hdr_tables.gif";

	
////////////////////////////////////////////////////////////////////////////////
//
// Español Alfabetización Internacional (es-es) Traducción de Emmanuelle 																//Gutiérrez (emmanuelle@sidar.org)
//
////////////////////////////////////////////////////////////////////////////////

var o = locale["es"] = locale["es-es"] = new Object;

	// Icon Titles (alt="")
	o["PostTopic"]			= "Responder";
	o["Cut"]				= "Cortar";
	o["Copy"]				= "Copiar";
	o["Paste"]				= "Pegar";
	o["SpellCheck"]			= "Revisión ortográfica";
	o["SelectAll"]			= "Seleccionar todo";
	o["RemoveFormatting"]	= "Eliminar formato";
	o["InsertLink"]			= "Insertar enlace";
	o["RemoveLink"]			= "Eliminar enlace";
	o["InsertImage"]		= "Insertar Imagen";
	o["InsertTable"]		= "Insertar Tabla";
	o["EditTable"]			= "Editar Tabla";
	o["InsertLine"]			= "Insertar línea horizontal";
	o["InsertSmily"]		= "Insertar Emoticon 8-)";
	o["InsertCharacter"]	= "Insertar carácter especial";
	o["About"]				= "Acerca del Editor de texto enriquecido";
	o["Bold"]				= "Negrita";
	o["Italic"]				= "Itálica";
	o["Underline"]			= "Subrayado";
	o["Strikethrough"]		= "Tachado";
	o["AlignLeft"]			= "Alinear a la izquierda";
	o["Center"]				= "Centrado";
	o["AlignRight"]			= "Alinear a la derecha";
	o["AlignBlock"]			= "Alinear bloque";
	o["NumberedList"]		= "Lista numerada";
	o["BulettedList"]		= "Lista con viñetas";
	o["DecreaseIndent"]		= "Disminuir sangrado";
	o["IncreaseIndent"]		= "Incrementar sangrado";
	o["HistoryBack"]		= "Deshacer";
	o["HistoryForward"]		= "Rehacer";
	o["TextColor"]			= "Color del Texto";
	o["BackgroundColor"]	= "Color del Fondo";

	o["RemoveColspan"]		= "Separar Columnas";
	o["RemoveRowspan"]		= "Separar Filas";
	o["IncreaseColspan"]	= "Juntar Columnas";
	o["IncreaseRowspan"]	= "Juntar Filas";
	o["AddColumn"]			= "Añadir Columna";
	o["AddRow"]				= "Añadir Fila";
	o["RemoveColumn"]		= "Eliminar Columna";
	o["RemoveRow"]			= "Eliminar Fila";

	// Label Text
	o["Style"]				= "Estilo";
	o["Font"]				= "Fuente";
	o["Size"]				= "Tamaño";
	o["Source"]				= "Código";

	// Titles
	o["SourceTitle"]		= "Cambiar entre vista edición y código.";

	// Image Sources
	o["icon_post"]			= "images/icon_post.gif";
	o["hdr_tables"]			= "images/hdr_tables.gif";

////////////////////////////////////////////////////////////////////////////

