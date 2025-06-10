# Prezentacja aplikacji GlobaLogix
## System zarządzania zleceniami transportowymi

---

## Slajd 1: Tytuł
**GlobaLogix - System zarządzania zleceniami transportowymi**

Kompleksowa platforma do zarządzania procesami logistycznymi z implementacją wzorca CQRS i Event Sourcing

---

## Slajd 2: Agenda prezentacji
1. **Przegląd systemu** - podstawowe funkcjonalności
2. **Architektura aplikacji** - wzorce i technologie
3. **Role użytkowników** - uprawnienia i funkcje
4. **Workflow procesów** - przepływ zleceń
5. **Funkcjonalności kluczowe** - szczegółowy opis
6. **Technologie wykorzystane** - stack technologiczny
7. **Demo systemu** - prezentacja live

---

## Slajd 3: Przegląd systemu
### Cel aplikacji
- **Zarządzanie zleceniami transportowymi** od momentu utworzenia do dostawy
- **Automatyzacja procesów** logistycznych
- **Śledzenie w czasie rzeczywistym** lokalizacji i statusów
- **Generowanie dokumentacji** transportowej

### Kluczowe korzyści
- ✅ Centralizacja zarządzania zleceniami
- ✅ Transparentność procesów
- ✅ Automatyzacja workflow
- ✅ Monitoring w czasie rzeczywistym

---

## Slajd 4: Architektura systemu
### Wzorce architektoniczne
- **CQRS (Command Query Responsibility Segregation)**
  - Rozdzielenie komend od zapytań
  - CommandBus i QueryBus
  - Dedykowane handlery

- **Event Sourcing**
  - Przechowywanie wydarzeń zamiast stanów
  - Możliwość odtworzenia historii
  - Projekcje read-model

### Technologie
- **Backend:** PHP 8+ z PDO
- **Frontend:** Vanilla JavaScript + Bootstrap
- **Baza danych:** MySQL
- **Architektura:** Event-driven

---

## Slajd 5: Role użytkowników
### 👥 Klient
- Składanie nowych zleceń transportowych
- Akceptacja ofert spedytorskich
- Śledzenie statusu zleceń
- Przeglądanie dokumentów

### 🚛 Spedytor
- Zatwierdzanie zleceń klientów
- Przypisywanie kierowców
- Planowanie tras
- Monitoring całości procesów

### 🚗 Kierowca
- Panel zarządzania dostawami
- Aktualizacja lokalizacji
- Zmiana statusów dostaw
- Generowanie dokumentów potwierdzających

---

## Slajd 6: Workflow procesów biznesowych
```
Klient → [Utworzenie zlecenia] → Zlecenie utworzone
   ↓
Spedytor → [Zatwierdzenie] → Zatwierdzone przez spedytora
   ↓  
Klient → [Akceptacja oferty] → Zaakceptowane przez klienta
   ↓
Spedytor → [Przypisanie kierowcy] → Kierowca przypisany
   ↓
Kierowca → [Rozpoczęcie dostawy] → W trakcie
   ↓
Kierowca → [Zakończenie dostawy] → Dostarczone
```

---

## Slajd 7: Funkcjonalności kluczowe - Zarządzanie zleceniami
### Tworzenie zleceń
- Formularz z autocomplete dla firm
- Walidacja danych wejściowych
- Automatyczne przypisywanie ID

### Zatwierdzanie przez spedytora
- Lista oczekujących zleceń
- Możliwość odrzucenia z komentarzem
- Przypisywanie kierowców z dropdown

### Akceptacja przez klienta
- Przegląd szczegółów oferty
- Możliwość akceptacji lub odrzucenia
- Powiadomienia o zmianach statusu

---

## Slajd 8: Funkcjonalności kluczowe - Panel kierowcy
### Zarządzanie dostawami
- Lista aktywnych zleceń
- Statystyki personalne
- Wybór aktywnego zlecenia

### Akcje w zależności od statusu
- **Gotowe do dostawy:** Rozpocznij dostawę, Planuj trasę
- **W trakcie:** Zakończ dostawę, Aktualizuj status
- **Pobrano ładunek:** Zakończ dostawę
- **Dostarczone:** Generuj dokumenty

### Śledzenie lokalizacji
- Aktualizacja współrzędnych GPS
- Śledzenie w czasie rzeczywistym
- Historia przemieszczenia

---

## Slajd 9: System CQRS - Komendy
### Główne komendy systemu
- `SubmitOrderCommand` - złożenie zlecenia
- `ValidateOrderCommand` - zatwierdzenie przez spedytora
- `AssignDriverCommand` - przypisanie kierowcy
- `StartDeliveryCommand` - rozpoczęcie dostawy
- `CompleteDeliveryCommand` - zakończenie dostawy
- `UpdateLocationCommand` - aktualizacja lokalizacji
- `GenerateDocumentCommand` - generowanie dokumentów

### Command Handlers
Każda komenda ma dedykowany handler odpowiedzialny za logikę biznesową i generowanie eventów.

---

## Slajd 10: System CQRS - Zapytania
### Główne zapytania systemu
- `GetOrdersQuery` - pobieranie listy zleceń
- `GetDriversQuery` - lista kierowców
- `GetTrackingInfoQuery` - informacje o śledzeniu
- `GetDocumentsQuery` - dokumenty zlecenia
- `GetOrderHistoryQuery` - historia zlecenia

### Query Handlers
Dedykowane handlery do obsługi zapytań z optymalizacją pod odczyt danych.

---

## Slajd 11: Event Sourcing - Wydarzenia
### Kluczowe wydarzenia
- `OrderSubmitted` - zlecenie złożone
- `OrderValidated` - zlecenie zatwierdzone
- `DriverAssigned` - kierowca przypisany
- `OrderAccepted` - oferta zaakceptowana
- `DeliveryStarted` - dostawa rozpoczęta
- `LocationUpdated` - lokalizacja zaktualizowana
- `DeliveryCompleted` - dostawa zakończona
- `DocumentGenerated` - dokument wygenerowany

### Projekcje
Wydarzenia są automatycznie projektowane na read-model w tabeli `order_list`.

---

## Slajd 12: Funkcjonalności dodatkowe
### Planowanie tras
- Algorytmy optymalizacji trasy
- Szacowanie czasu i kosztów
- Integracja z mapami (przygotowana)

### Generowanie dokumentów
- Faktury transportowe
- Listy przewozowe
- Potwierdzenia odbioru
- Format PDF

### System śledzenia
- Śledzenie w czasie rzeczywistym
- Historia lokalizacji
- Powiadomienia o zmianach

---

## Slajd 13: Interfejs użytkownika
### Responsive Design
- Bootstrap 5 dla responsywności
- FontAwesome dla ikon
- Material Design Bootstrap (MDB)

### Interaktywność
- Dynamiczne ładowanie treści
- Modalne okna
- AJAX dla wszystkich operacji
- Walidacja w czasie rzeczywistym

### UX/UI Features
- Autocomplete dla firm
- Kolorowe statusy zleceń
- Progresywne UI w panelu kierowcy
- Powiadomienia i alerty

---

## Slajd 14: Bezpieczeństwo i niezawodność
### Walidacja danych
- Walidacja po stronie klienta i serwera
- Prepared statements w bazach danych
- Sanityzacja wejść użytkownika

### Obsługa błędów
- Try-catch w JavaScript
- Graceful error handling
- User-friendly komunikaty błędów

### Transakcje
- Atomowe operacje w bazie danych
- Rollback w przypadku błędów
- Consistency przez CQRS

---

## Slajd 15: Możliwości rozwoju
### Planowane rozszerzenia
- **Integracja z systemami zewnętrznymi**
  - Google Maps API
  - Systemy płatności
  - SMS/Email notifications

- **Zaawansowane funkcje**
  - Machine learning dla optymalizacji tras
  - Predykcyjne analizy
  - Mobile app dla kierowców

- **Skalowanie**
  - Mikroserwisy
  - Message queues
  - Load balancing

---

## Slajd 16: Demo systemu
### Scenariusz demonstracji
1. **Logowanie jako Klient** - utworzenie nowego zlecenia
2. **Przełączenie na Spedytora** - zatwierdzenie i przypisanie kierowcy
3. **Powrót do Klienta** - akceptacja oferty
4. **Panel Kierowcy** - zarządzanie dostawą
5. **Śledzenie zlecenia** - monitoring statusu
6. **Dokumenty** - generowanie potwierdzenia

### Kluczowe punkty do pokazania
- Płynność interfejsu
- Dynamiczne zmiany statusów
- Panel kierowcy w akcji
- System powiadomień

---

## Slajd 17: Pytania i odpowiedzi
### Najczęstsze pytania:

**Q: Czy system obsługuje wielu kierowców jednocześnie?**
A: Tak, system został zaprojektowany do obsługi wielu użytkowników równocześnie.

**Q: Jak działa śledzenie w czasie rzeczywistym?**
A: Kierowcy mogą aktualizować swoją lokalizację, która jest zapisywana w bazie i dostępna dla wszystkich zainteresowanych stron.

**Q: Czy można zintegrować system z zewnętrznymi API?**
A: Tak, architektura pozwala na łatwą integrację z systemami typu Google Maps, płatności, etc.

---

## Slajd 18: Podsumowanie
### Kluczowe cechy GlobaLogix:
- ✅ **Nowoczesna architektura** - CQRS + Event Sourcing
- ✅ **Kompletny workflow** - od zlecenia do dostawy
- ✅ **Trzy role użytkowników** - Klient, Spedytor, Kierowca
- ✅ **Śledzenie w czasie rzeczywistym**
- ✅ **Automatyzacja procesów**
- ✅ **Responsywny interfejs**
- ✅ **Gotowość do skalowania**

### Dziękuję za uwagę!
**Demonstracja systemu w akcji** 🚛📱

---
