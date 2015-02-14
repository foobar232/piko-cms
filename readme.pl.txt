PikoCMS v0.9.7
Autor: Mendax - http://www.fabrykaspamu.pl
Licencja: CC-BY-SA - http://creativecommons.org/licenses/by-sa/3.0/


1. Co to jest?

PikoCMS to "jednoplikowy" Content Management System do tworzenia pseudo-statycznych mikrostronek.
Nazwa wzięła się stąd, iż kod główny zawiera się w niecałych 12kB, a przedrostek SI dla 10^(-12) to "piko".

Podstawowe cechy:
-szybkość instalacji,
-szybkość działania,
-automatyczne tworzenie pliku "robots.txt",
-automatyczne tworzenie pliku sitemap dla wyszukiwarek,
-automatyczne tworzenie kanału RSS z podstronami,
-przyjazne adresy,
-usunięcie efektu "duplicate content" oraz zlikwidowanie błędów 404.


2. Licencja i autorstwo

Autorem tego CMS-a jest człowiek używający pseudonimu "Mendax", autor bloga "Fabryka Spamu" {http://www.fabrykaspamu.pl}

Skrypt jest na licencji "Creative Commons Uznanie Autorstwa - Na Tych Samych Warunkach" (CC-BY-SA) {http://creativecommons.org/licenses/by-sa/3.0/}, co oznacza, że możesz go dowolnie przerabiać, wykorzystywać i rozpowszechniać, pod warunkiem pozostawienia informacji o autorze oraz przy zachowaniu tej samej licencji.

Przy czym jeśli wykorzystujesz ten CMS do celów komercyjnych, autorowi byłoby miło, gdybyś umieścił klikalnego linka do jego bloga. :)


3. Wymagania.

Podstawowym wymaganiem do działaniu skryptu jest serwer WWW z obsługą PHP 4.x lub nowszą. Nie jest wymagana baza danych.
Skrypt został przetestowany na kilku konfiguracjach sprzętowo-programowych, ale autor nie gwarantuje, że u Ciebie również będzie wszystko w porządku.


4a. Instalacja - wersja krótka

1. Zmień hasło w plliku "index.php" - zmienna $admpass
2. Wgraj pliki "index.php", "data.php", ".htaccess" i pliki szablonu graficznego na serwer.
3. Zmień CHMOD plików "data.php" i ".htaccess" na 666.
4. Wpisz w przeglądarce adres, pod jakim ma powstać strona.
5. Zaloguj się z wykorzystaniem hasła.
6. Dodaj artykuły na stronę.
7. Gotowe.


4b. Instalacja i konfiguracja - wersja dłuższa

Aby skrypt działał poprawnie potrzebny jest szablon graficzny. W paczce, którą ściągnąłeś powinien być jeden przykładowy. Jeśli chcesz zastosować swój szablon - przejdź do punktu 5.

Przed wgraniem na serwer otwórz plik "index.php" i zmień koniecznie zawartość zmiennej "admpass" - jest to hasło administracyjne.
Możesz tu również zmienić wskazanie na plik szablonu graficznego oraz końcówkę tworzonych podstron.
Ponadto możesz zdefiniować adres kanału RSS z linkami do wszystkich podstron - odniesienie do tego adresu pojawi się automatycznie w sekcji "head" szablonu. Jeśli nie chcesz tworzenia takiego kanału, wykasuj wartość zmiennej "rssfile".

Wgraj na serwer pliki "index.php", "data.php", ".htaccess" oraz szablon graficzny. Plikom "data.php" i ".htaccess" nadaj CHMOD 666.
Po wgraniu na serwer wpisz w przeglądarce adres, pod którym strona ma być widoczna. Nastąpi próba automatycznego wypełnienia odpowiednią treścią pliku ".htaccess" oraz danymi. Jeśli wszystko pójdzie zgodnie z planem, zostaniesz przekierowany do formularza logowania. Formularz ten jest również dostępny przez dopisanie do adresu: "?adm".
Po wpisaniu hasła zobaczysz częściowo wypełniony treścią formularz edycji/dodawania artykułów.

Opisy poszczególnych pól:
Sidebar - stały tekst na wszystkich podstronach domyślnie w pasku bocznym, może pozostać puste,
Footer - stały tekst na wszystkich podstronach domyślnie w sotpce, może pozostać puste,
MetaTitle X - tytuł <title>, umieszczony w sekcji <head> strony, wymagany,
URL X - opcjonalne wskazanie, jak ma wyglądać adres podstrony, jeśli pusty-zostanie automatycznie utworzony z tytułu,
MetaDescription X - opcjonalny opis strony, umieszczony w sekcji <head> strony,
ContentH1 X - tekst do umieszczenia w tagu <h1>, opcjonalnie,
ContentH2 X - tekst do umieszczenia w tagu <h2>, opcjonalnie,
MainContent - główna część artykułu.

Ze względów techniczno-oszczędnościowych możesz na raz dodać tylko jeden nowy artykuł (oraz edytować wszystkie istniejące).
Po skończeniu dodawania artykułów dla bezpieczeństwa zamień CHMOD-y plików ".htaccess" i "data.php na 644.
Aby usunąć artykuł, wyczyść pole MetaTitle. Uwaga: pole "MetaTitle 1" zawsze musi pozostać wypełnione!

Istnieje możliwość wgrywania własnych plików (np. obrazków) na serwer. Katalog, gdzie pliki będą ładowane określa zmienna $upfolde w pliku index.php. Na większości serwerów katalog o tej nazwie powinien mieć ustawione chmod-y na 777.


5. Dostosowanie własnego szablonu

Można zastosować swój szablon, wówczas należy umieścić w nim następujące tagi (ważna wielkość znaków):

{TITLE} - należy umieścić w sekcji "head" strony między tagami <title> a </title>,
{DESC} - (opcjonalny) w sekcji head do wstawienia w meta - description,
{H1} - (opcjonalny) domyślnie do wstawienia między tagi <h1> a </h1>,
{H2} - (opcjonalny) domyślnie do wstawienia między tagi <h2> a </h2>,
{CONTENT} - w tym miejscu zostanie umieszczona główna treść artykułu,
{SIDEBAR} - (opcjonalny) stała treść w obrębie wszystkich podstron, domyślnie w pasku bocznym,
{FOOTER} - (opcjonalny) stała treść w obrębie wszystkich podstron, domyślnie w stopce,
{LINKS} - umieszcza linki do wszystkich stworzonych podstron w formie listy <li><a href="...">..."</a></li>.

W pliku szablonu można umieszczać kod PHP (np. SWL-e, itp.) Aby kod ten się wykonał należy zmienić wartość zmiennej $phpintpl w pliku index.php na "true".


6. Ograniczenia

PikoCMS jest przeznaczony do szybkiego tworzenia mikrostronek przez ludzi, którzy posiadają conamniej podstawową wiedzę o HTML i PHP, z tej racji nie ma właściwie zabezpieczeń przed wpisywaniem niepoprawnych danych w sekcji administracyjnej.
W polach FOOTER, SIDEBAR oraz CONTENT x odradza się wpisywanie ciągu </textarea>.
W pozostałych polach odradza się stosowania większości znaczników html oraz znaków " i '.

Upload plików na serwer nie jest objęty żadnym sprawdzaniem pod kątem bezpieczeństwa. W szczególności: nie ma sprawdzania typu pliku, czy wielkość pliku nie jest większa niż dozwolona, czy istniał wcześniej plik o danej nazwie, czy docelowy katalog ma odpowiednie chmod-y.

Całość działa na standardzie kodowania utf-8.

Bez modyfikacji można zapisać do 10 podstron. Jeśli chcesz więcej - zmień odpowiednią liczbę w linijce 95 pliku index.php

Skrypt w standardzie tworzy link do pingowania Technorati, Feedburner oraz Google Blog Search. Jeśli chcesz więcej - dodaj odpowiednie opcje w 110 linijce pliku index.php


7. Historia

0.9.7	15.05.2009
	-nie można było wyłączyć tworzenia kanału RSS - zostało to poprawione.
	-poprawiono błąd występujący, kiedy w sekcje footer lub sidebar wpisano cudzysłów.

0.9.6	06.05.2009
	-poprawiono htaccess: nie następuje teraz przekierowanie z subdomen na domenę główną.

0.9.5	22.04.2009
	-dodano możliwość uploadu własnych plików na serwer.
	-na stronie administracyjnej po dodaniu artykułu pojawia się link umożliwiający pingowanie (pingomatic.com).
	-w pliku templatki można teraz umieszczać kod PHP.

0.9.1	26.03.2009
	-na stronie administracyjnej dodano link do strony głównej oraz opcję wylogowania.
	
0.9		24.03.2009
	-pierwsza publiczna wersja.