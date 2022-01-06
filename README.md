# avatardatabase

Dieser Plugin ermöglicht es eine eigene Avatardatenbank zu führen. Hierzu können die Informationen *Geschlecht, Jahrgang, Haarfarbe und Herkunft* angegeben werden. Zudem haben die User die Möglichkeit einen Link zu einer  Galerie anzugeben. <br />
Bearbeiten kann es nur das Team! Schon vergebene Avatare werden durchgestrichen!

## muss vorhanden sein
fontawesome

## Link
forenlink/misc.php?action=avatardatabase

## Templates
- avatardatabase 	
- avatardatabase_edit 	
- avatardatabase_formular 	
- avatardatabase_suggestions 	
- avatardatabase_table 	
- avatardatabase_table_divers

## optionen
Wichtig! Passe zunächst die Einstellungen ein, sonst kann der die Vergebenen Avatare nicht durchstreichen.

## Einstellungen
### Konfiguration
![picture alt](https://up.picr.de/42776433ss.png "Einstellungen")
<br />
Um auch zu Gewährleisten, dass die Avatarpersonen, die bei euch schon vergeben sind, auch durchgestrichen sind, müsst ihr im ACP sowohl die richtige FID einfügne, als auch die Art, wie die Avatarpersonen im Profilfeld stehen. Sonst kann er nicht korrekt abgleichen.
<br /><br />
### Gruppeneinstellungen
![picture alt](https://up.picr.de/42776444ae.png "Gruppeneinstellungen")
<br />
Ihr müsst in den Gruppeneinstellungen bei allen Gruppen deaktivieren, die **keine** Avatarpersonen in die Datenbank eintragen dürfen!
<br />
## Bild anstatt Link
Aktuell wird nur ein Link zu einer Galerie angezeigt. Möchtet ihr aber stattdessen ein Bild nutzen, dann müsst ihr in der Datei folgendes abändern:<br />
```PHP          
if(!empty($row['link'])){
                $link = "<a href='{$row['link']}' target='_blank' title='Link zur Galerie'><i class=\"fas fa-location-arrow\"></i></a>";
            }
```
<br />
Anstatt den Pfeil könnt ihr den img-tag nutzen. In meinem Beispiel könnt ihr eine feste Breite angeben (00 ersetzen) und die Höhe berechnet er automatisch. Wer besser bewanderter ist kann es auch zu einen Pop-up machen. (bitte leerzeichen entfernen nach dem < und vor dem >)<br />

< img src='{$row['link']}' style="width: 00px; height: auto;" >
