import { Input, Autocomplete, Select, Modal, Ripple, Dropdown, Collapse, initMDB } from "./mdb/js/mdb.es.min.js";
initMDB({ Input, Autocomplete, Select, Modal, Ripple, Dropdown, Collapse });

'use strict';

const getCookie = (name) => {
    const match = document.cookie.match(new RegExp('(^| )' + name + '=([^;]+)'));
    return match ? decodeURIComponent(match[2]) : null;
};

const setCookie = (name, value, days = 7) => {
    const expires = new Date(Date.now() + days * 864e5).toUTCString();
    document.cookie = `${name}=${encodeURIComponent(value)}; expires=${expires}; path=/`;
};

const user = (() => {
    let u = getCookie('user_spedycja');
    if (!u) {
        u = "Klient";
        setCookie('user_spedycja', u);
    }
    return u;
})();

document.addEventListener('DOMContentLoaded', () => {
    loadOrders();

    document.getElementById('reload-orders').addEventListener('click', loadOrders);// Event listenery zastąpiły globalne funkcje onclick    // Dodawanie nowego zlecenia - tylko dla klientów
    const addForm = user !== 'Klient' ? document.getElementById('nav-addForm')?.parentElement?.remove() : document.getElementById('nav-addForm');
    addForm?.addEventListener("click", async () => {
        showModal("views/addForm.php").then(loadContent).then(() => {
            const autocompleteData = [];

            const asyncAutocomplete = document.querySelector('#async');
            const asyncFilter = async (query) => {
                const url = `/api/queries/companies?search=${encodeURI(query)}`;
                const response = await fetch(url);
                const data = await response.json();
                return data;
            };

            const dataFilter = (value) => {
                return autocompleteData.filter((item) => {
                    return item.toLowerCase().startsWith(value.toLowerCase());
                });
            };

            asyncFilter('').then(data => {
                autocompleteData.push(...data);
                new Autocomplete(asyncAutocomplete, {
                    filter: dataFilter,
                    container: "#modalForm"
                });
            });

            const form = document.querySelector("#form-new-order");
            form.onsubmit = async e => {
                e.preventDefault();
                const data = Object.fromEntries(new FormData(form));
                const res = await api('POST', '/api/commands/submit-order', data);
                log(res);
                if (res.error) {
                    return;
                }
                loadOrders();
                hideModal();
            };
        });
    });    // Panel kierowcy - tylko dla kierowców
    const driverPanel = document.getElementById('nav-driverPanel');
    if (user !== 'Kierowca') {
        driverPanel?.parentElement?.remove();
    } else {        driverPanel?.addEventListener("click", async () => {
            try {
                // Krok 1: Załaduj modal z driverPanel.php
                await showModal("views/driverPanel.php");
                
                // Krok 2: Załaduj funkcjonalność inputów (loadContent)
                await loadContent();
                
                // Krok 3: Po załadowaniu modala, zaktualizuj dane
                await updateDriverPanelData();
                  // Krok 4: Skonfiguruj event listenery
                setupDriverPanelEventListeners();
                
            } catch (error) {
                console.error('Błąd podczas ładowania panelu kierowcy:', error);
                alert('Błąd podczas ładowania panelu kierowcy: ' + error.message);
            }
        });
    }

    // Select usera
    const selectUser = document.getElementById('select-user');
    Select.getInstance(selectUser).setValue(user);
    selectUser.addEventListener('change', async () => {
        const userId = selectUser.value;
        const res = await api('POST', '/api/commands/set-user', { user: userId });
        log(res);
        if (res.error) {
            return;
        }
        window.location.reload();
    });
});

async function api(method, url, data) {
    try {
        const opts = { method, headers:{} };
        if (data) {
            opts.headers['Content-Type'] = 'application/json';
            opts.body = JSON.stringify(data);
        }
        
        const res = await fetch(url, opts);
        const text = await res.text();
        
        if (!res.ok) {
            console.error(`API Error ${res.status}: ${res.statusText}`);
            return { error: `HTTP ${res.status}: ${res.statusText}` };
        }
        
        try {
            return JSON.parse(text);
        }
        catch (e) {
            console.error('API response is not valid JSON:', text);
            return { error: 'Invalid JSON response', response: text };
        }
    } catch (networkError) {
        console.error('Network error:', networkError);
        return { error: 'Network error: ' + networkError.message };
    }
}

// Lista zleceń
async function loadOrders() {
    const orders = await api('GET','/api/queries/orders');
    
    if (!orders || orders.error) {
        console.error('Błąd podczas ładowania zleceń:', orders);
        return;
    }
    
    const body = document.getElementById('orders-body');
    body.innerHTML = '';
    orders.forEach(o => {
        const tr = document.createElement('tr');
        
        let actions = '';
        if (user === 'Spedytor' && o.status === 'Przypisano kierowcę') {
            actions = `<button class="btn btn-sm btn-info" data-action="plan-route" data-order-id="${o.ID_order}">Planuj trasę</button>`;
        }
        if (o.status !== 'Utworzone') {
            actions += ` <button class="btn btn-sm btn-secondary" data-action="track-order" data-order-id="${o.ID_order}">Śledź</button>`;
            actions += ` <button class="btn btn-sm btn-outline-primary" data-action="get-documents" data-order-id="${o.ID_order}">Dokumenty</button>`;
        }
        
        tr.innerHTML = `
            <td>${o.ID_order}</td>
            <td>${o.status}</td>
            <td>${o.company}</td>
            <td>${o.location_from}</td>
            <td>${o.location_to}</td>
            <td>${o.cargo}</td>
            <td>${o.weight}kg</td>
            <td>${o.driver ?? "-"}</td>
            <td>${o.created_at}</td>
            <td>${actions}</td>
        `;
        tr.addEventListener('click', (e) => { 
            if (!e.target.closest('button')) {
                detailsOrder(o);
            }
        });
        
        // Dodaj event listenery dla przycisków akcji
        tr.querySelectorAll('[data-action]').forEach(button => {
            const action = button.getAttribute('data-action');
            const orderId = button.getAttribute('data-order-id');
            
            button.addEventListener('click', async (e) => {
                e.stopPropagation(); // Zapobiega otwarciu szczegółów zamówienia
                
                switch (action) {
                    case 'plan-route':
                        await planRoute(orderId);
                        break;
                    case 'track-order':
                        await trackOrder(orderId);
                        break;
                    case 'get-documents':
                        await getDocuments(orderId);
                        break;
                    default:
                        console.warn(`Nieznana akcja w tabeli zleceń: ${action}`);
                }
            });
        });
        
        body.appendChild(tr);
    });
}

// Szczegóły zlecenia
async function detailsOrder(order) {
    try {
        await showModal(`views/orderDetails.php?order=${JSON.stringify(order)}`);
        await loadContent();
        
        // Automatycznie ładuj informacje o trasie po załadowaniu modala
        await loadRouteInfo(order.ID_order);
        
        // Dodaj event listenery dla przycisków w szczegółach zamówienia
        setupOrderDetailsEventListeners(order.ID_order);

        const select = document.getElementById('driverId');
        if (select) {
            new Select(select, {
                container: "#modalForm",
            });
        }

        const form = document.querySelector("#form-details");
        if (form) {
            form.onsubmit = async e => {
                e.preventDefault();
                let res;

                // Sprawdzenie statusu zlecenia i wykonanie odpowiedniej akcji
                if (order.status === 'Utworzone') {
                    const data = Object.fromEntries(new FormData(e.target));
                    console.log(data);
                    res = await api('POST', '/api/commands/validate-order', data);
                }
                else if (order.status === 'Zatwierdzone przez spedytora' && user === 'Spedytor') {
                    if (select.value === '') {
                        alert('Proszę wybrać kierowcę.');
                        return;
                    }                    const formData = Object.fromEntries(new FormData(e.target));
                    console.log('Dane formularza:', formData);
                    
                    // AssignDriverCommand oczekuje argumentów w kolejności: orderId, driverId
                    // Konwertuj na stringi, żeby upewnić się o typie danych
                    const data = [String(formData.orderId), String(formData.driverId)];
                    console.log('Dane wysyłane do assign-driver:', data);
                    res = await api('POST', '/api/commands/assign-driver', data);
                }
                else if (order.status === 'Zatwierdzone przez spedytora' && user === 'Klient') {
                    const data = Object.fromEntries(new FormData(e.target));
                    res = await api('POST', '/api/commands/accept-offer', data);                }

                log(res);
                if (res && res.error) {
                    alert('Błąd: ' + res.error);
                    return;
                }
                if (!res) {
                    alert('Błąd podczas przetwarzania żądania. Sprawdź konsolę.');
                    return;
                }
                loadOrders();
                hideModal();
            };
        }
    } catch (error) {
        console.error('Error loading order details:', error);
        alert('Błąd podczas ładowania szczegółów zlecenia: ' + error.message);
    }
}

// Planowanie trasy
async function planRoute(orderId) {
    const result = await api('POST', '/api/commands/plan-route', { orderId: orderId });
    if (result && !result.error) {
        alert(`Trasa zaplanowana pomyślnie!\n\nTrasa: ${result.route}\nSzacowany czas: ${result.estimatedTime}\nSzacowany koszt: ${result.estimatedCost} PLN`);
        // Odśwież informacje o trasie
        await loadRouteInfo(orderId);
    } else {
        alert('Błąd podczas planowania trasy: ' + (result.error || 'Nieznany błąd'));
    }
}

// Ładowanie informacji o trasie
async function loadRouteInfo(orderId) {
    const tracking = await api('GET', `/api/queries/tracking-info?orderId=${orderId}`);
    if (tracking && !tracking.error && tracking.route) {
        const routeInfoDiv = document.getElementById('routeInfo');
        if (routeInfoDiv) {
            routeInfoDiv.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <strong>Trasa:</strong><br>
                        ${tracking.route}
                    </div>
                    <div class="col-md-3">
                        <strong>Szacowany czas:</strong><br>
                        ${tracking.estimated_time}
                    </div>
                    <div class="col-md-3">
                        <strong>Szacowany koszt:</strong><br>
                        ${tracking.estimated_cost} PLN
                    </div>
                </div>
            `;
        }
    }
}

async function trackOrder(orderId) {
    const tracking = await api('GET', `/api/queries/tracking-info?orderId=${orderId}`);
    if (tracking && !tracking.error) {
        let info = `Zlecenie #${tracking.ID_order}\n`;
        info += `Status: ${tracking.status}\n`;
        info += `Kierowca: ${tracking.name_driver || 'Nie przypisano'}\n`;
        if (tracking.latitude && tracking.longitude) {
            info += `Ostatnia lokalizacja: ${tracking.latitude}, ${tracking.longitude}\n`;
            info += `Aktualizacja: ${tracking.last_location_update}\n`;
        }
        if (tracking.route) {
            info += `Trasa: ${tracking.route}\n`;
            info += `Szacowany czas: ${tracking.estimated_time}\n`;
        }
        alert(info);
    }
}

async function getDocuments(orderId) {
    const docs = await api('GET', `/api/queries/documents?orderId=${orderId}`);
    if (docs && docs.length > 0) {
        let docList = `Dokumenty dla zlecenia #${orderId}:\n\n`;
        docs.forEach(doc => {
            docList += `${doc.document_type}: ${doc.document_path}\n`;
            docList += `Wygenerowano: ${doc.generated_at}\n\n`;
        });
        alert(docList);
    } else {
        alert('Brak dokumentów dla tego zlecenia');
    }
}

// Funkcje pomocnicze
async function showModal(url, id = 'modalForm') {
    const modal = document.getElementById(id);
    if (!modal) {
        console.error(`Modal element with id '${id}' not found`);
        return;
    }

    try {
        // Zamknij istniejącą instancję modala jeśli istnieje
        const existingInstance = Modal.getInstance(modal);
        if (existingInstance) {
            existingInstance.dispose();
        }

        modal.innerHTML = '';
        const res = await fetch(url);
        if (!res.ok) {
            throw new Error(`HTTP error! status: ${res.status}`);
        }
        
        const html = await res.text();
        modal.innerHTML = html;
        
        // Poczekaj chwilę na renderowanie DOM
        await new Promise(resolve => setTimeout(resolve, 10));
        
        const instance = new Modal(modal, {
            backdrop: 'static',
            keyboard: false
        });
        instance.show();
        
        return instance;
    } catch (error) {
        console.error('Error in showModal:', error);
        alert('Błąd podczas ładowania okna dialogowego: ' + error.message);
    }
}

async function hideModal(id = 'modalForm') {
    const modal = document.getElementById(id);
    if (!modal) {
        console.error(`Modal element with id '${id}' not found`);
        return;
    }

    try {
        const instance = Modal.getInstance(modal);
        if (instance) {
            instance.hide();
            setTimeout(() => {
                try {
                    instance.dispose();
                } catch (error) {
                    console.warn('Error disposing modal instance:', error);
                }
            }, 500);
        }
    } catch (error) {
        console.error('Error in hideModal:', error);
    }
}

async function log(message) {
    console.log("Wynik operacji: ", message);
}

async function loadContent() {
    document.querySelectorAll(".form-outline:not([data-mdb-input-initialized='true'])").forEach(input => {
        new Input(input);
    });
}

// Funkcje panelu kierowcy
async function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                document.getElementById('latitude').value = position.coords.latitude.toFixed(6);
                document.getElementById('longitude').value = position.coords.longitude.toFixed(6);
                document.getElementById('latitude').classList.add('active');
                document.getElementById('longitude').classList.add('active');
                alert('Lokalizacja pobrana pomyślnie!');
            },
            function(error) {
                alert('Błąd podczas pobierania lokalizacji: ' + error.message);
            }
        );
    }
    else {
        alert('Geolokalizacja nie jest obsługiwana przez tę przeglądarkę.');
    }
}

async function updateLocation() {
    const latitude = document.getElementById('latitude').value;
    const longitude = document.getElementById('longitude').value;
    const orderSelect = document.getElementById('activeOrderSelect');
    const orderId = orderSelect.value;

    if (!latitude || !longitude) {
        alert('Proszę podać współrzędne geograficzne.');
        return;
    }

    if (!orderId) {
        alert('Proszę wybrać zlecenie.');
        return;
    }

    const result = await api('POST', '/api/commands/update-location', {
        orderId: orderId,
        driverId: 1, // To powinno być dynamiczne w rzeczywistej aplikacji
        latitude: parseFloat(latitude),
        longitude: parseFloat(longitude)
    });

    if (result && !result.error) {
        alert('Lokalizacja zaktualizowana pomyślnie!');
        // Wyczyść pola
        document.getElementById('latitude').value = '';
        document.getElementById('longitude').value = '';
    }
    else {
        alert('Błąd podczas aktualizacji lokalizacji: ' + (result.error || 'Nieznany błąd'));
    }
}

async function loadDriverOrders() {
    const orders = await api('GET', '/api/queries/orders');
    const activeOrderSelect = document.getElementById('activeOrderSelect');
    const driverOrdersList = document.getElementById('driverOrdersList');
    
    if (activeOrderSelect && orders) {
        // Wyczyść obecne opcje
        activeOrderSelect.innerHTML = '<option value="">Wybierz aktywne zlecenie</option>';
        
        // Filtruj zlecenia przypisane do kierowców
        const driverOrders = orders.filter(order => order.driver && order.status !== 'Utworzone');
        
        driverOrders.forEach(order => {
            const option = document.createElement('option');
            option.value = order.ID_order;
            option.textContent = `#${order.ID_order} - ${order.location_from} → ${order.location_to}`;
            activeOrderSelect.appendChild(option);
        });
        
        // Zaktualizuj statystyki
        updateDriverStats(driverOrders);
    }
    
    // Zaktualizuj listę zleceń kierowcy
    if (driverOrdersList && orders) {
        driverOrdersList.innerHTML = '';
        const driverOrders = orders.filter(order => order.driver && order.status !== 'Utworzone');
        
        if (driverOrders.length === 0) {
            driverOrdersList.innerHTML = '<div class="text-center p-3 text-muted">Brak przypisanych zleceń</div>';
        } else {
            driverOrders.forEach(order => {
                const statusClass = getStatusClass(order.status);
                const orderItem = document.createElement('div');
                orderItem.className = `list-group-item d-flex justify-content-between align-items-center`;
                orderItem.innerHTML = `
                    <div>
                        <h6 class="mb-1">Zlecenie #${order.ID_order}</h6>
                        <p class="mb-1 text-muted">${order.location_from} → ${order.location_to}</p>
                        <small class="text-muted">${order.cargo} (${order.weight}kg)</small>
                    </div>
                    <span class="badge ${statusClass}">${order.status}</span>
                `;
                driverOrdersList.appendChild(orderItem);
            });
        }
    }
}

function getStatusClass(status) {
    switch (status) {
        case 'Zaakceptowane przez klienta':
            return 'bg-success';
        case 'Przypisano kierowcę':
            return 'bg-primary';
        case 'W trakcie dostawy':
            return 'bg-warning';
        case 'Dostarczone':
            return 'bg-info';
        default:
            return 'bg-secondary';
    }
}

function updateDriverStats(orders) {
    const activeOrdersCount = document.getElementById('activeOrdersCount');
    const completedOrdersCount = document.getElementById('completedOrdersCount');
    const totalDistanceKm = document.getElementById('totalDistanceKm');
    
    if (activeOrdersCount) {
        const activeCount = orders.filter(o => o.status !== 'Dostarczone').length;
        activeOrdersCount.textContent = activeCount;
    }
    
    if (completedOrdersCount) {
        const completedCount = orders.filter(o => o.status === 'Dostarczone').length;
        completedOrdersCount.textContent = completedCount;
    }
    
    if (totalDistanceKm) {
        // Symulacja dystansu - w rzeczywistej aplikacji byłoby to obliczane na podstawie tras
        const estimatedDistance = orders.length * 150; // 150km na zlecenie
        totalDistanceKm.textContent = estimatedDistance;
    }
}

async function startDelivery() {
    const orderSelect = document.getElementById('activeOrderSelect');
    const orderId = orderSelect.value;

    if (!orderId) {
        alert('Proszę wybrać zlecenie.');
        return;
    }

    const result = await api('POST', '/api/commands/start-delivery', {
        orderId: orderId,
        driverId: 1 // To powinno być dynamiczne w rzeczywistej aplikacji
    });

    if (result && !result.error) {
        alert('Dostawa rozpoczęta pomyślnie!');
        loadDriverOrders(); // Odśwież listę zleceń
    } else {
        alert('Błąd podczas rozpoczynania dostawy: ' + (result.error || 'Nieznany błąd'));
    }
}

async function completeDelivery() {
    const orderSelect = document.getElementById('activeOrderSelect');
    const orderId = orderSelect.value;
    const signature = document.getElementById('signature').value;

    if (!orderId) {
        alert('Proszę wybrać zlecenie.');
        return;
    }

    if (!signature) {
        alert('Proszę podać podpis lub potwierdzenie odbioru.');
        return;
    }

    const result = await api('POST', '/api/commands/complete-delivery', {
        orderId: orderId,
        driverId: 1, // To powinno być dynamiczne w rzeczywistej aplikacji
        signature: signature
    });

    if (result && !result.error) {
        alert('Dostawa zakończona pomyślnie!');
        document.getElementById('signature').value = ''; // Wyczyść pole
        loadDriverOrders(); // Odśwież listę zleceń
    } else {
        alert('Błąd podczas kończenia dostawy: ' + (result.error || 'Nieznany błąd'));
    }
}

async function generateDocument(orderId) {
    // Zapytaj użytkownika o typ dokumentu
    const documentType = prompt('Podaj typ dokumentu do wygenerowania (np. "faktura", "list przewozowy", "potwierdzenie"):');
    
    if (!documentType) {
        return;
    }

    const result = await api('POST', '/api/commands/generate-document', {
        orderId: orderId,
        documentType: documentType
    });

    if (result && !result.error) {
        alert(`Dokument wygenerowany pomyślnie!\n\nTyp: ${result.documentType}\nŚcieżka: ${result.documentPath}`);
    }
    else {
        alert('Błąd podczas generowania dokumentu: ' + (result.error || 'Nieznany błąd'));
    }
}

async function showOrderHistory(orderId) {
    const history = await api('GET', `/api/queries/order-history?orderId=${orderId}`);
    if (history && history.length > 0) {
        let historyText = `Historia zlecenia #${orderId}:\n\n`;
        
        history.forEach((event, index) => {
            historyText += `${index + 1}. ${event.type}\n`;
            historyText += `   Data: ${event.timestamp}\n`;
            
            // Formatowanie szczegółów wydarzenia
            if (event.details) {
                switch (event.type) {
                    case 'OrderSubmitted':
                        historyText += `   Firma: ${event.details.company}\n`;
                        historyText += `   Trasa: ${event.details.pickup} → ${event.details.delivery}\n`;
                        historyText += `   Ładunek: ${event.details.cargo} (${event.details.weight}kg)\n`;
                        break;
                    case 'OrderValidated':
                        historyText += `   Cena: ${event.details.price} PLN\n`;
                        break;
                    case 'DriverAssigned':
                        historyText += `   ID Kierowcy: ${event.details.driverId}\n`;
                        break;
                    case 'RoutePlanned':
                        historyText += `   Trasa: ${event.details.route}\n`;
                        historyText += `   Szacowany czas: ${event.details.estimatedTime}\n`;
                        historyText += `   Szacowany koszt: ${event.details.estimatedCost} PLN\n`;
                        break;
                    case 'LocationUpdated':
                        historyText += `   Lokalizacja: ${event.details.latitude}, ${event.details.longitude}\n`;
                        break;
                    case 'DocumentGenerated':
                        historyText += `   Typ dokumentu: ${event.details.documentType}\n`;
                        historyText += `   Ścieżka: ${event.details.documentPath}\n`;
                        break;
                }
            }
            historyText += '\n';
        });
        
        alert(historyText);
    } else {
        alert('Brak historii dla tego zlecenia');
    }
}

async function refreshDriverData() {
    await loadDriverOrders();
    alert('Dane kierowcy odświeżone pomyślnie!');
}

async function showTodayDeliveries() {
    const orders = await api('GET', '/api/queries/orders');
    const today = new Date().toISOString().split('T')[0];
    
    if (orders) {
        const todayOrders = orders.filter(order => {
            return order.created_at && order.created_at.includes(today);
        });
        
        if (todayOrders.length > 0) {
            let deliveriesText = `Dzisiejsze dostawy (${todayOrders.length}):\n\n`;
            todayOrders.forEach((order, index) => {
                deliveriesText += `${index + 1}. Zlecenie #${order.ID_order}\n`;
                deliveriesText += `   Trasa: ${order.location_from} → ${order.location_to}\n`;
                deliveriesText += `   Status: ${order.status}\n`;
                deliveriesText += `   Ładunek: ${order.cargo} (${order.weight}kg)\n\n`;
            });
            alert(deliveriesText);
        } else {
            alert('Brak dostaw na dzisiaj');
        }
    }
}

// Prosta funkcja aktualizacji danych panelu kierowcy
async function updateDriverPanelData() {
    const driverName = 'Janusz Mikke'; // Hard-coded kierowca dla demonstracji
    
    try {
        // Pobierz dane zleceń
        const ordersResponse = await api('GET', '/api/queries/orders');
        
        if (!ordersResponse || ordersResponse.error) {
            throw new Error(ordersResponse?.error || 'Błąd podczas ładowania danych kierowcy');
        }
        
        // Filtruj zlecenia dla kierowcy
        const driverOrders = ordersResponse.filter(order => order.driver === driverName);
          // Mapowanie statusów - zaktualizowane dla poprawnego przepływu
        const statusMapping = {
            'Utworzone': 'pending',
            'Zatwierdzone przez spedytora': 'approved', 
            'Zaakceptowane przez klienta': 'ready_for_delivery', // ID_status = 3 - gotowe do dostawy
            'W trakcie': 'in_progress',                          // ID_status = 4 - w trakcie dostawy  
            'Pobrano ładunek': 'pickup_completed',
            'Dostarczone': 'delivered',                          // ID_status = 5 - dostarczone
            'Anulowane': 'cancelled'
        };
        
        // Dodaj znormalizowane statusy
        driverOrders.forEach(order => {
            order.normalizedStatus = statusMapping[order.status] || 'pending';
        });
          // Kategoryzuj zlecenia
        const totalOrders = driverOrders.length;
        const activeOrders = driverOrders.filter(order => 
            ['ready_for_delivery', 'in_progress', 'pickup_completed'].includes(order.normalizedStatus)
        );
        const completedOrders = driverOrders.filter(order => 
            order.normalizedStatus === 'delivered'
        );
        
        // Szacunkowa odległość
        const totalDistance = completedOrders.length * 100; // 100km na zlecenie
        
        // Aktualizuj statystyki
        updateDriverStatistics(totalOrders, activeOrders.length, completedOrders.length, totalDistance);
        
        // Wypełnij listy
        populateActiveOrdersSelect(activeOrders);
        populateAllOrdersList(driverOrders);
        
        // Skonfiguruj wybór aktywnego zlecenia
        setupActiveOrderSelection();
        
    } catch (error) {
        console.error('Błąd podczas aktualizacji danych kierowcy:', error);
        throw error;
    }
}

// Aktualizacja statystyk kierowcy
function updateDriverStatistics(total, active, completed, distance) {
    const elements = {
        totalOrdersCount: document.getElementById('totalOrdersCount'),
        activeOrdersCount: document.getElementById('activeOrdersCount'), 
        completedOrdersCount: document.getElementById('completedOrdersCount'),
        totalDistanceKm: document.getElementById('totalDistanceKm')
    };
    
    // Sprawdź czy wszystkie elementy istnieją
    for (const [id, element] of Object.entries(elements)) {
        if (!element) {
            console.error(`Element z ID '${id}' nie został znaleziony w DOM`);
            return;
        }
    }
    
    // Aktualizuj wartości
    elements.totalOrdersCount.textContent = total;
    elements.activeOrdersCount.textContent = active;
    elements.completedOrdersCount.textContent = completed;
    elements.totalDistanceKm.textContent = distance;
}

// Wypełnianie dropdown z aktywnymi zleceniami
function populateActiveOrdersSelect(activeOrders) {
    const select = document.getElementById('activeOrderSelect');
    
    if (!select) {
        console.error('Element activeOrderSelect nie został znaleziony w DOM');
        return;
    }
    
    // Wyczyść opcje
    select.innerHTML = '<option value="" selected>Wybierz zlecenie do zarządzania</option>';
    
    activeOrders.forEach(order => {
        const option = document.createElement('option');
        option.value = order.ID_order;
        option.textContent = `#${order.ID_order} - ${order.location_from} → ${order.location_to} (${order.status})`;
        select.appendChild(option);
    });
    
    // Jeśli brak aktywnych zleceń
    if (activeOrders.length === 0) {
        const option = document.createElement('option');
        option.value = '';
        option.textContent = 'Brak aktywnych zleceń';
        option.disabled = true;
        select.appendChild(option);
    }
}

// Konfiguracja wyboru aktywnego zlecenia z progresywnym UI
function setupActiveOrderSelection() {
    const select = document.getElementById('activeOrderSelect');
    const managementPanel = document.getElementById('orderManagementPanel');
    const completionPanel = document.getElementById('completionPanel');
    
    if (!select) {
        console.error('Element activeOrderSelect nie został znaleziony w DOM');
        return;
    }
    
    if (!managementPanel) {
        console.error('Element orderManagementPanel nie został znaleziony w DOM');
        return;
    }
    
    if (!completionPanel) {
        console.error('Element completionPanel nie został znaleziony w DOM');
        return;
    }
    
    select.addEventListener('change', async function() {
        const selectedOrderId = this.value;
        
        if (!selectedOrderId) {
            // Ukryj panel zarządzania jeśli nic nie wybrano
            managementPanel.style.display = 'none';
            completionPanel.style.display = 'none';
            return;
        }
        
        try {
            // Pobierz szczegóły wybranego zlecenia
            const orders = await api('GET', '/api/queries/orders');
            const selectedOrder = orders.find(order => order.ID_order == selectedOrderId);
            
            if (!selectedOrder) {
                throw new Error('Nie znaleziono wybranego zlecenia');
            }
            
            // Pokaż panel zarządzania
            managementPanel.style.display = 'block';
            
            // Wypełnij szczegóły zlecenia
            populateSelectedOrderDetails(selectedOrder);
            
            // Skonfiguruj akcje dostawy na podstawie statusu
            setupDeliveryActions(selectedOrder);
            
            // Pokaż/ukryj panel zakończenia w zależności od statusu
            if (selectedOrder.status === 'pickup_completed') {
                completionPanel.style.display = 'block';
            } else {
                completionPanel.style.display = 'none';
            }
            
        } catch (error) {
            console.error('Błąd podczas ładowania szczegółów zlecenia:', error);
            alert('Błąd podczas ładowania szczegółów zlecenia: ' + error.message);
        }
    });
}

// Wypełnianie szczegółów wybranego zlecenia
function populateSelectedOrderDetails(order) {
    const detailsContainer = document.getElementById('selectedOrderDetails');
    
    const statusBadgeClass = getStatusBadgeClass(order.status);
    const statusText = getStatusText(order.status);
    
    detailsContainer.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-hashtag me-1"></i>Zlecenie #${order.ID_order}</h6>
                <p class="mb-2"><strong>Status:</strong> <span class="badge ${statusBadgeClass}">${statusText}</span></p>
                <p class="mb-2"><strong>Firma:</strong> ${order.company}</p>
                <p class="mb-2"><strong>Ładunek:</strong> ${order.cargo}</p>
                <p class="mb-0"><strong>Waga:</strong> ${order.weight} kg</p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-route me-1"></i>Trasa</h6>
                <p class="mb-2"><strong>Odbiór:</strong> ${order.location_from}</p>
                <p class="mb-2"><strong>Dostawa:</strong> ${order.location_to}</p>
                <p class="mb-2"><strong>Data utworzenia:</strong> ${formatDate(order.created_at)}</p>
                <p class="mb-0"><strong>Cena:</strong> ${order.price ? order.price + ' PLN' : 'Nie ustalono'}</p>
            </div>
        </div>
    `;
}

// Konfiguracja akcji dostawy na podstawie statusu zlecenia
function setupDeliveryActions(order) {
    const actionsContainer = document.getElementById('deliveryActions');
    
    let actionsHTML = '';
    const normalizedStatus = order.normalizedStatus || 'pending';
      switch (normalizedStatus) {
        case 'pending':
            actionsHTML = `
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-1"></i>
                    Zlecenie oczekuje na zatwierdzenie
                </div>
            `;
            break;
            
        case 'approved':
            actionsHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-hourglass-half me-1"></i>
                    Oczekuje na akceptację klienta
                </div>
            `;
            break;
            
        case 'ready_for_delivery':  // ID_status = 3: Zaakceptowane przez klienta
            actionsHTML = `
                <button type="button" class="btn btn-success w-100 mb-2" data-action="start-delivery" data-order-id="${order.ID_order}">
                    <i class="fas fa-play me-1"></i>Rozpocznij dostawę
                </button>
                <button type="button" class="btn btn-outline-primary w-100" data-action="plan-route" data-order-id="${order.ID_order}">
                    <i class="fas fa-route me-1"></i>Zaplanuj trasę
                </button>
                <div class="alert alert-success mb-2">
                    <i class="fas fa-check-circle me-1"></i>
                    Zlecenie zaakceptowane przez klienta. Gotowe do dostawy!
                </div>
            `;
            break;
            
        case 'in_progress':         // ID_status = 4: W trakcie dostawy
            actionsHTML = `
                <button type="button" class="btn btn-primary w-100 mb-2" data-action="complete-delivery" data-order-id="${order.ID_order}">
                    <i class="fas fa-flag-checkered me-1"></i>Zakończ dostawę
                </button>
                <button type="button" class="btn btn-outline-info w-100" data-action="update-status" data-order-id="${order.ID_order}">
                    <i class="fas fa-info-circle me-1"></i>Aktualizuj status
                </button>
                <div class="alert alert-info mb-2">
                    <i class="fas fa-truck me-1"></i>
                    Dostawa w trakcie. Po dostarczeniu kliknij "Zakończ dostawę".
                </div>
            `;
            break;
            
        case 'pickup_completed':
            actionsHTML = `
                <button type="button" class="btn btn-primary w-100 mb-2" data-action="complete-delivery" data-order-id="${order.ID_order}">
                    <i class="fas fa-flag-checkered me-1"></i>Zakończ dostawę
                </button>
                <div class="alert alert-info mb-2">
                    <i class="fas fa-info-circle me-1"></i>
                    Ładunek pobrany. Wprowadź podpis odbiorcy i zakończ dostawę.
                </div>
            `;
            break;
            
        case 'delivered':
            actionsHTML = `
                <div class="alert alert-success">
                    <i class="fas fa-check-circle me-1"></i>
                    Dostawa zakończona pomyślnie!
                </div>
                <button type="button" class="btn btn-outline-secondary w-100" data-action="generate-document" data-order-id="${order.ID_order}">
                    <i class="fas fa-file-pdf me-1"></i>Generuj dokumenty
                </button>
            `;
            break;
            
        default:
            actionsHTML = `
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-1"></i>
                    Nieznany status zlecenia: ${order.status} (${normalizedStatus})
                </div>
            `;
    }
    
    actionsContainer.innerHTML = actionsHTML;
    
    // Dodaj event listenery dla nowych przycisków
    setupDeliveryActionListeners();
}

// Event listenery dla akcji dostawy
function setupDeliveryActionListeners() {
    const actionButtons = document.querySelectorAll('#deliveryActions [data-action]');
    
    actionButtons.forEach(button => {
        // Usuń poprzednie listenery aby uniknąć duplikowania
        button.replaceWith(button.cloneNode(true));
    });
    
    // Dodaj nowe listenery
    document.querySelectorAll('#deliveryActions [data-action]').forEach(button => {
        const action = button.getAttribute('data-action');
        const orderId = button.getAttribute('data-order-id');
        
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            switch (action) {
                case 'start-delivery':
                    await handleStartDelivery(orderId);
                    break;
                case 'confirm-pickup':
                    await handleConfirmPickup(orderId);
                    break;
                case 'complete-delivery':
                    await handleCompleteDelivery(orderId);
                    break;
                case 'plan-route':
                    await planRoute(orderId);
                    break;
                case 'update-status':
                    await handleUpdateStatus(orderId);
                    break;
                case 'generate-document':
                    await generateDocument(orderId);
                    break;
                default:
                    console.warn(`Nieznana akcja dostawy: ${action}`);
            }
        });
    });
}

// Wypełnianie listy wszystkich zleceń kierowcy
function populateAllOrdersList(orders) {
    const container = document.getElementById('allOrdersList');
    
    if (!container) {
        console.error('Element allOrdersList nie został znaleziony w DOM');
        return;
    }
    
    if (orders.length === 0) {
        container.innerHTML = `
            <div class="text-center p-3 text-muted">
                <i class="fas fa-inbox me-2"></i>Brak przypisanych zleceń
            </div>
        `;
        return;
    }
    
    let listHTML = '<div class="table-responsive"><table class="table table-hover"><thead><tr>';
    listHTML += '<th>ID</th><th>Trasa</th><th>Ładunek</th><th>Status</th><th>Data</th><th>Akcje</th>';
    listHTML += '</tr></thead><tbody>';
    
    orders.forEach(order => {
        const statusBadgeClass = getStatusBadgeClass(order.status);
        const statusText = getStatusText(order.status);
        
        listHTML += `
            <tr>
                <td><strong>#${order.ID_order}</strong></td>
                <td>
                    <small class="text-muted">Od:</small> ${order.location_from}<br>
                    <small class="text-muted">Do:</small> ${order.location_to}
                </td>
                <td>${order.cargo}<br><small class="text-muted">${order.weight} kg</small></td>
                <td><span class="badge ${statusBadgeClass}">${statusText}</span></td>
                <td>${formatDate(order.created_at)}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-outline-primary" data-action="show-order-details" data-order-id="${order.ID_order}">
                        <i class="fas fa-eye"></i>
                    </button>
                </td>
            </tr>
        `;
    });
    
    listHTML += '</tbody></table></div>';
    container.innerHTML = listHTML;
    
    // Dodaj event listenery dla przycisków szczegółów
    setupOrderListEventListeners();
}

// Event listenery dla listy zleceń
function setupOrderListEventListeners() {
    document.querySelectorAll('[data-action="show-order-details"]').forEach(button => {
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            const orderId = button.getAttribute('data-order-id');
            await showOrderDetails(orderId);
        });
    });
}

// Funkcje pomocnicze do statusów
function getStatusBadgeClass(status) {
    // Mapowanie polskich statusów na klasy Bootstrap
    const statusClasses = {
        'Utworzone': 'bg-warning text-dark',
        'Zatwierdzone przez spedytora': 'bg-info',
        'Zaakceptowane przez klienta': 'bg-info', 
        'W trakcie': 'bg-primary',
        'Pobrano ładunek': 'bg-warning text-dark',
        'Dostarczone': 'bg-success',
        'Anulowane': 'bg-danger'
    };
    return statusClasses[status] || 'bg-secondary';
}

function getStatusText(status) {
    // Zwracamy oryginalny polski status
    return status || 'Nieznany';
}

function formatDate(dateString) {
    if (!dateString) return 'Nie podano';
    const date = new Date(dateString);
    return date.toLocaleDateString('pl-PL') + ' ' + date.toLocaleTimeString('pl-PL', { hour: '2-digit', minute: '2-digit' });
}

// Nowe funkcje obsługi akcji dostawy
async function handleStartDelivery(orderId) {
    const confirmed = confirm('Czy na pewno chcesz rozpocząć dostawę?');
    if (!confirmed) return;
    
    try {
        const result = await api('POST', '/api/commands/update-order-status', {
            orderId: parseInt(orderId),
            status: 'in_progress',
            driverId: 1
        });
          if (result && !result.error) {
            alert('Dostawa rozpoczęta pomyślnie!');
            await updateDriverPanelData(); // Odśwież dane
        } else {
            throw new Error(result?.error || 'Nieznany błąd');
        }
    } catch (error) {
        console.error('Błąd podczas rozpoczynania dostawy:', error);
        alert('Błąd podczas rozpoczynania dostawy: ' + error.message);
    }
}

async function handleConfirmPickup(orderId) {
    const confirmed = confirm('Czy na pewno chcesz potwierdzić odbiór ładunku?');
    if (!confirmed) return;
    
    try {
        const result = await api('POST', '/api/commands/update-order-status', {
            orderId: parseInt(orderId),
            status: 'pickup_completed',
            driverId: 1
        });
          if (result && !result.error) {
            alert('Odbiór ładunku potwierdzony!');
            await updateDriverPanelData(); // Odśwież dane
        } else {
            throw new Error(result?.error || 'Nieznany błąd');
        }
    } catch (error) {
        console.error('Błąd podczas potwierdzania odbioru:', error);
        alert('Błąd podczas potwierdzania odbioru: ' + error.message);
    }
}

async function handleCompleteDelivery(orderId) {
    const signature = document.getElementById('signature').value.trim();
    
    if (!signature) {
        alert('Proszę wprowadzić podpis/potwierdzenie odbioru przed zakończeniem dostawy.');
        document.getElementById('signature').focus();
        return;
    }
    
    const confirmed = confirm(`Czy na pewno chcesz zakończyć dostawę?\nPodpis: ${signature}`);
    if (!confirmed) return;
    
    try {
        const result = await api('POST', '/api/commands/complete-delivery', {
            orderId: orderId,
            signature: signature,
            driverId: 1
        });
          if (result && !result.error) {
            alert('Dostawa zakończona pomyślnie!');
            document.getElementById('signature').value = ''; // Wyczyść pole
            await updateDriverPanelData(); // Odśwież dane
        } else {
            throw new Error(result?.error || 'Nieznany błąd');
        }
    } catch (error) {
        console.error('Błąd podczas kończenia dostawy:', error);
        alert('Błąd podczas kończenia dostawy: ' + error.message);
    }
}

async function handleUpdateStatus(orderId) {
    const newStatus = prompt('Wprowadź nowy status (in_progress, pickup_completed, delivered):');
    if (!newStatus) return;
    
    try {
        const result = await api('POST', '/api/commands/update-order-status', {
            orderId: parseInt(orderId),
            status: newStatus,
            driverId: 1
        });
          if (result && !result.error) {
            alert('Status zaktualizowany pomyślnie!');
            await updateDriverPanelData(); // Odśwież dane
        } else {
            throw new Error(result?.error || 'Nieznany błąd');
        }
    } catch (error) {
        console.error('Błąd podczas aktualizacji statusu:', error);
        alert('Błąd podczas aktualizacji statusu: ' + error.message);
    }
}

// Funkcje konfiguracji event listenerów
function setupDriverPanelEventListeners() {
    // Event listener dla selecta zleceń - uruchamiaj test ZL#2 po wyborze
    const orderSelect = document.getElementById('activeOrderSelect');
    if (orderSelect) {
        orderSelect.addEventListener('change', async function() {
            const selectedOrderId = this.value;
            
            if (selectedOrderId) {
                console.log(`🔧 Wybrano zlecenie #${selectedOrderId}, uruchamiam test...`);
                
                // Sprawdź czy to zlecenie #2 - jeśli tak, uruchom automatyczny test
                if (selectedOrderId === '2' && typeof window.testOrderZL2WithWait === 'function') {
                    setTimeout(() => {
                        console.log('🔧 Auto-wykonuję test ZL#2 po wyborze z selecta...');
                        window.testOrderZL2WithWait();
                    }, 500); // Krótkie opóźnienie aby UI się zaktualizował
                }
            }
        });
    }
    
    // Event listenery dla przycisków lokalizacji (zawsze widoczne)
    const getLocationBtn = document.querySelector('[data-action="get-location"]');
    const updateLocationBtn = document.querySelector('[data-action="update-location"]');
    
    if (getLocationBtn) {
        getLocationBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            await getCurrentLocation();
        });
    }
    
    if (updateLocationBtn) {
        updateLocationBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            await updateLocation();
        });
    }
}

function setupOrderDetailsEventListeners(orderId) {
    // Znajdź wszystkie przyciski z data-action w szczegółach zamówienia
    const buttons = document.querySelectorAll('[data-action]');
    
    buttons.forEach(button => {
        const action = button.getAttribute('data-action');
        const buttonOrderId = button.getAttribute('data-order-id') || orderId;
        
        button.addEventListener('click', async (e) => {
            e.preventDefault();
            
            switch (action) {
                case 'plan-route':
                    await planRoute(buttonOrderId);
                    break;
                case 'generate-document':
                    await generateDocument(buttonOrderId);
                    break;
                default:
                    console.warn(`Nieznana akcja w szczegółach zamówienia: ${action}`);
            }
        });
    });
}

// Eksport funkcji do testowania (udostępnienie w obiekcie window)
window.updateDriverPanelData = updateDriverPanelData;
window.setupDeliveryActions = setupDeliveryActions;
window.handleStartDelivery = handleStartDelivery;
window.handleCompleteDelivery = handleCompleteDelivery;
window.showModal = showModal;
window.api = api;
