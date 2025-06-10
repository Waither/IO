// Test funkcjonalności panelu kierowcy
async function testDriverPanel() {
    console.log('=== TESTOWANIE PANELU KIEROWCY ===');
    
    // 1. Sprawdź czy funkcje są dostępne
    console.log('1. Sprawdzanie dostępności funkcji...');
    console.log('updateDriverPanelData:', typeof window.updateDriverPanelData);
    console.log('setupDeliveryActions:', typeof window.setupDeliveryActions);
    console.log('handleStartDelivery:', typeof window.handleStartDelivery);
    
    // 2. Sprawdź czy jesteś zalogowany jako kierowca
    console.log('2. Sprawdzanie użytkownika...');
    const userCookie = document.cookie.split(';').find(c => c.trim().startsWith('user_spedycja='));
    console.log('User cookie:', userCookie);
    
    // 3. Sprawdź czy panel kierowcy istnieje w DOM
    console.log('3. Sprawdzanie elementów DOM...');
    const driverPanel = document.getElementById('driverPanel');
    const deliveryActions = document.getElementById('deliveryActions');
    console.log('Driver panel element:', driverPanel);
    console.log('Delivery actions element:', deliveryActions);
    
    // 4. Test ładowania danych panelu kierowcy (jeśli funkcja istnieje)
    if (typeof window.updateDriverPanelData === 'function') {
        console.log('4. Testowanie ładowania danych panelu kierowcy...');
        try {
            await window.updateDriverPanelData();
            console.log('✅ Panel kierowcy załadowany pomyślnie');
        } catch (error) {
            console.error('❌ Błąd podczas ładowania panelu kierowcy:', error);
        }
    } else {
        console.log('4. ❌ Funkcja updateDriverPanelData nie jest dostępna');
        console.log('   Prawdopodobnie musisz przejść do panelu kierowcy');
    }
    
    // 2. Test funkcji mapowania statusów
    console.log('2. Testowanie mapowania statusów...');
    const testStatuses = [
        'Utworzone',
        'Zatwierdzone przez spedytora', 
        'Zaakceptowane przez klienta',
        'W trakcie',
        'Dostarczone'
    ];
    
    const statusMapping = {
        'Utworzone': 'pending',
        'Zatwierdzone przez spedytora': 'approved',
        'Zaakceptowane przez klienta': 'ready_for_delivery',
        'W trakcie': 'in_progress',
        'Pobrano ładunek': 'pickup_completed',
        'Dostarczone': 'delivered',
        'Anulowane': 'cancelled'
    };
    
    testStatuses.forEach(status => {
        const mapped = statusMapping[status];
        if (mapped) {
            console.log(`✅ Status "${status}" → "${mapped}"`);
        } else {
            console.log(`⚠️ Status "${status}" nie ma mapowania`);
        }
    });
    
    // 3. Test konfiguracji akcji dla różnych statusów
    console.log('3. Testowanie konfiguracji akcji...');
    const testOrder = {
        ID_order: 999,
        status: 'Zaakceptowane przez klienta',
        normalizedStatus: 'ready_for_delivery'
    };
    
    console.log('Testowy order:', testOrder);
    console.log('Sprawdzenie czy pokazuje przycisk "Rozpocznij dostawę" dla statusu 3');
      console.log('=== TEST ZAKOŃCZONY ===');
}

// Funkcja do przełączania na panel kierowcy
function switchToDriverPanel() {
    console.log('Przełączanie na panel kierowcy...');
    
    // Ustaw cookie użytkownika na kierowcę
    document.cookie = 'user_spedycja=Kierowca; path=/';
    
    // Przeładuj stronę - najprostrze rozwiązanie
    location.reload();
}

// Funkcja do otwierania prawdziwego panelu kierowcy
async function openDriverPanelModal() {
    console.log('🚚 Otwieranie panelu zarządzania dostawą...');
    
    try {
        // Użyj funkcji showModal z app.js
        if (typeof window.showModal === 'function') {
            await window.showModal('driverPanel.php');
            console.log('✅ Panel kierowcy otwarty jako modal');
            
            // Poczekaj chwilę na załadowanie
            setTimeout(() => {
                console.log('🔍 Sprawdzanie elementów w modalu...');
                const modal = document.getElementById('modalForm');
                const deliveryActions = document.getElementById('deliveryActions');
                console.log('Modal element:', modal);
                console.log('Delivery actions w modalu:', deliveryActions);
                
                if (deliveryActions) {
                    console.log('✅ Panel akcji dostępny w modalu!');
                } else {
                    console.log('❌ Panel akcji nie znaleziony w modalu');
                }
            }, 1000);
            
        } else {
            console.error('❌ Funkcja showModal nie jest dostępna');
        }
    } catch (error) {
        console.error('❌ Błąd podczas otwierania panelu:', error);
    }
}

// Funkcja do testowania konkretnego zlecenia
async function testOrderActions(orderId = 2) {
    console.log(`=== TESTOWANIE AKCJI DLA ZLECENIA #${orderId} ===`);
    
    try {
        // Pobierz dane zlecenia
        const response = await fetch('/api/queries/orders');
        const orders = await response.json();
        const order = orders.find(o => o.ID_order == orderId);
        
        if (!order) {
            console.error(`❌ Nie znaleziono zlecenia #${orderId}`);
            return;
        }
        
        console.log('📋 Dane zlecenia:', order);
        console.log('📊 Status:', order.status);
        
        // Sprawdź czy panel akcji istnieje
        const actionsContainer = document.getElementById('deliveryActions');
        if (!actionsContainer) {
            console.error('❌ Kontener akcji nie istnieje. Czy jesteś w panelu kierowcy?');
            return;
        }
        
        // Przetestuj setupDeliveryActions
        if (typeof window.setupDeliveryActions === 'function') {
            console.log('🔧 Konfigurowanie akcji dostawy...');
            
            // Dodaj normalized status
            const statusMapping = {
                'Utworzone': 'pending',
                'Zatwierdzone przez spedytora': 'approved',
                'Zaakceptowane przez klienta': 'ready_for_delivery',
                'W trakcie': 'in_progress',
                'Pobrano ładunek': 'pickup_completed',
                'Dostarczone': 'delivered',
                'Anulowane': 'cancelled'
            };
            
            order.normalizedStatus = statusMapping[order.status] || 'pending';
            console.log('📊 Normalized status:', order.normalizedStatus);
            
            window.setupDeliveryActions(order);
            
            // Sprawdź co zostało wygenerowane
            console.log('🔍 HTML akcji:');
            console.log(actionsContainer.innerHTML);
              // Sprawdź przyciski
            const buttons = actionsContainer.querySelectorAll('button[data-action]');
            console.log(`🔲 Znaleziono ${buttons.length} przycisków akcji:`);
            buttons.forEach(btn => {
                console.log(`  - ${btn.getAttribute('data-action')}: "${btn.textContent.trim()}"`);
            });
            
            // Sprawdź czy dla statusu "Zaakceptowane przez klienta" jest przycisk "Rozpocznij dostawę"
            if (order.status === 'Zaakceptowane przez klienta') {
                const startButton = actionsContainer.querySelector('button[data-action="start_delivery"]');
                if (startButton) {
                    console.log('✅ Przycisk "Rozpocznij dostawę" wygenerowany dla zlecenia #' + orderId);
                } else {
                    console.log('❌ Brak przycisku "Rozpocznij dostawę" dla zlecenia #' + orderId);
                }
            }
            
        } else {
            console.error('❌ Funkcja setupDeliveryActions nie jest dostępna');
        }
        
    } catch (error) {
        console.error('❌ Błąd podczas testowania:', error);
    }
}

// Ulepszona funkcja testowania ZL#2 z czekaniem na załadowanie
async function testOrderZL2WithWait(maxAttempts = 10) {
    console.log('🔧 Auto-wykonuję test ZL#2...');
    
    for (let attempt = 1; attempt <= maxAttempts; attempt++) {
        console.log(`📋 Próba ${attempt}/${maxAttempts} - sprawdzam czy panel jest gotowy...`);
        
        // Sprawdź czy modal i jego elementy są dostępne
        const modal = document.getElementById('modalForm');
        const actionsContainer = document.getElementById('deliveryActions');
        
        console.log(`🔍 Modal: ${modal ? '✅ znaleziony' : '❌ nie znaleziony'}`);
        console.log(`🔍 Actions container: ${actionsContainer ? '✅ znaleziony' : '❌ nie znaleziony'}`);
        
        if (modal && actionsContainer) {
            console.log('✅ Panel akcji znaleziony! Uruchamiam test...');
            await testOrderActions(2);
            return;
        }
        
        // Sprawdź czy funkcje są dostępne
        console.log(`🔍 setupDeliveryActions: ${typeof window.setupDeliveryActions}`);
        console.log(`🔍 updateDriverPanelData: ${typeof window.updateDriverPanelData}`);
        
        console.log(`⏳ Panel nie jest jeszcze gotowy (próba ${attempt}), czekam 500ms...`);
        await new Promise(resolve => setTimeout(resolve, 500));
    }
    
    console.error('❌ Nie udało się znaleźć panelu akcji po 10 próbach');
    console.log('🔍 Ostateczny stan elementów:');
    console.log('Modal:', document.getElementById('modalForm'));
    console.log('Actions container:', document.getElementById('deliveryActions'));
}

// Dodaj przyciski testowe do interface'u
function addTestButtons() {
    // Kontener dla przycisków
    const testContainer = document.createElement('div');
    testContainer.style.position = 'fixed';
    testContainer.style.top = '10px';
    testContainer.style.right = '10px';
    testContainer.style.zIndex = '9999';
    testContainer.style.display = 'flex';
    testContainer.style.flexDirection = 'column';
    testContainer.style.gap = '5px';
    
    // Przycisk głównego testu
    const testButton = document.createElement('button');
    testButton.textContent = 'TEST PANELU';
    testButton.className = 'btn btn-warning btn-sm';
    testButton.onclick = testDriverPanel;
    
    // Przycisk przełączania na kierowcę
    const switchButton = document.createElement('button');
    switchButton.textContent = 'KIEROWCA';
    switchButton.className = 'btn btn-info btn-sm';
    switchButton.onclick = switchToDriverPanel;
      // Przycisk testowania zlecenia
    const orderTestButton = document.createElement('button');
    orderTestButton.textContent = 'TEST ZL. #2';
    orderTestButton.className = 'btn btn-success btn-sm';
    orderTestButton.onclick = () => testOrderActions(2);
      // Przycisk otwierania panelu kierowcy
    const openPanelButton = document.createElement('button');
    openPanelButton.textContent = 'OTWÓRZ PANEL';
    openPanelButton.className = 'btn btn-primary btn-sm';
    openPanelButton.onclick = openDriverPanelModal;
    
    // Przycisk automatycznego wyboru ZL#2
    const selectZL2Button = document.createElement('button');
    selectZL2Button.textContent = 'WYBIERZ ZL#2';
    selectZL2Button.className = 'btn btn-warning btn-sm';
    selectZL2Button.onclick = selectOrderZL2;
      testContainer.appendChild(testButton);
    testContainer.appendChild(switchButton);
    testContainer.appendChild(orderTestButton);
    testContainer.appendChild(openPanelButton);
    testContainer.appendChild(selectZL2Button);
    document.body.appendChild(testContainer);
    
    console.log('🔧 Przyciski testowe dodane w prawym górnym rogu');
}

// Funkcja do automatycznego wyboru zlecenia #2 z selecta
async function selectOrderZL2() {
    console.log('🔧 Próbuje automatycznie wybrać zlecenie #2...');
    
    const orderSelect = document.getElementById('activeOrderSelect');
    if (!orderSelect) {
        console.error('❌ Select zleceń nie został znaleziony');
        return;
    }
    
    // Sprawdź czy zlecenie #2 jest dostępne w opcjach
    const option2 = orderSelect.querySelector('option[value="2"]');
    if (!option2) {
        console.error('❌ Zlecenie #2 nie jest dostępne w selectie');
        console.log('🔍 Dostępne opcje:', Array.from(orderSelect.options).map(opt => opt.value));
        return;
    }
    
    // Ustaw wartość selecta na zlecenie #2
    orderSelect.value = '2';
    
    // Ręcznie wywołaj event 'change'
    const event = new Event('change', { bubbles: true });
    orderSelect.dispatchEvent(event);
    
    console.log('✅ Zlecenie #2 wybrane automatycznie!');
}

// Eksport funkcji do window object dla dostępu z innych plików
window.testDriverPanel = testDriverPanel;
window.testOrderActions = testOrderActions;
window.testOrderZL2 = () => testOrderActions(2);
window.switchToDriverPanel = switchToDriverPanel;
window.openDriverPanelModal = openDriverPanelModal;
window.testOrderZL2WithWait = testOrderZL2WithWait;
window.selectOrderZL2 = selectOrderZL2;
window.selectOrderZL2 = selectOrderZL2;

console.log('🔧 Funkcje testowe wyeksportowane do window object');

// Uruchom po załadowaniu strony
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', addTestButtons);
} else {
    addTestButtons();
}
